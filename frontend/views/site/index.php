<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $produtos */

use yii\helpers\Html;

$this->title = 'Início';
$this->params['fullWidth'] = true;
?>
<div class="site-index">

    <!-- Hero -->
    <section class="shop-hero text-center">
        <div class="container">
            <div class="shop-eyebrow mb-3">Coleção 2026</div>
            <h1 class="shop-display">Corre mais longe<br>Anda com estilo</h1>
            <a href="#icones" class="btn btn-light shop-pill">Comprar</a>
        </div>
    </section>

    <!-- Tres cartoes grandes -->
    <section class="container py-5">
        <div class="row g-3">
            <div class="col-md-4">
                <a href="#" class="shop-tile">
                    <i class="fas fa-shoe-prints"></i>
                    <span class="btn btn-light shop-pill">Comprar essenciais</span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="shop-tile">
                    <i class="fas fa-shoe-prints"></i>
                    <span class="btn btn-light shop-pill">Comprar running</span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="shop-tile">
                    <i class="fas fa-shoe-prints"></i>
                    <span class="btn btn-light shop-pill">Comprar estilo</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Os icones (produtos lidos da base de dados) -->
    <section class="container pb-5" id="icones">
        <h2 class="shop-display shop-section-title">Compra os nossos ícones</h2>

        <?php if (empty($produtos)): ?>
            <p class="text-secondary text-center">Ainda não há produtos disponíveis.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($produtos as $p): ?>
                    <div class="col-6 col-lg-3">
                        <div class="product-card h-100">
                            <div class="product-img"><i class="fas fa-shoe-prints"></i></div>
                            <div class="p-3">
                                <div class="small text-secondary"><?= Html::encode($p['marca']) ?></div>
                                <div class="fw-semibold"><?= Html::encode($p['nome']) ?></div>
                                <div class="product-price mt-1"><?= number_format((float) $p['preco'], 2, ',', '.') ?> €</div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>

    <!-- Faixa grande -->
    <section class="shop-banner">
        <div class="container">
            <h2 class="shop-display mb-3">Não falhes<br>um único dia</h2>
            <p class="mb-4">O tempo muda. Os teus objetivos não.</p>
            <a href="#" class="btn btn-light shop-pill me-2">Comprar</a>
            <a href="#" class="btn btn-outline-light shop-pill">Explorar</a>
        </div>
    </section>
</div>