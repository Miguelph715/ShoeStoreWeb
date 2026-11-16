<?php

declare(strict_types=1);

namespace backend\models;

use common\models\Produto;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * Os filtros da lista de produtos do back-office.
 */
class ProdutoSearch extends Model
{
    public $id;
    public $nome;
    public $marca_id;
    public $categoria_id;
    public $genero;
    public $uso;
    public $ativo;
    //esgotado, baixo (5 ou menos pares) ou normal
    public $stock;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'marca_id', 'categoria_id', 'ativo'], 'integer'],
            [['nome', 'genero', 'uso', 'stock'], 'safe'],
        ];
    }

    public static function opcoesStock(): array
    {
        return ['esgotado' => 'Esgotado', 'baixo' => 'Stock baixo (até 5)', 'normal' => 'Stock normal'];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Produto::find()->alias('p')->with(['marca', 'categoria', 'produtoTamanhos']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id', 'nome', 'preco', 'ativo'],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'p.id' => $this->id,
            'p.marca_id' => $this->marca_id,
            'p.categoria_id' => $this->categoria_id,
            'p.genero' => $this->genero,
            'p.uso' => $this->uso,
            'p.ativo' => $this->ativo,
        ]);
        $query->andFilterWhere(['like', 'p.nome', $this->nome]);

        //o stock total do produto: soma dos tamanhos
        $total = '(SELECT COALESCE(SUM(pt.stock), 0) FROM {{%produto_tamanho}} pt WHERE pt.produto_id = p.id)';
        match ($this->stock) {
            'esgotado' => $query->andWhere(new Expression($total . ' = 0')),
            'baixo' => $query->andWhere(new Expression($total . ' BETWEEN 1 AND 5')),
            'normal' => $query->andWhere(new Expression($total . ' > 5')),
            default => null,
        };

        return $dataProvider;
    }
}
