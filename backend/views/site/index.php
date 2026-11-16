<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array $estatisticas */
/** @var common\models\Encomenda[] $ultimas */
/** @var common\models\Produto[] $poucoStock */

use common\components\Encomendas;
use yii\helpers\Html;

$this->title = 'Painel';
$f = Yii::$app->formatter;
$caixas = [
    ['Produtos na loja', $estatisticas['produtos'], 'shoe-prints', 'info', ['/produto/index']],
    ['Encomendas pendentes', $estatisticas['pendentes'], 'clock', 'warning', ['/encomenda/index', 'EncomendaSearch[estado]' => 'Pendente']],
    ['Esgotados', $estatisticas['esgotados'], 'ban', 'danger', ['/produto/index', 'ProdutoSearch[stock]' => 'esgotado']],
    ['Stock baixo (até 5)', $estatisticas['stockBaixo'], 'exclamation-triangle', 'secondary', ['/produto/index', 'ProdutoSearch[stock]' => 'baixo']],
];
?>
<div class="site-index">

    <div class="row">
        <?php foreach ($caixas as [$titulo, $valor, $icone, $cor, $url]): ?>
            <div class="col-6 col-lg-3">
                <div class="small-box bg-<?= $cor ?>">
                    <div class="inner">
                        <h3><?= (int) $valor ?></h3>
                        <p><?= Html::encode($titulo) ?></p>
                    </div>
                    <div class="icon"><i class="fas fa-<?= $icone ?>"></i></div>
                    <?= Html::a('Ver <i class="fas fa-arrow-circle-right"></i>', $url, ['class' => 'small-box-footer']) ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-euro-sign"></i></span>
                <div class="info-box-content"><span class="info-box-text">Vendas (sem canceladas)</span><span class="info-box-number"><?= $f->asCurrency($estatisticas['vendas'], 'EUR') ?></span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-box-open"></i></span>
                <div class="info-box-content"><span class="info-box-text">Encomendas no total</span><span class="info-box-number"><?= (int) $estatisticas['encomendas'] ?></span></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-dark"><i class="fas fa-users"></i></span>
                <div class="info-box-content"><span class="info-box-text">Clientes</span><span class="info-box-number"><?= (int) $estatisticas['clientes'] ?></span></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><strong>Últimas encomendas</strong></div>
                <div class="card-body table-responsive p-0">
                    <?php if (!$ultimas): ?>
                        <p class="p-3 mb-0 text-muted">Ainda não há encomendas.</p>
                    <?php else: ?>
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Nº</th><th>Cliente</th><th>Data</th><th>Estado</th><th class="text-right">Total</th></tr></thead>
                            <tbody>
                            <?php foreach ($ultimas as $e): ?>
                                <tr>
                                    <td><?= Html::a('#' . $e->id, ['/encomenda/view', 'id' => $e->id]) ?></td>
                                    <td><?= Html::encode($e->user->username) ?></td>
                                    <td><?= $f->asDatetime($e->created_at) ?></td>
                                    <td><span class="badge badge-<?= Encomendas::classe($e->estado) ?>"><?= Html::encode($e->estado) ?></span></td>
                                    <td class="text-right"><?= $f->asCurrency($e->total, 'EUR') ?></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><strong>Para repor (stock até 5 pares)</strong></div>
                <div class="card-body table-responsive p-0">
                    <?php if (!$poucoStock): ?>
                        <p class="p-3 mb-0 text-muted">Todos os produtos têm stock.</p>
                    <?php else: ?>
                        <table class="table table-sm mb-0">
                            <tbody>
                            <?php foreach ($poucoStock as $p): ?>
                                <?php $total = array_sum(array_map(fn($t) => (int) $t->stock, $p->produtoTamanhos)); ?>
                                <tr>
                                    <td><?= Html::a(Html::encode($p->nome), ['/produto/view', 'id' => $p->id]) ?></td>
                                    <td class="text-right"><span class="badge badge-<?= $total === 0 ? 'danger' : 'warning' ?>"><?= $total === 0 ? 'esgotado' : $total . ' pares' ?></span></td>
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
