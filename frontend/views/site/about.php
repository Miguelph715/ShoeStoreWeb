<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Sobre nós';
$this->params['fullWidth'] = true;
?>
<div class="site-about">
    <section class="shop-hero text-center" style="min-height: 40vh">
        <div class="container">
            <div class="shop-eyebrow mb-3">A nossa história</div>
            <h1 class="shop-display">Sobre nós</h1>
        </div>
    </section>

    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <p class="lead">
                    A ShoeStore é uma loja de calçado online feita para quem se mexe todos os dias:
                    do treino à rua, do trabalho ao fim de semana.
                </p>
                <p class="text-secondary">
                    Reunimos as melhores marcas num só sítio, com o stock por tamanho e por cor sempre à vista,
                    para encontrares o ténis certo sem perderes tempo.
                </p>
            </div>
        </div>

        <div class="row g-4 mt-4 text-center">
            <div class="col-md-4">
                <div class="product-card p-4 h-100">
                    <i class="fas fa-shoe-prints fs-2" style="color: var(--shoe-accent)"></i>
                    <h2 class="h5 mt-3">Marcas de confiança</h2>
                    <p class="text-secondary small mb-0">Um catálogo com várias marcas e categorias.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product-card p-4 h-100">
                    <i class="fas fa-ruler fs-2" style="color: var(--shoe-accent)"></i>
                    <h2 class="h5 mt-3">O teu tamanho</h2>
                    <p class="text-secondary small mb-0">Escolhe o tamanho e a cor antes de comprares.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="product-card p-4 h-100">
                    <i class="fas fa-lock fs-2" style="color: var(--shoe-accent)"></i>
                    <h2 class="h5 mt-3">Compra segura</h2>
                    <p class="text-secondary small mb-0">Os teus dados e pagamentos protegidos.</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <?= Html::a('Voltar à loja', Yii::$app->homeUrl, ['class' => 'btn btn-shoe shop-pill']) ?>
        </div>
    </section>
</div>