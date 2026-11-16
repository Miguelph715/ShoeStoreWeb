<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\Produto $produto */
/** @var common\models\ProdutoTamanho[] $tamanhos */
/** @var common\models\Produto[] $parecidos */
/** @var int[] $favoritos */

use frontend\components\Loja;
use yii\helpers\Html;

$this->title = $produto->nome;
$this->params['breadcrumbs'][] = ['label' => 'Produtos', 'url' => ['/produto/index']];
$this->params['breadcrumbs'][] = ['label' => $produto->categoria->nome, 'url' => ['/produto/index', 'categoria' => $produto->categoria_id]];
$this->params['breadcrumbs'][] = $produto->nome;

$convidado = Yii::$app->user->isGuest;
$emPromocao = Loja::emPromocao($produto);
$favorito = in_array($produto->id, $favoritos, true);
$generos = ['homem' => 'Homem', 'mulher' => 'Mulher', 'crianca' => 'Criança', 'unissexo' => 'Unissexo'];
$stockTotal = Loja::stockTotal($produto);
$temStock = $stockTotal > 0;
?>
<div class="site-produto">
    <div class="row g-4 g-lg-5">

        <!-- Imagem (por agora, a sapatilha) -->
        <div class="col-md-6">
            <div class="product-hero">
                <i class="fas fa-shoe-prints"></i>
                <?php if ($emPromocao): ?>
                    <span class="shop-badge product-badge">-<?= Loja::desconto($produto) ?>%</span>
                <?php endif ?>
            </div>
        </div>

        <!-- Informacao -->
        <div class="col-md-6">
            <div class="text-secondary mb-1">
                <?= Html::a(Html::encode($produto->marca->nome), ['/produto/index', 'marca' => $produto->marca_id], ['class' => 'text-secondary']) ?>
                &middot; <?= Html::encode($generos[$produto->genero] ?? '') ?>
            </div>
            <h1 class="shop-display mb-3"><?= Html::encode($produto->nome) ?></h1>

            <div class="fs-3 mb-3">
                <span class="product-price"><?= Loja::euros(Loja::precoAtual($produto)) ?></span>
                <?php if ($emPromocao): ?>
                    <span class="product-old-price fs-6"><?= Loja::euros((float) $produto->preco) ?></span>
                <?php endif ?>
            </div>

            <p class="text-secondary"><?= Html::encode((string) $produto->descricao) ?></p>

            <?= Html::beginForm(['/carrinho/adicionar'], 'post') ?>
                <?= Html::hiddenInput('produto_id', (string) $produto->id) ?>

                <!-- Tamanhos -->
                <div class="mb-4">
                    <div class="fw-semibold mb-2">Tamanho</div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($tamanhos as $pt): ?>
                            <?php $esgotado = (int) $pt->stock <= 0; ?>
                            <input type="radio" class="btn-check" name="tamanho_id" id="t<?= $pt->tamanho_id ?>"
                                   value="<?= $pt->tamanho_id ?>" <?= $esgotado ? 'disabled' : 'required' ?>>
                            <label class="btn btn-outline-light size-pill" for="t<?= $pt->tamanho_id ?>"
                                   title="<?= $esgotado ? 'Esgotado' : ((int) $pt->stock) . ' em stock' ?>">
                                <?= Html::encode(rtrim(rtrim(number_format((float) $pt->tamanho->numero, 1, '.', ''), '0'), '.')) ?>
                                <small class="d-block size-stock <?= $esgotado ? 'stock-out' : ((int) $pt->stock <= 3 ? 'stock-low' : '') ?>"><?= $esgotado ? 'esgotado' : (int) $pt->stock . ' un.' ?></small>
                            </label>
                        <?php endforeach ?>
                    </div>
                    <div class="small mt-2 <?= $temStock ? 'text-secondary' : 'stock-out' ?>">
                        <?= $temStock ? 'Em stock: ' . $stockTotal . ' ' . ($stockTotal === 1 ? 'par' : 'pares') . ' (os tamanhos riscados estão esgotados).' : 'Produto esgotado.' ?>
                    </div>
                </div>

                <!-- Cores -->
                <div class="mb-4">
                    <div class="fw-semibold mb-2">Cor</div>
                    <div class="d-flex flex-wrap gap-3">
                        <?php foreach ($produto->cors as $i => $cor): ?>
                            <div>
                                <input type="radio" class="btn-check" name="cor_id" id="c<?= $cor->id ?>" value="<?= $cor->id ?>"
                                       <?= $i === 0 ? 'checked' : '' ?> required>
                                <label class="color-swatch" for="c<?= $cor->id ?>" title="<?= Html::encode($cor->nome) ?>">
                                    <span style="background: <?= Html::encode($cor->codigo_hex) ?>"></span>
                                </label>
                                <div class="small text-secondary text-center"><?= Html::encode($cor->nome) ?></div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>

                <!-- Quantidade e botoes -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div style="width: 6.5rem">
                        <?= Html::input('number', 'quantidade', '1', ['class' => 'form-control', 'min' => 1, 'max' => 10, 'aria-label' => 'Quantidade']) ?>
                    </div>
                    <?php if ($convidado): ?>
                        <?= Html::a('Entra para comprar', ['/site/login'], ['class' => 'btn btn-shoe btn-lg flex-grow-1']) ?>
                    <?php elseif (!$temStock): ?>
                        <button type="button" class="btn btn-secondary btn-lg flex-grow-1" disabled>Esgotado</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-shoe btn-lg flex-grow-1"><i class="fas fa-shopping-bag me-2"></i>Adicionar ao carrinho</button>
                    <?php endif ?>
                </div>
            <?= Html::endForm() ?>

            <!-- Favorito -->
            <?= Html::beginForm(['/favorito/alternar'], 'post', ['class' => 'mt-3']) ?>
                <?= Html::hiddenInput('produto_id', (string) $produto->id) ?>
                <button type="submit" class="btn btn-outline-light shop-pill">
                    <i class="<?= $favorito ? 'fas' : 'far' ?> fa-heart me-2"></i><?= $favorito ? 'Nos favoritos' : 'Adicionar aos favoritos' ?>
                </button>
            <?= Html::endForm() ?>

            <ul class="list-unstyled text-secondary small mt-4 mb-0">
                <li><i class="fas fa-truck me-2" style="color: var(--shoe-accent)"></i>Entrega em 2 a 4 dias úteis</li>
                <li><i class="fas fa-undo me-2" style="color: var(--shoe-accent)"></i>30 dias para trocar</li>
            </ul>
        </div>
    </div>

    <?php if ($parecidos): ?>
        <h2 class="shop-display shop-section-title mt-5">Também te pode interessar</h2>
        <div class="row g-3 g-md-4">
            <?php foreach ($parecidos as $p): ?>
                <div class="col-6 col-lg-3">
                    <?= $this->render('_card', ['p' => $p, 'favoritos' => $favoritos]) ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>
