<?php

declare(strict_types=1);

use backend\models\ProdutoForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\ProdutoForm $model */
?>
<div class="produto-form card">
    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'descricao')->textarea(['rows' => 3]) ?>

        <div class="row">
            <div class="col-md-6"><?= $form->field($model, 'marca_id')->dropDownList(ProdutoForm::marcas(), ['prompt' => 'Escolhe...']) ?></div>
            <div class="col-md-6"><?= $form->field($model, 'categoria_id')->dropDownList(ProdutoForm::categorias(), ['prompt' => 'Escolhe...']) ?></div>
        </div>

        <div class="row">
            <div class="col-md-6"><?= $form->field($model, 'preco')->input('number', ['step' => '0.01', 'min' => 0]) ?></div>
            <div class="col-md-6">
                <?= $form->field($model, 'preco_promocional')->input('number', ['step' => '0.01', 'min' => 0])->hint('Deixa vazio se não estiver em promoção.') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6"><?= $form->field($model, 'genero')->dropDownList(ProdutoForm::generos()) ?></div>
            <div class="col-md-6"><?= $form->field($model, 'uso')->dropDownList(ProdutoForm::usos()) ?></div>
        </div>

        <?= $form->field($model, 'imagem')->textInput(['maxlength' => true, 'placeholder' => 'https://...'])->hint('Opcional. Por agora a loja mostra o ícone da sapatilha.') ?>

        <?= $form->field($model, 'cores')->checkboxList(ProdutoForm::listaCores(), ['inline' => true]) ?>

        <?= $form->field($model, 'ativo')->checkbox() ?>

        <div class="form-group mb-0">
            <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
