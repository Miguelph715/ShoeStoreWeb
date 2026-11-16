<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var frontend\models\EncomendaForm $form */
/** @var array $linhas */
/** @var float $total */

use frontend\components\Loja;
use frontend\models\EncomendaForm;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Finalizar encomenda';
$this->params['breadcrumbs'][] = ['label' => 'Carrinho', 'url' => ['/carrinho/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-finalizar">
    <h1 class="shop-display mb-4">Finalizar encomenda</h1>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="shop-panel">
                <?php $f = ActiveForm::begin(['id' => 'form-encomenda', 'enableClientValidation' => false]); ?>

                <h2 class="h5 mb-3">Dados de envio</h2>
                <?= $f->field($form, 'email')->textInput(['autocomplete' => 'email']) ?>
                <?= $f->field($form, 'morada_envio')->textInput(['autocomplete' => 'street-address', 'placeholder' => 'Rua, número, código postal e cidade']) ?>

                <h2 class="h5 mt-4 mb-3">Pagamento</h2>
                <?= $f->field($form, 'metodo_pagamento_id')->radioList(EncomendaForm::metodos(), ['class' => 'pay-methods'])->label(false) ?>
                <p class="small text-secondary">Neste projeto o pagamento é simulado: a encomenda fica pendente até ser confirmada.</p>

                <div class="d-grid mt-4">
                    <?= Html::submitButton('Confirmar encomenda', ['class' => 'btn btn-shoe btn-lg']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="shop-panel">
                <h2 class="h5 mb-3">A tua encomenda</h2>
                <?php foreach ($linhas as $l): ?>
                    <div class="d-flex justify-content-between small mb-2">
                        <span>
                            <?= (int) $l['quantidade'] ?> &times; <?= Html::encode($l['produto']->nome) ?>
                            <span class="text-secondary">(<?= Html::encode(rtrim(rtrim(number_format((float) $l['tamanho']->numero, 1, '.', ''), '0'), '.')) ?>, <?= Html::encode($l['cor']?->nome ?? '') ?>)</span>
                        </span>
                        <span><?= Loja::euros($l['subtotal']) ?></span>
                    </div>
                <?php endforeach ?>
                <div class="d-flex justify-content-between mt-3 pt-3 border-top border-secondary-subtle"><span class="text-secondary">Envio</span><span>Grátis</span></div>
                <div class="d-flex justify-content-between fs-5 fw-semibold mt-2"><span>Total</span><span class="product-price"><?= Loja::euros($total) ?></span></div>
            </div>
        </div>
    </div>
</div>
