<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\Perfil $perfil */

use yii\helpers\Html;

$this->title = 'O meu perfil';
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user->identity;
$vazio = '<span class="text-secondary">Por preencher</span>';
?>
<div class="site-perfil">
    <h1 class="shop-display mb-4">O meu perfil</h1>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="shop-panel">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-secondary">Nome</dt><dd class="col-sm-8"><?= Html::encode($perfil->nome) ?></dd>
                    <dt class="col-sm-4 text-secondary">Nome de utilizador</dt><dd class="col-sm-8"><?= Html::encode($user->username) ?></dd>
                    <dt class="col-sm-4 text-secondary">Email</dt><dd class="col-sm-8"><?= Html::encode($user->email) ?></dd>
                    <dt class="col-sm-4 text-secondary">Telefone</dt><dd class="col-sm-8"><?= $perfil->telefone ? Html::encode($perfil->telefone) : $vazio ?></dd>
                    <dt class="col-sm-4 text-secondary">Morada</dt><dd class="col-sm-8"><?= $perfil->morada ? Html::encode($perfil->morada) : $vazio ?></dd>
                    <dt class="col-sm-4 text-secondary">Código postal</dt><dd class="col-sm-8"><?= $perfil->codigo_postal ? Html::encode($perfil->codigo_postal) : $vazio ?></dd>
                    <dt class="col-sm-4 text-secondary">Cidade</dt><dd class="col-sm-8"><?= $perfil->cidade ? Html::encode($perfil->cidade) : $vazio ?></dd>
                    <dt class="col-sm-4 text-secondary">NIF</dt><dd class="col-sm-8 mb-0"><?= $perfil->nif ? Html::encode($perfil->nif) : $vazio ?></dd>
                </dl>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <?= Html::a('Editar perfil', ['editar'], ['class' => 'btn btn-shoe shop-pill']) ?>
                <?= Html::a('Alterar password', ['password'], ['class' => 'btn btn-outline-light shop-pill']) ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="shop-panel">
                <h2 class="h5 mb-3">A minha conta</h2>
                <?= Html::a('<i class="fas fa-box-open me-2"></i>As minhas encomendas', ['/encomenda/index'], ['class' => 'shop-drawer-link']) ?>
                <?= Html::a('<i class="far fa-heart me-2"></i>Favoritos', ['/favorito/index'], ['class' => 'shop-drawer-link']) ?>
                <?= Html::a('<i class="fas fa-shopping-bag me-2"></i>Carrinho', ['/carrinho/index'], ['class' => 'shop-drawer-link']) ?>
            </div>
        </div>
    </div>
</div>
