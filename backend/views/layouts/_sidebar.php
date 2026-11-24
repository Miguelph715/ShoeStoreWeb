<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use hail812\adminlte\widgets\Menu;
use yii\helpers\Html;
use yii\helpers\Url;

$roles = array_keys(Yii::$app->authManager->getRolesByUser((int) Yii::$app->user->id));
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= Url::to(['/site/index']) ?>" class="brand-link">
        <i class="fas fa-shoe-prints brand-icon"></i>
        <span class="brand-text">ShoeStore</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <span class="d-block text-white"><?= Html::encode(Yii::$app->user->identity?->username) ?></span>
                <small class="text-muted"><?= Html::encode($roles[0] ?? '') ?></small>
            </div>
        </div>

        <nav class="mt-2">
            <?php
            $itens = [
                ['label' => 'Painel', 'icon' => 'tachometer-alt', 'url' => ['/site/index']],
            ];

            //o catalogo e as vendas: gestor e admin
            if (Yii::$app->user->can('verProdutos')) {
                $itens[] = ['label' => 'Catálogo', 'header' => true];
                $itens[] = ['label' => 'Produtos', 'icon' => 'shoe-prints', 'url' => ['/produto/index']];
            }
            if (Yii::$app->user->can('gerirEncomendas')) {
                $itens[] = ['label' => 'Vendas', 'header' => true];
                $itens[] = ['label' => 'Encomendas', 'icon' => 'box-open', 'url' => ['/encomenda/index']];
            }

            //so aparece a quem tem a permissao (o admin)
            if (Yii::$app->user->can('verUtilizadores')) {
                $itens[] = ['label' => 'Administração', 'header' => true];
                $itens[] = ['label' => 'Utilizadores', 'icon' => 'users', 'url' => ['/user/index']];
            }

            echo Menu::widget(['items' => $itens]);
            ?>
        </nav>
    </div>
</aside>