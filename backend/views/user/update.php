<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\UserForm $model */

$this->title = 'Editar utilizador: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Utilizadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->user->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="user-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>