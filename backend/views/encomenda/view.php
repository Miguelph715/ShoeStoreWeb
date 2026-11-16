<?php

declare(strict_types=1);

use common\components\Encomendas;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Encomenda $model */

$this->title = 'Encomenda #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Encomendas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$f = Yii::$app->formatter;
$seguintes = Encomendas::seguintes($model->estado);
$tam = fn($t) => rtrim(rtrim(number_format((float) $t->numero, 1, '.', ''), '0'), '.');
?>
<div class="encomenda-view">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><strong>Produtos</strong></div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Produto</th><th>Tamanho</th><th>Cor</th><th class="text-right">Preço</th><th class="text-right">Qtd.</th><th class="text-right">Subtotal</th></tr></thead>
                        <tbody>
                        <?php foreach ($model->linhaEncomendas as $l): ?>
                            <tr>
                                <td><?= Html::a(Html::encode($l->produto->nome), ['/produto/view', 'id' => $l->produto_id]) ?></td>
                                <td><?= Html::encode($tam($l->tamanho)) ?></td>
                                <td><?= Html::encode($l->cor->nome) ?></td>
                                <td class="text-right"><?= $f->asCurrency($l->preco_unitario, 'EUR') ?></td>
                                <td class="text-right"><?= (int) $l->quantidade ?></td>
                                <td class="text-right"><?= $f->asCurrency((float) $l->preco_unitario * (int) $l->quantidade, 'EUR') ?></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                        <tfoot><tr><th colspan="5" class="text-right">Total</th><th class="text-right"><?= $f->asCurrency($model->total, 'EUR') ?></th></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><strong>Encomenda</strong>
                    <span class="badge badge-<?= Encomendas::classe($model->estado) ?> float-right"><?= Html::encode($model->estado) ?></span>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Cliente</dt><dd><?= Html::encode($model->user->username) ?></dd>
                        <dt>Email</dt><dd><?= Html::encode($model->email) ?></dd>
                        <dt>Morada de envio</dt><dd><?= Html::encode($model->morada_envio) ?></dd>
                        <dt>Pagamento</dt><dd><?= Html::encode($model->metodoPagamento->nome) ?></dd>
                        <dt>Criada em</dt><dd><?= $f->asDatetime($model->created_at) ?></dd>
                        <dt>Última alteração</dt><dd class="mb-0"><?= $f->asDatetime($model->updated_at) ?></dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><strong>Mudar o estado</strong></div>
                <div class="card-body">
                    <?php if (!$seguintes): ?>
                        <p class="text-muted mb-0">Esta encomenda está "<?= Html::encode($model->estado) ?>": já não se pode alterar.</p>
                    <?php else: ?>
                        <?php foreach ($seguintes as $novo): ?>
                            <?= Html::beginForm(['estado', 'id' => $model->id], 'post', ['class' => 'mb-2']) ?>
                                <?= Html::hiddenInput('estado', $novo) ?>
                                <button type="submit" class="btn btn-<?= $novo === 'Cancelada' ? 'outline-danger' : 'primary' ?> btn-block"
                                    <?= $novo === 'Cancelada' ? 'data-confirm="Cancelar esta encomenda? O stock volta a ficar disponível."' : '' ?>>
                                    <?= $novo === 'Cancelada' ? 'Cancelar encomenda' : 'Marcar como ' . Html::encode($novo) ?>
                                </button>
                            <?= Html::endForm() ?>
                        <?php endforeach ?>
                        <p class="small text-muted mb-0">Fluxo: Pendente, Confirmada, Enviada, Entregue. Só se cancela até estar Confirmada.</p>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
