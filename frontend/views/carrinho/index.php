<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $linhas */
/** @var float $total */

use frontend\components\Loja;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Carrinho';
$this->params['breadcrumbs'][] = $this->title;
$temProblema = (bool) array_filter(array_column($linhas, 'problema'));
?>
<div class="site-carrinho">
    <h1 class="shop-display mb-4">O teu carrinho</h1>

    <?php if (!$linhas): ?>
        <div class="shop-panel text-center py-5">
            <i class="fas fa-shopping-bag fs-1 mb-3" style="color: var(--shoe-accent)"></i>
            <h2 class="h5">O teu carrinho está vazio</h2>
            <p class="text-secondary">Escolhe os teus próximos ténis.</p>
            <?= Html::a('Ver novidades', ['/produto/index', 'novidades' => 1], ['class' => 'btn btn-shoe shop-pill']) ?>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php foreach ($linhas as $l): ?>
                    <?php $p = $l['produto']; ?>
                    <div class="shop-panel cart-line mb-3">
                        <div class="cart-thumb"><i class="fas fa-shoe-prints"></i></div>

                        <div class="flex-grow-1">
                            <div class="small text-secondary"><?= Html::encode($p->marca->nome) ?></div>
                            <a class="fw-semibold text-white text-decoration-none" href="<?= Url::to(['/produto/view', 'id' => $p->id]) ?>"><?= Html::encode($p->nome) ?></a>
                            <div class="small text-secondary">
                                Tamanho <?= Html::encode(rtrim(rtrim(number_format((float) $l['tamanho']->numero, 1, '.', ''), '0'), '.')) ?>
                                &middot; <?= Html::encode($l['cor']?->nome ?? '') ?>
                            </div>
                            <div class="product-price mt-1"><?= Loja::euros($l['preco']) ?></div>
                            <?php if ($l['problema']): ?>
                                <div class="small text-warning mt-1"><i class="fas fa-exclamation-triangle me-1"></i><?= Html::encode($l['problema']) ?></div>
                            <?php endif ?>
                        </div>

                        <div class="cart-actions">
                            <?= Html::beginForm(['/carrinho/atualizar'], 'post', ['class' => 'd-flex gap-2']) ?>
                                <?= Html::hiddenInput('chave', $l['chave']) ?>
                                <?= Html::input('number', 'quantidade', (string) $l['quantidade'], ['class' => 'form-control form-control-sm', 'min' => 1, 'max' => 10, 'style' => 'width: 4.5rem', 'aria-label' => 'Quantidade']) ?>
                                <button type="submit" class="btn btn-sm btn-outline-light">Atualizar</button>
                            <?= Html::endForm() ?>
                            <div class="fw-semibold"><?= Loja::euros($l['subtotal']) ?></div>
                            <?= Html::beginForm(['/carrinho/remover'], 'post') ?>
                                <?= Html::hiddenInput('chave', $l['chave']) ?>
                                <button type="submit" class="btn btn-sm btn-link text-secondary p-0"><i class="fas fa-trash-alt me-1"></i>Remover</button>
                            <?= Html::endForm() ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <div class="col-lg-4">
                <div class="shop-panel">
                    <h2 class="h5 mb-3">Resumo</h2>
                    <div class="d-flex justify-content-between mb-2"><span class="text-secondary">Produtos</span><span><?= Loja::euros($total) ?></span></div>
                    <div class="d-flex justify-content-between mb-3"><span class="text-secondary">Envio</span><span>Grátis</span></div>
                    <div class="d-flex justify-content-between fs-5 fw-semibold border-top border-secondary-subtle pt-3 mb-4">
                        <span>Total</span><span class="product-price"><?= Loja::euros($total) ?></span>
                    </div>
                    <?php if ($temProblema): ?>
                        <p class="small text-warning">Resolve os avisos nas linhas para poderes finalizar.</p>
                        <button type="button" class="btn btn-secondary w-100" disabled>Finalizar encomenda</button>
                    <?php else: ?>
                        <?= Html::a('Finalizar encomenda', ['/encomenda/finalizar'], ['class' => 'btn btn-shoe btn-lg w-100']) ?>
                    <?php endif ?>
                    <?= Html::a('Continuar a comprar', ['/produto/index'], ['class' => 'btn btn-link text-secondary w-100 mt-2']) ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>
