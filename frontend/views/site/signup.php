<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Criar conta';
$this->params['fullWidth'] = true;
$this->params['meta_description'] = 'Cria a tua conta ShoeStore.';
?>
<section class="auth-wrap site-signup">
    <div class="auth-card">
        <h1 class="h3 fw-bold mb-1">Criar conta</h1>
        <p class="text-secondary mb-4">Preenche os campos para começares a comprar.</p>

        <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'autocomplete' => 'username']) ?>

        <?= $form->field($model, 'email')->textInput(['autocomplete' => 'email']) ?>

        <?= $form->field($model, 'password')
            ->passwordInput(['autocomplete' => 'new-password'])
            ->hint('Mínimo de ' . Yii::$app->params['user.passwordMinLength'] . ' caracteres.') ?>

        <div class="d-grid mt-3">
            <?= Html::submitButton('Criar conta', ['class' => 'btn btn-shoe btn-lg', 'name' => 'signup-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <p class="text-center text-secondary small mt-4 mb-0">
            Já tens conta? <?= Html::a('Entrar', ['/site/login']) ?>
        </p>
    </div>
</section>