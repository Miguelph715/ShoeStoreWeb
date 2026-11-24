<?php

declare(strict_types=1);

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\user`.
 */
/**
 * Filtros da lista de utilizadores: id, username, email, estado e role.
 */
class UserSearch extends User
{

    //filtro por role (o role vive na tabela auth_assignment, nao na user)
    public $role;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email', 'role'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    /**
     * Cria o data provider da lista, com os filtros aplicados.
     */
    public function search(array $params, string|null $formName = null)
    {
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_ASC]],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['user.id' => $this->id, 'user.status' => $this->status]);
        $query->andFilterWhere(['like', 'user.username', $this->username])
            ->andFilterWhere(['like', 'user.email', $this->email]);

        //filtrar por role: junta a tabela auth_assignment
        if ($this->role !== null && $this->role !== '') {
            $query->innerJoin('auth_assignment', 'auth_assignment.user_id = user.id')
                ->andWhere(['auth_assignment.item_name' => $this->role]);
        }

        return $dataProvider;
    }
}
