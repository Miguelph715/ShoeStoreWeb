<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $marcas */

use yii\helpers\Html;

$this->title = 'Marcas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-marcas">
    <h1 class="shop-display mb-4">Marcas</h1>

    <div class="row g-3 g-md-4">
        <?php foreach ($marcas as $m): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <?= Html::a(
                    '<div class="brand-name">' . Html::encode($m['nome']) . '</div>'
                    . '<div class="small text-secondary">' . (int) $m['n'] . ' ' . ((int) $m['n'] === 1 ? 'produto' : 'produtos') . '</div>',
                    ['/produto/index', 'marca' => $m['id']],
                    ['class' => 'brand-card']
                ) ?>
            </div>
        <?php endforeach ?>
    </div>
</div>
