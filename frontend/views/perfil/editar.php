<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var frontend\models\PerfilForm $form */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Editar perfil';
$this->params['fullWidth'] = true;
?>
<section class="auth-wrap">
    <div class="auth-card auth-card-wide">
        <h1 class="h3 fw-bold mb-1">Editar perfil</h1>
        <p class="text-secondary mb-4">Estes dados ficam já preenchidos quando fizeres uma encomenda.</p>

        <?php $f = ActiveForm::begin(['id' => 'form-perfil', 'enableClientValidation' => false]); ?>

        <?= $f->field($form, 'nome')->textInput(['autofocus' => true]) ?>

        <div class="row">
            <div class="col-sm-6"><?= $f->field($form, 'telefone')->textInput(['autocomplete' => 'tel']) ?></div>
            <div class="col-sm-6"><?= $f->field($form, 'nif')->textInput(['maxlength' => 9, 'inputmode' => 'numeric']) ?></div>
        </div>

        <?= $f->field($form, 'morada')->textInput(['autocomplete' => 'street-address']) ?>

        <div class="row">
            <div class="col-sm-4"><?= $f->field($form, 'codigo_postal')->textInput(['placeholder' => '0000-000', 'autocomplete' => 'postal-code']) ?></div>
            <div class="col-sm-8"><?= $f->field($form, 'cidade')->textInput(['autocomplete' => 'address-level2']) ?></div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-shoe btn-lg flex-grow-1']) ?>
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</section>
