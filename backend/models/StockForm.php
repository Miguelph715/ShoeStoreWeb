<?php

declare(strict_types=1);

namespace backend\models;

use common\models\Produto;
use common\models\ProdutoTamanho;
use common\models\Tamanho;
use Yii;
use yii\base\Model;

/**
 * O stock de um produto, tamanho a tamanho.
 * Um tamanho com numero (0 ou mais) e vendido; um tamanho em branco nao e vendido neste produto.
 */
class StockForm extends Model
{
    //tamanho_id => stock (ou '' se o produto nao tem esse tamanho)
    public array $stock = [];

    private Produto $_produto;

    public function __construct(Produto $produto, array $config = [])
    {
        $this->_produto = $produto;
        foreach (ProdutoTamanho::find()->where(['produto_id' => $produto->id])->all() as $pt) {
            $this->stock[$pt->tamanho_id] = (int) $pt->stock;
        }
        parent::__construct($config);
    }

    /**
     * Todos os tamanhos da loja, do mais pequeno para o maior.
     *
     * @return Tamanho[]
     */
    public static function tamanhos(): array
    {
        return Tamanho::find()->orderBy('numero')->all();
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['stock', 'validateStock'],
        ];
    }

    public function validateStock(string $atributo): void
    {
        foreach ($this->stock as $valor) {
            if ($valor === '' || $valor === null) {
                continue;
            }
            if (!is_numeric($valor) || (string) (int) $valor !== trim((string) $valor) || (int) $valor < 0 || (int) $valor > 100000) {
                $this->addError($atributo, 'O stock tem de ser um número inteiro entre 0 e 100000.');

                return;
            }
        }
    }

    /**
     * Grava o stock: cria, atualiza ou tira cada tamanho. Devolve true se correu bem.
     */
    public function guardar(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transacao = Yii::$app->db->beginTransaction();
        try {
            foreach (self::tamanhos() as $tamanho) {
                $valor = $this->stock[$tamanho->id] ?? '';
                $linha = ProdutoTamanho::findOne(['produto_id' => $this->_produto->id, 'tamanho_id' => $tamanho->id]);
                if ($valor === '' || $valor === null) {
                    $linha?->delete();
                    continue;
                }
                $linha ??= new ProdutoTamanho();
                $linha->produto_id = $this->_produto->id;
                $linha->tamanho_id = $tamanho->id;
                $linha->stock = (int) $valor;
                if (!$linha->save()) {
                    throw new \RuntimeException('stock: ' . json_encode($linha->errors));
                }
            }
            $transacao->commit();

            return true;
        } catch (\Throwable $e) {
            $transacao->rollBack();
            $this->addError('stock', 'Não foi possível gravar o stock.');
            Yii::error('StockForm: ' . $e->getMessage());

            return false;
        }
    }

    public function getProduto(): Produto
    {
        return $this->_produto;
    }
}
