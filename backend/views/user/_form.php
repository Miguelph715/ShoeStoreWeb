<?php

use backend\models\UserForm;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\UserForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'new-password'])->hint($model->user ? 'Deixa vazio para manter a password atual.' : '') ?>

    <?= $form->field($model, 'role')->dropDownList(UserForm::roleList()) ?>

    <?= $form->field($model, 'status')->dropDownList(User::statusList()) ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>