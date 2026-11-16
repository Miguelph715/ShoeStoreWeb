<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\Produto[] $produtos */
/** @var int[] $favoritos */

use yii\helpers\Html;

$this->title = 'Favoritos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-favoritos">
    <h1 class="shop-display mb-4">Os teus favoritos</h1>

    <?php if (!$produtos): ?>
        <div class="shop-panel text-center py-5">
            <i class="far fa-heart fs-1 mb-3" style="color: var(--shoe-accent)"></i>
            <h2 class="h5">Ainda não tens favoritos</h2>
            <p class="text-secondary">Carrega no coração de um produto para o guardares aqui.</p>
            <?= Html::a('Ver novidades', ['/produto/index', 'novidades' => 1], ['class' => 'btn btn-shoe shop-pill']) ?>
        </div>
    <?php else: ?>
        <div class="row g-3 g-md-4">
            <?php foreach ($produtos as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <?= $this->render('/produto/_card', ['p' => $p, 'favoritos' => $favoritos]) ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>
