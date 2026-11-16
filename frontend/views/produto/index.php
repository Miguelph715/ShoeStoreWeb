<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var frontend\models\ProdutoFiltro $filtro */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int[] $favoritos */

use yii\bootstrap5\LinkPager;
use yii\helpers\Html;

$this->title = $filtro->titulo();
$this->params['breadcrumbs'][] = $this->title;
//?filtros=1 (o "Filtros" do menu lateral) abre o painel de filtros logo no telemovel
$abrirFiltros = (bool) Yii::$app->request->get('filtros');
?>
<div class="site-catalogo">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="shop-display mb-1"><?= Html::encode($this->title) ?></h1>
            <div class="text-secondary"><?= $dataProvider->totalCount ?> <?= $dataProvider->totalCount === 1 ? 'produto' : 'produtos' ?></div>
        </div>
        <button class="btn btn-outline-light shop-pill d-lg-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#painel-filtros" aria-expanded="<?= $abrirFiltros ? 'true' : 'false' ?>" aria-controls="painel-filtros">
            <i class="fas fa-sliders-h"></i> Filtros
        </button>
    </div>

    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="collapse d-lg-block <?= $abrirFiltros ? 'show' : '' ?>" id="painel-filtros">
                <div class="shop-panel">
                    <?= $this->render('_filtros', ['filtro' => $filtro]) ?>
                </div>
            </div>
        </aside>

        <div class="col-lg-9">
            <?php if ($dataProvider->totalCount === 0): ?>
                <div class="shop-panel text-center py-5">
                    <i class="fas fa-search fs-1 mb-3" style="color: var(--shoe-accent)"></i>
                    <h2 class="h5">Não encontrámos produtos</h2>
                    <p class="text-secondary">Experimenta tirar alguns filtros ou pesquisar outra coisa.</p>
                    <?= Html::a('Ver todos os produtos', ['/produto/index'], ['class' => 'btn btn-shoe shop-pill']) ?>
                </div>
            <?php else: ?>
                <div class="row g-3 g-md-4">
                    <?php foreach ($dataProvider->getModels() as $p): ?>
                        <div class="col-6 col-md-4">
                            <?= $this->render('_card', ['p' => $p, 'favoritos' => $favoritos]) ?>
                        </div>
                    <?php endforeach ?>
                </div>

                <div class="d-flex justify-content-center mt-5">
                    <?= LinkPager::widget([
                        'pagination' => $dataProvider->pagination,
                        'firstPageLabel' => false,
                        'lastPageLabel' => false,
                        'prevPageLabel' => '&laquo;',
                        'nextPageLabel' => '&raquo;',
                    ]) ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
