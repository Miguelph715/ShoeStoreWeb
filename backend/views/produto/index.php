<?php

declare(strict_types=1);

use backend\models\ProdutoForm;
use backend\models\ProdutoSearch;
use common\models\Produto;
use yii\bootstrap4\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\ProdutoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Produtos';
$this->params['breadcrumbs'][] = $this->title;
$podeStock = Yii::$app->user->can('gerirStock');
?>
<div class="produto-index">

    <p><?= Html::a('Criar produto', ['create'], ['class' => 'btn btn-success']) ?></p>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pager' => ['class' => LinkPager::class],
                'layout' => "{items}\n<div class=\"p-3 d-flex justify-content-between align-items-center\">{summary}{pager}</div>",
                //os produtos desativados ficam a cinzento
                'rowOptions' => fn(Produto $m) => (int) $m->ativo === 0 ? ['class' => 'table-secondary text-muted'] : [],
                'columns' => [
                    'id',
                    'nome',
                    [
                        'attribute' => 'marca_id',
                        'label' => 'Marca',
                        'filter' => ProdutoForm::marcas(),
                        'value' => fn(Produto $m) => $m->marca->nome,
                    ],
                    [
                        'attribute' => 'categoria_id',
                        'label' => 'Categoria',
                        'filter' => ProdutoForm::categorias(),
                        'value' => fn(Produto $m) => $m->categoria->nome,
                    ],
                    [
                        'attribute' => 'genero',
                        'label' => 'Género',
                        'filter' => ProdutoForm::generos(),
                        'value' => fn(Produto $m) => ProdutoForm::generos()[$m->genero] ?? $m->genero,
                    ],
                    [
                        'attribute' => 'uso',
                        'label' => 'Para quê',
                        'filter' => ProdutoForm::usos(),
                        'value' => fn(Produto $m) => ProdutoForm::usos()[$m->uso] ?? $m->uso,
                    ],
                    [
                        'attribute' => 'preco',
                        'label' => 'Preço',
                        'format' => 'raw',
                        'value' => function (Produto $m) {
                            $f = Yii::$app->formatter;
                            if ($m->preco_promocional === null) {
                                return $f->asCurrency($m->preco, 'EUR');
                            }

                            return '<span class="text-danger">' . $f->asCurrency($m->preco_promocional, 'EUR') . '</span> <s class="text-muted small">' . $f->asCurrency($m->preco, 'EUR') . '</s>';
                        },
                    ],
                    [
                        'attribute' => 'stock',
                        'label' => 'Stock',
                        'filter' => ProdutoSearch::opcoesStock(),
                        'format' => 'raw',
                        'value' => function (Produto $m) {
                            $total = array_sum(array_map(fn($t) => (int) $t->stock, $m->produtoTamanhos));
                            $cor = $total === 0 ? 'danger' : ($total <= 5 ? 'warning' : 'success');

                            return '<span class="badge badge-' . $cor . '">' . $total . ($total === 0 ? ' (esgotado)' : ' pares') . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'ativo',
                        'label' => 'Na loja',
                        'filter' => [1 => 'Sim', 0 => 'Não'],
                        'value' => fn(Produto $m) => (int) $m->ativo === 1 ? 'Sim' : 'Não',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{view} {update} {stock} {desativar} {ativar} {delete}',
                        'urlCreator' => fn($action, Produto $m) => Url::to([$action, 'id' => $m->id]),
                        'contentOptions' => ['class' => 'text-nowrap'],
                        'buttons' => [
                            'view' => fn($url) => Html::a('<i class="fas fa-eye"></i>', $url, ['class' => 'btn btn-sm btn-outline-secondary', 'title' => 'Ver']),
                            'update' => fn($url) => Html::a('<i class="fas fa-pen"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary', 'title' => 'Editar']),
                            'stock' => fn($url) => Html::a('<i class="fas fa-boxes"></i>', $url, ['class' => 'btn btn-sm btn-outline-info', 'title' => 'Gerir stock']),
                            'desativar' => fn($url) => Html::a('<i class="fas fa-eye-slash"></i>', $url, [
                                'class' => 'btn btn-sm btn-outline-warning',
                                'title' => 'Desativar (tirar da loja)',
                                'data' => ['confirm' => 'Desativar este produto? Deixa de aparecer na loja.', 'method' => 'post'],
                            ]),
                            'ativar' => fn($url) => Html::a('<i class="fas fa-check"></i>', $url, ['class' => 'btn btn-sm btn-outline-success', 'title' => 'Ativar (voltar à loja)', 'data' => ['method' => 'post']]),
                            'delete' => fn($url) => Html::a('<i class="fas fa-trash"></i>', $url, [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'title' => 'Eliminar',
                                'data' => ['confirm' => 'Eliminar este produto de vez? Só é possível se nunca foi encomendado.', 'method' => 'post'],
                            ]),
                        ],
                        'visibleButtons' => [
                            'stock' => $podeStock,
                            'desativar' => fn(Produto $m) => (int) $m->ativo === 1,
                            'ativar' => fn(Produto $m) => (int) $m->ativo === 0,
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
