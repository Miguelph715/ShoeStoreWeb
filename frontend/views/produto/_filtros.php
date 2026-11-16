<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var frontend\models\ProdutoFiltro $filtro */

use frontend\components\Loja;
use frontend\models\ProdutoFiltro;
use yii\helpers\Html;
?>
<?= Html::beginForm(['/produto/index'], 'get', ['class' => 'shop-filters']) ?>
    <div class="mb-3">
        <label class="form-label small" for="f-q">Pesquisar</label>
        <?= Html::input('search', 'q', $filtro->q, ['class' => 'form-control', 'id' => 'f-q', 'placeholder' => 'Nome, marca ou categoria']) ?>
    </div>

    <div class="mb-3">
        <label class="form-label small" for="f-genero">Género</label>
        <?= Html::dropDownList('genero', $filtro->genero, Loja::generos(), ['class' => 'form-select', 'id' => 'f-genero', 'prompt' => 'Todos']) ?>
    </div>

    <div class="mb-3">
        <label class="form-label small" for="f-uso">Para quê</label>
        <?= Html::dropDownList('uso', $filtro->uso, Loja::usos(), ['class' => 'form-select', 'id' => 'f-uso', 'prompt' => 'Todos']) ?>
    </div>

    <div class="mb-3">
        <label class="form-label small" for="f-categoria">Categoria</label>
        <?= Html::dropDownList('categoria', $filtro->categoria, ProdutoFiltro::categoriasLista(), ['class' => 'form-select', 'id' => 'f-categoria', 'prompt' => 'Todas']) ?>
    </div>

    <div class="mb-3">
        <label class="form-label small" for="f-marca">Marca</label>
        <?= Html::dropDownList('marca', $filtro->marca, ProdutoFiltro::marcasLista(), ['class' => 'form-select', 'id' => 'f-marca', 'prompt' => 'Todas']) ?>
    </div>

    <div class="mb-3">
        <label class="form-label small">Preço (€)</label>
        <div class="d-flex gap-2">
            <?= Html::input('number', 'preco_min', $filtro->preco_min, ['class' => 'form-control', 'placeholder' => 'De', 'min' => 0, 'step' => '0.01', 'aria-label' => 'Preço mínimo']) ?>
            <?= Html::input('number', 'preco_max', $filtro->preco_max, ['class' => 'form-control', 'placeholder' => 'Até', 'min' => 0, 'step' => '0.01', 'aria-label' => 'Preço máximo']) ?>
        </div>
    </div>

    <div class="form-check mb-3">
        <?= Html::checkbox('promocao', !empty($filtro->promocao), ['class' => 'form-check-input', 'id' => 'f-promocao', 'value' => 1, 'uncheck' => null]) ?>
        <label class="form-check-label" for="f-promocao">Só promoções</label>
    </div>

    <div class="mb-4">
        <label class="form-label small" for="f-ordem">Ordenar por</label>
        <?= Html::dropDownList('ordem', $filtro->ordem, ProdutoFiltro::ordens(), ['class' => 'form-select', 'id' => 'f-ordem']) ?>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-shoe">Aplicar filtros</button>
        <?= Html::a('Limpar', ['/produto/index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
<?= Html::endForm() ?>
