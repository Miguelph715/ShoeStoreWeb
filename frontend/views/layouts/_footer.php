<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;
?>
<footer id="footer" class="shop-footer mt-auto pt-5 pb-4">
    <div class="container">
        <div class="row gy-4">
            <div class="col-md-4">
                <span class="shop-logo fs-5"><i class="fas fa-shoe-prints"></i>ShoeStore</span>
                <p class="text-secondary small mt-3 mb-0">Ténis para todos os dias, do treino à rua.</p>
            </div>
            <div class="col-6 col-md-2">
                <h3>Loja</h3>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><a href="#">Novidades</a></li>
                    <li class="mb-2"><a href="#">Homem</a></li>
                    <li class="mb-2"><a href="#">Mulher</a></li>
                    <li class="mb-2"><a href="#">Criança</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h3>Ajuda</h3>
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><?= Html::a('Sobre nós', ['/site/about']) ?></li>
                    <li class="mb-2"><?= Html::a('Contactos', ['/site/contact']) ?></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h3>A tua conta</h3>
                <ul class="list-unstyled mb-0 small">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <li class="mb-2"><?= Html::a('Entrar', ['/site/login']) ?></li>
                        <li class="mb-2"><?= Html::a('Criar conta', ['/site/signup']) ?></li>
                    <?php else: ?>
                        <li class="mb-2"><?= Html::a('Sair', ['/site/logout'], ['data-method' => 'post']) ?></li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
        <div class="border-top border-secondary-subtle mt-4 pt-3 text-secondary small">
            &copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?>. Todos os direitos reservados.
        </div>
    </div>
</footer>