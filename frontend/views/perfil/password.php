<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var frontend\models\AlterarPasswordForm $form */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Alterar password';
$this->params['fullWidth'] = true;
?>
<section class="auth-wrap">
    <div class="auth-card">
        <h1 class="h3 fw-bold mb-1">Alterar password</h1>
        <p class="text-secondary mb-4">Escolhe uma password com pelo menos <?= Yii::$app->params['user.passwordMinLength'] ?> caracteres.</p>

        <?php $f = ActiveForm::begin(['id' => 'form-password', 'enableClientValidation' => false]); ?>

        <?= $f->field($form, 'atual')->passwordInput(['autofocus' => true, 'autocomplete' => 'current-password']) ?>
        <?= $f->field($form, 'nova')->passwordInput(['autocomplete' => 'new-password']) ?>
        <?= $f->field($form, 'repetir')->passwordInput(['autocomplete' => 'new-password']) ?>

        <div class="d-flex gap-2 mt-3">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-shoe btn-lg flex-grow-1']) ?>
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</section>
