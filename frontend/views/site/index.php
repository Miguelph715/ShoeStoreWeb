<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\Produto[] $produtos */
/** @var int[] $favoritos */

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
            <?= Html::a('Comprar', ['/produto/index', 'novidades' => 1], ['class' => 'btn btn-light shop-pill']) ?>
        </div>
    </section>

    <!-- Tres cartoes grandes -->
    <section class="container py-5">
        <div class="row g-3">
            <div class="col-md-4">
                <?= Html::a('<i class="fas fa-shoe-prints"></i><span class="btn btn-light shop-pill">Comprar para homem</span>', ['/produto/index', 'genero' => 'homem'], ['class' => 'shop-tile']) ?>
            </div>
            <div class="col-md-4">
                <?= Html::a('<i class="fas fa-shoe-prints"></i><span class="btn btn-light shop-pill">Comprar para mulher</span>', ['/produto/index', 'genero' => 'mulher'], ['class' => 'shop-tile']) ?>
            </div>
            <div class="col-md-4">
                <?= Html::a('<i class="fas fa-shoe-prints"></i><span class="btn btn-light shop-pill">Comprar para criança</span>', ['/produto/index', 'genero' => 'crianca'], ['class' => 'shop-tile']) ?>
            </div>
        </div>
    </section>

    <!-- Os icones (os produtos mais recentes, lidos da base de dados) -->
    <section class="container pb-5" id="icones">
        <h2 class="shop-display shop-section-title">Compra os nossos ícones</h2>

        <?php if (empty($produtos)): ?>
            <p class="text-secondary text-center">Ainda não há produtos disponíveis.</p>
        <?php else: ?>
            <div class="row g-3 g-md-4">
                <?php foreach ($produtos as $p): ?>
                    <div class="col-6 col-lg-3">
                        <?= $this->render('/produto/_card', ['p' => $p, 'favoritos' => $favoritos]) ?>
                    </div>
                <?php endforeach ?>
            </div>
            <div class="text-center mt-4">
                <?= Html::a('Ver todos os produtos', ['/produto/index'], ['class' => 'btn btn-outline-light shop-pill']) ?>
            </div>
        <?php endif ?>
    </section>

    <!-- Faixa grande -->
    <section class="shop-banner">
        <div class="container">
            <h2 class="shop-display mb-3">Não falhes<br>um único dia</h2>
            <p class="mb-4">O tempo muda. Os teus objetivos não.</p>
            <?= Html::a('Promoções', ['/produto/index', 'promocao' => 1], ['class' => 'btn btn-light shop-pill me-2']) ?>
            <?= Html::a('Desporto', ['/produto/index', 'uso' => 'desporto'], ['class' => 'btn btn-outline-light shop-pill']) ?>
        </div>
    </section>
</div>
