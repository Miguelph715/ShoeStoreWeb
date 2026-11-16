<?php

declare(strict_types=1);

use backend\models\EncomendaSearch;
use common\components\Encomendas;
use common\models\Encomenda;
use yii\bootstrap4\LinkPager;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\EncomendaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Encomendas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="encomenda-index">
    <div class="card">
        <div class="card-body table-responsive p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pager' => ['class' => LinkPager::class],
                'layout' => "{items}\n<div class=\"p-3 d-flex justify-content-between align-items-center\">{summary}{pager}</div>",
                'columns' => [
                    ['attribute' => 'id', 'label' => 'Nº', 'value' => fn(Encomenda $m) => '#' . $m->id],
                    [
                        'attribute' => 'cliente',
                        'label' => 'Cliente',
                        'value' => fn(Encomenda $m) => $m->user->username,
                    ],
                    ['attribute' => 'email', 'label' => 'Email', 'filter' => false],
                    [
                        'attribute' => 'created_at',
                        'label' => 'Data',
                        'filter' => false,
                        'format' => 'datetime',
                    ],
                    [
                        'attribute' => 'total',
                        'filter' => false,
                        'format' => ['currency', 'EUR'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'estado',
                        'filter' => EncomendaSearch::estados(),
                        'format' => 'raw',
                        'value' => fn(Encomenda $m) => '<span class="badge badge-' . Encomendas::classe($m->estado) . '">' . Html::encode($m->estado) . '</span>',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{view}',
                        'urlCreator' => fn($action, Encomenda $m) => Url::to([$action, 'id' => $m->id]),
                        'buttons' => ['view' => fn($url) => Html::a('Ver', $url, ['class' => 'btn btn-sm btn-outline-secondary'])],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
