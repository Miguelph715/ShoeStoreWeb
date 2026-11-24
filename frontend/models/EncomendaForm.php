<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\Encomenda;
use common\models\LinhaEncomenda;
use common\models\MetodoPagamento;
use common\models\ProdutoTamanho;
use frontend\components\Cart;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Finalizar encomenda: email, morada e método de pagamento.
 */
class EncomendaForm extends Model
{
    public string $email = '';
    public string $morada_envio = '';
    public $metodo_pagamento_id;

    //motivo da falha em criar()
    public string $erro = '';

    public function rules()
    {
        return [
            [['email', 'morada_envio'], 'trim'],
            [['email', 'morada_envio', 'metodo_pagamento_id'], 'required'],
            ['email', 'email'],
            [['email', 'morada_envio'], 'string', 'max' => 255],
            ['metodo_pagamento_id', 'in', 'range' => array_keys(self::metodos()), 'message' => 'Escolhe um método de pagamento.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'morada_envio' => 'Morada de envio',
            'metodo_pagamento_id' => 'Método de pagamento',
        ];
    }

    //métodos ativos: id => nome
    public static function metodos()
    {
        return ArrayHelper::map(MetodoPagamento::find()->where(['ativo' => 1])->orderBy('id')->asArray()->all(), 'id', 'nome');
    }

    /**
     * Cria a encomenda a partir do carrinho; null se falhar (ver $erro).
     */
    public function criar(int $userId)
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

        $encomenda = new Encomenda([
            'user_id' => $userId,
            'metodo_pagamento_id' => (int) $this->metodo_pagamento_id,
            'email' => $this->email,
            'morada_envio' => $this->morada_envio,
            'estado' => 'Pendente',
            'total' => Cart::total(),
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        if (!$encomenda->validate()) {
            $this->erro = 'Não foi possível gravar a encomenda.';

            return null;
        }

        //desconta o stock; se faltar, repõe o que já tinha descontado
        $descontadas = [];
        foreach ($linhas as $l) {
            $where = ['and', ['produto_id' => $l['produto']->id, 'tamanho_id' => $l['tamanho']->id], ['>=', 'stock', $l['quantidade']]];
            if (ProdutoTamanho::updateAllCounters(['stock' => -$l['quantidade']], $where) === 0) {
                $this->reporStock($descontadas);
                $this->erro = $l['produto']->nome . ': já não há stock suficiente.';

                return null;
            }
            $descontadas[] = $l;
        }

        $encomenda->save(false);
        foreach ($linhas as $l) {
            //o preço fica guardado na linha
            $linha = new LinhaEncomenda([
                'encomenda_id' => $encomenda->id,
                'produto_id' => $l['produto']->id,
                'tamanho_id' => $l['tamanho']->id,
                'cor_id' => $l['cor']->id,
                'preco_unitario' => $l['preco'],
                'quantidade' => $l['quantidade'],
            ]);
            $linha->save(false);
        }

        Cart::limpar();

        return $encomenda;
    }

    private function reporStock(array $linhas)
    {
        foreach ($linhas as $l) {
            ProdutoTamanho::updateAllCounters(
                ['stock' => $l['quantidade']],
                ['produto_id' => $l['produto']->id, 'tamanho_id' => $l['tamanho']->id]
            );
        }
    }
}
