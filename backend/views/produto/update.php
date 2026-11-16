<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var backend\models\ProdutoForm $model */

$this->title = 'Editar produto';
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->produto->nome, 'url' => ['view', 'id' => $model->produto->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
