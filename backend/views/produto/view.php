<?php

declare(strict_types=1);

use backend\models\ProdutoForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Produto $model */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$f = Yii::$app->formatter;
$tamanhos = $model->produtoTamanhos;
usort($tamanhos, fn($a, $b) => (float) $a->tamanho->numero <=> (float) $b->tamanho->numero);
$total = array_sum(array_map(fn($t) => (int) $t->stock, $tamanhos));
?>
<div class="produto-view">

    <p>
        <?php if (Yii::$app->user->can('editarProduto')): ?>
            <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('gerirStock')): ?>
            <?= Html::a('Gerir stock', ['stock', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('editarProduto')): ?>
            <?php if ((int) $model->ativo === 1): ?>
                <?= Html::a('Desativar', ['desativar', 'id' => $model->id], ['class' => 'btn btn-outline-warning', 'data' => ['confirm' => 'Desativar este produto? Deixa de aparecer na loja.', 'method' => 'post']]) ?>
            <?php else: ?>
                <?= Html::a('Ativar', ['ativar', 'id' => $model->id], ['class' => 'btn btn-outline-success', 'data' => ['method' => 'post']]) ?>
            <?php endif ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('eliminarProduto')): ?>
            <?= Html::a('Eliminar', ['delete', 'id' => $model->id], ['class' => 'btn btn-outline-danger', 'data' => ['confirm' => 'Eliminar este produto de vez? Só é possível se nunca foi encomendado.', 'method' => 'post']]) ?>
        <?php endif ?>
    </p>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><th style="width: 11rem">Nome</th><td><?= Html::encode($model->nome) ?></td></tr>
                        <tr><th>Marca</th><td><?= Html::encode($model->marca->nome) ?></td></tr>
                        <tr><th>Categoria</th><td><?= Html::encode($model->categoria->nome) ?></td></tr>
                        <tr><th>Género</th><td><?= Html::encode(ProdutoForm::generos()[$model->genero] ?? $model->genero) ?></td></tr>
                        <tr><th>Para quê</th><td><?= Html::encode(ProdutoForm::usos()[$model->uso] ?? $model->uso) ?></td></tr>
                        <tr><th>Preço</th><td><?= $f->asCurrency($model->preco, 'EUR') ?></td></tr>
                        <tr><th>Preço promocional</th><td><?= $model->preco_promocional === null ? '<span class="text-muted">Sem promoção</span>' : $f->asCurrency($model->preco_promocional, 'EUR') ?></td></tr>
                        <tr><th>Na loja</th><td><?= (int) $model->ativo === 1 ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não (desativado)</span>' ?></td></tr>
                        <tr><th>Cores</th><td><?= $model->cors ? Html::encode(implode(', ', array_map(fn($c) => $c->nome, $model->cors))) : '<span class="text-muted">Nenhuma</span>' ?></td></tr>
                        <tr><th>Imagem</th><td><?= $model->imagem ? Html::a(Html::encode($model->imagem), $model->imagem, ['target' => '_blank', 'rel' => 'noopener']) : '<span class="text-muted">Sem imagem</span>' ?></td></tr>
                        <tr><th>Descrição</th><td><?= nl2br(Html::encode((string) $model->descricao)) ?></td></tr>
                        <tr><th>Criado em</th><td><?= $f->asDatetime($model->created_at) ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><strong>Stock por tamanho</strong> <span class="float-right text-muted"><?= $total ?> pares</span></div>
                <div class="card-body p-0">
                    <?php if (!$tamanhos): ?>
                        <p class="p-3 mb-0 text-muted">Ainda não há tamanhos. Usa "Gerir stock" para os acrescentar.</p>
                    <?php else: ?>
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Tamanho</th><th class="text-right">Stock</th></tr></thead>
                            <tbody>
                            <?php foreach ($tamanhos as $pt): ?>
                                <?php $s = (int) $pt->stock; ?>
                                <tr>
                                    <td><?= Html::encode(rtrim(rtrim(number_format((float) $pt->tamanho->numero, 1, '.', ''), '0'), '.')) ?></td>
                                    <td class="text-right"><span class="badge badge-<?= $s === 0 ? 'danger' : ($s <= 3 ? 'warning' : 'success') ?>"><?= $s ?></span></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
