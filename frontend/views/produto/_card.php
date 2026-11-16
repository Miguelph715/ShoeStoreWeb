<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\Produto $p */
/** @var int[] $favoritos */

use frontend\components\Loja;
use yii\helpers\Html;
use yii\helpers\Url;

$url = Url::to(['/produto/view', 'id' => $p->id]);
$emPromocao = Loja::emPromocao($p);
$favorito = in_array($p->id, $favoritos ?? [], true);
?>
<div class="product-card h-100 position-relative">
    <a class="product-img" href="<?= $url ?>" aria-label="Ver <?= Html::encode($p->nome) ?>">
        <i class="fas fa-shoe-prints"></i>
        <?php if ($emPromocao): ?>
            <span class="shop-badge product-badge">-<?= Loja::desconto($p) ?>%</span>
        <?php endif ?>
    </a>

    <?= Html::beginForm(['/favorito/alternar'], 'post', ['class' => 'product-fav']) ?>
        <?= Html::hiddenInput('produto_id', (string) $p->id) ?>
        <button type="submit" class="shop-icon-btn" aria-label="<?= $favorito ? 'Tirar dos favoritos' : 'Pôr nos favoritos' ?>">
            <i class="<?= $favorito ? 'fas' : 'far' ?> fa-heart"></i>
        </button>
    <?= Html::endForm() ?>

    <div class="p-3">
        <div class="small text-secondary"><?= Html::encode($p->marca->nome) ?></div>
        <a class="fw-semibold text-white text-decoration-none" href="<?= $url ?>"><?= Html::encode($p->nome) ?></a>
        <div class="mt-1">
            <span class="product-price"><?= Loja::euros(Loja::precoAtual($p)) ?></span>
            <?php if ($emPromocao): ?>
                <span class="product-old-price"><?= Loja::euros((float) $p->preco) ?></span>
            <?php endif ?>
        </div>
    </div>
</div>
