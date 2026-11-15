<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Entrar';
$this->params['fullWidth'] = true;
$this->params['meta_description'] = 'Entra na tua conta ShoeStore.';
?>
<section class="auth-wrap site-login">
    <div class="auth-card">
        <h1 class="h3 fw-bold mb-1">Entrar</h1>
        <p class="text-secondary mb-4">Bem-vindo de volta à ShoeStore.</p>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'enableClientValidation' => false,
        ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'autocomplete' => 'username']) ?>

        <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'current-password']) ?>

        <?= $form->field($model, 'rememberMe')->checkbox() ?>

        <div class="d-grid mt-3">
            <?= Html::submitButton('Entrar', ['class' => 'btn btn-shoe btn-lg', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <p class="text-center text-secondary small mt-4 mb-0">
            Ainda não tens conta? <?= Html::a('Criar conta', ['/site/signup']) ?>
        </p>
    </div>
</section>