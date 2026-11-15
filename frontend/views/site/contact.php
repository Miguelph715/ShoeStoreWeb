<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \frontend\models\ContactForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\captcha\Captcha;

$this->title = 'Contactos';
$this->params['fullWidth'] = true;
?>
<section class="auth-wrap site-contact">
    <div class="auth-card auth-card-wide">
        <h1 class="h3 fw-bold mb-1">Contactos</h1>
        <p class="text-secondary mb-4">Tens uma dúvida? Escreve-nos e respondemos o mais depressa possível.</p>

        <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'email') ?>
            </div>
        </div>

        <?= $form->field($model, 'subject') ?>

        <?= $form->field($model, 'body')->textarea(['rows' => 5]) ?>

        <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
            'template' => '<div class="d-flex align-items-center gap-3">{image}{input}</div>',
        ]) ?>

        <div class="d-grid mt-3">
            <?= Html::submitButton('Enviar', ['class' => 'btn btn-shoe btn-lg', 'name' => 'contact-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</section>