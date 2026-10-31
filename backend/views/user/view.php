<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var common\models\Perfil|null $perfil */
/** @var string[] $roles */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Utilizadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if ($model->status === User::STATUS_DELETED): ?>
            <?= Html::a('Restaurar', ['restore', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => ['method' => 'post'],
            ]) ?>
        <?php else: ?>
            <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminar este utilizador? Pode ser restaurado depois.', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        //nao se mostram o auth_key nem o hash da password
        'attributes' => [
            'id',
            'username',
            'email:email',
            ['label' => 'Nome', 'value' => $perfil?->nome],
            ['label' => 'Role', 'value' => implode(', ', $roles) ?: '-'],
            ['label' => 'Tipo (perfil)', 'value' => $perfil?->user_type],
            ['attribute' => 'status', 'value' => $model->statusLabel],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>