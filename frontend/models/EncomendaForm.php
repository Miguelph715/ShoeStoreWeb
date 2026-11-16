<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\Encomenda;
use common\models\LinhaEncomenda;
use common\models\MetodoPagamento;
use frontend\components\Cart;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * O formulario de "Finalizar encomenda": email, morada de envio e metodo de pagamento.
 * criar() grava a encomenda, as linhas e desconta o stock, tudo numa transacao.
 */
class EncomendaForm extends Model
{
    public string $email = '';
    public string $morada_envio = '';
    public $metodo_pagamento_id;

    //o texto do erro quando o stock nao chega (preenchido por criar())
    public string $erro = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'morada_envio'], 'trim'],
            [['email', 'morada_envio', 'metodo_pagamento_id'], 'required'],
            ['email', 'email'],
            [['email', 'morada_envio'], 'string', 'max' => 255],
            ['metodo_pagamento_id', 'in', 'range' => array_keys(self::metodos()), 'message' => 'Escolhe um método de pagamento.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'email' => 'Email',
            'morada_envio' => 'Morada de envio',
            'metodo_pagamento_id' => 'Método de pagamento',
        ];
    }

    /**
     * Os metodos de pagamento ativos: id => nome.
     */
    public static function metodos(): array
    {
        return ArrayHelper::map(MetodoPagamento::find()->where(['ativo' => 1])->orderBy('id')->asArray()->all(), 'id', 'nome');
    }

    /**
     * Cria a encomenda a partir do carrinho. Devolve a encomenda, ou null se algo falhar
     * (nesse caso, $this->erro tem o motivo e nada fica gravado).
     */
    public function criar(int $userId): Encomenda|null
    {
        $linhas = Cart::detalhe();
        if ($linhas === []) {
            $this->erro = 'O carrinho está vazio.';

            return null;
        }
        foreach ($linhas as $l) {
            if ($l['problema'] !== null) {
                $this->erro = $l['produto']->nome . ': ' . $l['problema'];

                return null;
            }
        }

        $db = Yii::$app->db;
        $transacao = $db->beginTransaction();
        try {
            $encomenda = new Encomenda();
            $encomenda->user_id = $userId;
            $encomenda->metodo_pagamento_id = (int) $this->metodo_pagamento_id;
            $encomenda->email = $this->email;
            $encomenda->morada_envio = $this->morada_envio;
            $encomenda->estado = 'Pendente';
            $encomenda->total = Cart::total();
            $encomenda->created_at = time();
            $encomenda->updated_at = time();
            if (!$encomenda->save()) {
                throw new \RuntimeException('Não foi possível gravar a encomenda.');
            }

            foreach ($linhas as $l) {
                //desconta o stock; se entretanto alguem comprou o ultimo par, nao desconta nada (0 linhas) e falha
                $descontou = $db->createCommand(
                    'UPDATE {{%produto_tamanho}} SET stock = stock - :q WHERE produto_id = :p AND tamanho_id = :t AND stock >= :q',
                    [':q' => $l['quantidade'], ':p' => $l['produto']->id, ':t' => $l['tamanho']->id]
                )->execute();
                if ($descontou === 0) {
                    throw new \RuntimeException($l['produto']->nome . ': já não há stock suficiente.');
                }

                $linha = new LinhaEncomenda();
                $linha->encomenda_id = $encomenda->id;
                $linha->produto_id = $l['produto']->id;
                $linha->tamanho_id = $l['tamanho']->id;
                $linha->cor_id = $l['cor']->id;
                //o preco fica gravado: se o produto mudar de preco depois, a encomenda nao muda
                $linha->preco_unitario = $l['preco'];
                $linha->quantidade = $l['quantidade'];
                if (!$linha->save()) {
                    throw new \RuntimeException('Não foi possível gravar uma linha da encomenda.');
                }
            }

            $transacao->commit();
            Cart::limpar();

            return $encomenda;
        } catch (\Throwable $e) {
            $transacao->rollBack();
            $this->erro = $e->getMessage();

            return null;
        }
    }
}
