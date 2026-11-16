<?php

declare(strict_types=1);

use backend\models\StockForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\StockForm $form */

$produto = $form->produto;
$this->title = 'Stock: ' . $produto->nome;
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $produto->nome, 'url' => ['view', 'id' => $produto->id]];
$this->params['breadcrumbs'][] = 'Stock';
?>
<div class="produto-stock">
    <div class="card">
        <div class="card-body">
            <p class="text-muted">
                Indica quantos pares há de cada tamanho. <b>0</b> = esgotado (o tamanho continua à vista, riscado).
                <b>Em branco</b> = o produto não tem esse tamanho.
            </p>

            <?php if ($form->hasErrors()): ?>
                <div class="alert alert-danger"><?= Html::encode($form->getFirstError('stock')) ?></div>
            <?php endif ?>

            <?= Html::beginForm(['stock', 'id' => $produto->id], 'post') ?>
                <div class="row">
                    <?php foreach (StockForm::tamanhos() as $t): ?>
                        <?php $n = rtrim(rtrim(number_format((float) $t->numero, 1, '.', ''), '0'), '.'); ?>
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                            <div class="form-group">
                                <label for="stock-<?= $t->id ?>">Tamanho <?= Html::encode($n) ?></label>
                                <?= Html::input('number', 'stock[' . $t->id . ']', $form->stock[$t->id] ?? '', [
                                    'class' => 'form-control', 'id' => 'stock-' . $t->id, 'min' => 0, 'max' => 100000, 'step' => 1,
                                ]) ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <?= Html::submitButton('Guardar stock', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Cancelar', ['view', 'id' => $produto->id], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
