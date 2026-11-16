<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use frontend\components\Cart;
use frontend\components\Loja;
use yii\helpers\Html;
use yii\helpers\Url;

$convidado = Yii::$app->user->isGuest;
$quantidadeCarrinho = $convidado ? 0 : Cart::quantidadeTotal();
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
                <?= Html::a('Olá, ' . Html::encode(Yii::$app->user->identity->username), ['/perfil/index']) ?>
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
                <?php foreach (Loja::menuPrincipal() as [$texto, $rota]): ?>
                    <li class="nav-item"><?= Html::a(Html::encode($texto), $rota, ['class' => 'nav-link']) ?></li>
                <?php endforeach ?>
            </ul>

            <div class="d-flex align-items-center gap-2 ms-auto ms-lg-0">
                <?= Html::beginForm(['/produto/index'], 'get', ['class' => 'shop-search d-none d-md-flex', 'role' => 'search']) ?>
                    <i class="fas fa-search"></i>
                    <input type="search" name="q" value="<?= Html::encode((string) Yii::$app->request->get('q')) ?>"
                           placeholder="Pesquisar" aria-label="Pesquisar" autocomplete="off">
                <?= Html::endForm() ?>
                <?= Html::a('<i class="far fa-heart"></i>', ['/favorito/index'], ['class' => 'shop-icon-btn', 'aria-label' => 'Favoritos']) ?>
                <?= Html::a(
                    '<i class="fas fa-shopping-bag"></i>' . ($quantidadeCarrinho > 0 ? '<span class="shop-count">' . $quantidadeCarrinho . '</span>' : ''),
                    ['/carrinho/index'],
                    ['class' => 'shop-icon-btn position-relative', 'aria-label' => 'Carrinho']
                ) ?>
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
    <div class="offcanvas-body pt-3">

        <?= Html::beginForm(['/produto/index'], 'get', ['class' => 'shop-search w-100', 'role' => 'search']) ?>
            <i class="fas fa-search"></i>
            <input type="search" name="q" placeholder="Pesquisar" aria-label="Pesquisar" autocomplete="off" class="flex-grow-1">
        <?= Html::endForm() ?>

        <div class="shop-drawer-title">Loja</div>
        <?= Html::a('Novidades', ['/produto/index', 'novidades' => 1], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Promoções <span class="shop-badge">%</span>', ['/produto/index', 'promocao' => 1], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Homem <i class="fas fa-chevron-right small"></i>', ['/produto/index', 'genero' => 'homem'], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Mulher <i class="fas fa-chevron-right small"></i>', ['/produto/index', 'genero' => 'mulher'], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Criança <i class="fas fa-chevron-right small"></i>', ['/produto/index', 'genero' => 'crianca'], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Marcas', ['/produto/marcas'], ['class' => 'shop-drawer-link']) ?>

        <div class="shop-drawer-title">Para quê</div>
        <?= Html::a('Desporto', ['/produto/index', 'uso' => 'desporto'], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Estilo', ['/produto/index', 'uso' => 'estilo'], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Formal', ['/produto/index', 'uso' => 'formal'], ['class' => 'shop-drawer-link']) ?>

        <div class="shop-drawer-title">Explorar</div>
        <?= Html::a('Filtros <i class="fas fa-sliders-h small"></i>', ['/produto/index', 'filtros' => 1], ['class' => 'shop-drawer-link']) ?>

        <?php if (!$convidado): ?>
            <div class="shop-drawer-title">A minha conta</div>
            <?= Html::a('O meu perfil', ['/perfil/index'], ['class' => 'shop-drawer-link']) ?>
            <?= Html::a('As minhas encomendas', ['/encomenda/index'], ['class' => 'shop-drawer-link']) ?>
            <?= Html::a('Favoritos', ['/favorito/index'], ['class' => 'shop-drawer-link']) ?>
            <?= Html::a('Carrinho', ['/carrinho/index'], ['class' => 'shop-drawer-link']) ?>
        <?php endif ?>

        <div class="shop-drawer-title">Ajuda</div>
        <?= Html::a('Sobre nós', ['/site/about'], ['class' => 'shop-drawer-link']) ?>
        <?= Html::a('Contactos', ['/site/contact'], ['class' => 'shop-drawer-link']) ?>
    </div>
</div>
