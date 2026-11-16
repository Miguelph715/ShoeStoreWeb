<?php

declare(strict_types=1);

namespace backend\models;

use common\components\Encomendas;
use common\models\Encomenda;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Os filtros da lista de encomendas do back-office.
 */
class EncomendaSearch extends Model
{
    public $id;
    public $cliente;
    public $estado;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['id', 'integer'],
            [['cliente', 'estado'], 'safe'],
        ];
    }

    public static function estados(): array
    {
        return array_combine(Encomendas::ESTADOS, Encomendas::ESTADOS);
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Encomenda::find()->alias('e')->joinWith('user u')->with('metodoPagamento');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => ['id', 'total', 'created_at'],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['e.id' => $this->id, 'e.estado' => $this->estado]);
        $query->andFilterWhere(['like', 'u.username', $this->cliente]);

        return $dataProvider;
    }
}
