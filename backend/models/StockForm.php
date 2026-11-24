<?php

declare(strict_types=1);

namespace backend\models;

use common\models\Produto;
use common\models\ProdutoTamanho;
use common\models\Tamanho;
use yii\base\Model;

/**
 * Stock de um produto por tamanho (vazio = tamanho não vendido).
 */
class StockForm extends Model
{
    //tamanho_id => stock ou ''
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
     * @return Tamanho[]
     */
    public static function tamanhos()
    {
        return Tamanho::find()->orderBy('numero')->all();
    }

    public function rules()
    {
        return [
            ['stock', 'validateStock'],
        ];
    }

    public function validateStock(string $atributo)
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
     * Cria, atualiza ou remove o stock de cada tamanho.
     */
    public function guardar()
    {
        if (!$this->validate()) {
            return false;
        }

        foreach (self::tamanhos() as $tamanho) {
            $valor = $this->stock[$tamanho->id] ?? '';
            $linha = ProdutoTamanho::findOne(['produto_id' => $this->_produto->id, 'tamanho_id' => $tamanho->id]);
            if ($valor === '' || $valor === null) {
                $linha?->delete();
                continue;
            }
            $linha ??= new ProdutoTamanho(['produto_id' => $this->_produto->id, 'tamanho_id' => $tamanho->id]);
            $linha->stock = (int) $valor;
            if (!$linha->save()) {
                $this->addError('stock', 'Não foi possível gravar o stock.');

                return false;
            }
        }

        return true;
    }

    public function getProduto()
    {
        return $this->_produto;
    }
}
