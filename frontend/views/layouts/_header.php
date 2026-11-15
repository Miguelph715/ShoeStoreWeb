<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\Url;

//o menu do meio (as paginas de catalogo so existem na Etapa 2: por agora as ligacoes ficam em '#')
$menu = ['Novidades', 'Homem', 'Mulher', 'Criança', 'Desporto'];
$convidado = Yii::$app->user->isGuest;
?>
<header id="header" class="shop-header sticky-top">

    <!-- Barra fina -->
    <div class="shop-utility">
        <div class="container-fluid px-4 d-flex justify-content-end align-items-center gap-3">
            <?= Html::a('Ajuda', ['/site/contact']) ?>
            <?php if ($convidado): ?>
                <?= Html::a('Criar conta', ['/site/signup']) ?>
                <?= Html::a('Entrar', ['/site/login']) ?>
            <?php else: ?>
                <span>Olá, <?= Html::encode(Yii::$app->user->identity->username) ?></span>
                <?= Html::a('Sair', ['/site/logout'], ['data-method' => 'post']) ?>
            <?php endif ?>
        </div>
    </div>

    <!-- Barra principal -->
    <nav class="shop-nav">
        <div class="container-fluid px-4 d-flex align-items-center gap-3">
            <button class="shop-icon-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#menu-lateral"
                    aria-controls="menu-lateral" aria-label="Abrir o menu">
                <i class="fas fa-bars"></i>
            </button>

            <a class="shop-logo fs-5" href="<?= Url::home() ?>"><i class="fas fa-shoe-prints"></i>ShoeStore</a>

            <ul class="nav mx-auto d-none d-lg-flex">
                <?php foreach ($menu as $texto): ?>
                    <li class="nav-item"><a class="nav-link" href="#"><?= Html::encode($texto) ?></a></li>
                <?php endforeach ?>
            </ul>

            <div class="d-flex align-items-center gap-2 ms-auto ms-lg-0">
                <div class="shop-search d-none d-md-flex">
                    <i class="fas fa-search"></i>
                    <input type="search" placeholder="Pesquisar" aria-label="Pesquisar" autocomplete="off">
                </div>
                <a class="shop-icon-btn" href="#" aria-label="Favoritos"><i class="far fa-heart"></i></a>
                <a class="shop-icon-btn" href="#" aria-label="Carrinho"><i class="fas fa-shopping-bag"></i></a>
            </div>
        </div>
    </nav>
</header>

<!-- Menu lateral -->
<div class="offcanvas offcanvas-start shop-drawer" tabindex="-1" id="menu-lateral" aria-labelledby="menu-lateral-titulo">
    <div class="offcanvas-header">
        <span class="shop-logo fs-5" id="menu-lateral-titulo"><i class="fas fa-shoe-prints"></i>ShoeStore</span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body pt-0">

        <div class="shop-drawer-title">Loja</div>
        <a class="shop-drawer-link" href="#">Novidades</a>
        <a class="shop-drawer-link" href="#">Promoções <span class="shop-badge">%</span></a>
        <a class="shop-drawer-link" href="#">Homem <i class="fas fa-chevron-right small"></i></a>
        <a class="shop-drawer-link" href="#">Mulher <i class="fas fa-chevron-right small"></i></a>
        <a class="shop-drawer-link" href="#">Criança <i class="fas fa-chevron-right small"></i></a>

        <div class="shop-drawer-title">Para quê</div>
        <a class="shop-drawer-link" href="#">Desporto</a>
        <a class="shop-drawer-link" href="#">Estilo</a>
        <a class="shop-drawer-link" href="#">Formal</a>

        <div class="shop-drawer-title">Explorar</div>
        <a class="shop-drawer-link" href="#">Filtros <i class="fas fa-sliders-h small"></i></a>

        <div class="shop-drawer-title">Ajuda</div>
        <?= Html::a('Sobre nós', ['/site/about'], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Contactos', ['/site/contact'], ['class' => 'shop-drawer-link']) ?>
    </div>
</div>