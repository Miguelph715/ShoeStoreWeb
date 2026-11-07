<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap4\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$this->title = 'Entrar';
?>
<div class="shoe-login">

    <!-- Painel da marca (esquerda; em ecras pequenos fica so o logotipo) -->
    <div class="shoe-login-brand">
        <div class="shoe-login-logo"><i class="fas fa-shoe-prints"></i> ShoeStore</div>
        <div class="shoe-login-claim">
            <h2>Gere a loja,<br>passo a passo.</h2>
            <p>Catálogo, stock e encomendas num só sítio.</p>
        </div>
    </div>

    <!-- Formulario (direita) -->
    <div class="shoe-login-side">
        <div class="shoe-login-box">
            <h1>Entrar</h1>
            <p class="text-muted mb-4">Acesso reservado a gestores e administradores.</p>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'enableClientValidation' => false,
            ]); ?>

            <?php
            //se o campo tem erro, mostra um icone; a mensagem aparece num balao ao passar o rato
            $icone = static fn(string $campo): string => $model->hasErrors($campo)
                ? Html::tag('span', '<i class="fas fa-exclamation-circle"></i>', [
                    'class' => 'shoe-field-alert',
                    'tabindex' => 0,
                    'data-tip' => $model->getFirstError($campo),
                ])
                : '';
            ?>

            <?= $form->field($model, 'username', [
                'template' => "{label}\n<div class=\"shoe-field\">{input}" . $icone('username') . "</div>",
            ])
                ->textInput(['autofocus' => true, 'autocomplete' => 'off'])
                ->label('Nome de utilizador') ?>

            <?= $form->field($model, 'password', [
                'template' => "{label}\n<div class=\"shoe-field\">{input}" . $icone('password') . "</div>",
            ])
                ->passwordInput(['autocomplete' => 'new-password'])
                ->label('Password') ?>

            <?= $form->field($model, 'rememberMe')->checkbox()->label('Lembrar-me') ?>

            <?= Html::submitButton('Entrar', ['class' => 'btn btn-shoe btn-lg btn-block', 'name' => 'login-button']) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>