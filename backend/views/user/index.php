<?php

use backend\models\UserForm;
use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Utilizadores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Criar utilizador', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //as linhas dos utilizadores eliminados ficam a cinzento
        'rowOptions' => fn(User $model) => $model->status === User::STATUS_DELETED ? ['class' => 'table-secondary text-muted'] : [],
        'columns' => [
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'role',
                'label' => 'Role',
                'filter' => UserForm::roleList(),
                'value' => fn(User $model) => implode(', ', array_keys(Yii::$app->authManager->getRolesByUser($model->id))) ?: '-',
            ],
            [
                'attribute' => 'status',
                //lista pendente com Ativo, Inativo e Eliminado
                'filter' => User::statusList(),
                'value' => fn(User $model) => $model->statusLabel,
            ],
            'created_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete} {restore}',
                'urlCreator' => fn($action, User $model) => Url::to([$action, 'id' => $model->id]),
                'buttons' => [
                    'view' => fn($url) => Html::a('Ver', $url, ['class' => 'btn btn-sm btn-outline-secondary']),
                    'update' => fn($url) => Html::a('Editar', $url, ['class' => 'btn btn-sm btn-outline-primary']),
                    'delete' => fn($url) => Html::a('Eliminar', $url, [
                        'class' => 'btn btn-sm btn-outline-danger',
                        'data' => ['confirm' => 'Eliminar este utilizador? Pode ser restaurado depois.', 'method' => 'post'],
                    ]),
                    'restore' => fn($url) => Html::a('Restaurar', $url, [
                        'class' => 'btn btn-sm btn-outline-success',
                        'data' => ['method' => 'post'],
                    ]),
                ],
                //Eliminar so aparece em utilizadores nao eliminados; Restaurar so nos eliminados
                'visibleButtons' => [
                    'delete' => fn(User $model) => $model->status !== User::STATUS_DELETED,
                    'restore' => fn(User $model) => $model->status === User::STATUS_DELETED,
                ],
            ],
        ],
    ]); ?>

</div>