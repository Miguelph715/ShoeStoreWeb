<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var backend\models\ProdutoForm $model */

$this->title = 'Criar produto';
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="produto-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
