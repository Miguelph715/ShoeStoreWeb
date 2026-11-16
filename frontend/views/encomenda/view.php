<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\Encomenda $encomenda */

use frontend\components\Loja;
use yii\helpers\Html;

$this->title = 'Encomenda #' . $encomenda->id;
$this->params['breadcrumbs'][] = ['label' => 'As minhas encomendas', 'url' => ['/encomenda/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-encomenda">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <h1 class="shop-display mb-0">Encomenda #<?= $encomenda->id ?></h1>
        <span class="badge fs-6 text-bg-<?= Loja::estadoClasse($encomenda->estado) ?>"><?= Html::encode($encomenda->estado) ?></span>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="table-responsive shop-panel p-0">
                <table class="table align-middle mb-0">
                    <thead>
                    <tr><th>Produto</th><th>Tamanho</th><th>Cor</th><th class="text-end">Preço</th><th class="text-end">Qtd.</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($encomenda->linhaEncomendas as $l): ?>
                        <tr>
                            <td><?= Html::a(Html::encode($l->produto->nome), ['/produto/view', 'id' => $l->produto_id], ['class' => 'text-white']) ?></td>
                            <td><?= Html::encode(rtrim(rtrim(number_format((float) $l->tamanho->numero, 1, '.', ''), '0'), '.')) ?></td>
                            <td><?= Html::encode($l->cor->nome) ?></td>
                            <td class="text-end"><?= Loja::euros((float) $l->preco_unitario) ?></td>
                            <td class="text-end"><?= (int) $l->quantidade ?></td>
                            <td class="text-end"><?= Loja::euros((float) $l->preco_unitario * (int) $l->quantidade) ?></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="shop-panel">
                <h2 class="h5 mb-3">Resumo</h2>
                <dl class="mb-0 small">
                    <dt class="text-secondary">Data</dt><dd><?= date('d/m/Y H:i', (int) $encomenda->created_at) ?></dd>
                    <dt class="text-secondary">Email</dt><dd><?= Html::encode($encomenda->email) ?></dd>
                    <dt class="text-secondary">Morada de envio</dt><dd><?= Html::encode($encomenda->morada_envio) ?></dd>
                    <dt class="text-secondary">Pagamento</dt><dd><?= Html::encode($encomenda->metodoPagamento->nome) ?></dd>
                </dl>
                <div class="d-flex justify-content-between fs-5 fw-semibold border-top border-secondary-subtle pt-3 mt-3">
                    <span>Total</span><span class="product-price"><?= Loja::euros((float) $encomenda->total) ?></span>
                </div>

                <?php if ($encomenda->estado === 'Pendente'): ?>
                    <?= Html::beginForm(['/encomenda/cancelar', 'id' => $encomenda->id], 'post', ['class' => 'mt-3']) ?>
                        <button type="submit" class="btn btn-outline-danger w-100"
                                data-confirm="Cancelar esta encomenda? O stock volta a ficar disponível.">Cancelar encomenda</button>
                    <?= Html::endForm() ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
