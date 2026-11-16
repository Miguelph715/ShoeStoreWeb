<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use frontend\components\Loja;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;

$this->title = 'As minhas encomendas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-encomendas">
    <h1 class="shop-display mb-4">As minhas encomendas</h1>

    <?php if ($dataProvider->totalCount === 0): ?>
        <div class="shop-panel text-center py-5">
            <i class="fas fa-box-open fs-1 mb-3" style="color: var(--shoe-accent)"></i>
            <h2 class="h5">Ainda não fizeste nenhuma encomenda</h2>
            <p class="text-secondary">Quando comprares, as tuas encomendas aparecem aqui.</p>
            <?= Html::a('Ver novidades', ['/produto/index', 'novidades' => 1], ['class' => 'btn btn-shoe shop-pill']) ?>
        </div>
    <?php else: ?>
        <div class="table-responsive shop-panel p-0">
            <table class="table align-middle mb-0">
                <thead>
                <tr><th>Nº</th><th>Data</th><th>Estado</th><th class="text-end">Total</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($dataProvider->getModels() as $e): ?>
                    <tr>
                        <td>#<?= $e->id ?></td>
                        <td><?= date('d/m/Y H:i', (int) $e->created_at) ?></td>
                        <td><span class="badge text-bg-<?= Loja::estadoClasse($e->estado) ?>"><?= Html::encode($e->estado) ?></span></td>
                        <td class="text-end"><?= Loja::euros((float) $e->total) ?></td>
                        <td class="text-end"><?= Html::a('Ver', ['view', 'id' => $e->id], ['class' => 'btn btn-sm btn-outline-light']) ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            <?= LinkPager::widget(['pagination' => $dataProvider->pagination, 'firstPageLabel' => false, 'lastPageLabel' => false]) ?>
        </div>
    <?php endif ?>
</div>
