<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use backend\assets\AdminAsset;
use yii\bootstrap4\Breadcrumbs;
use yii\helpers\Html;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?> | <?= Html::encode(Yii::$app->name) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
    <?php $this->beginBody() ?>

    <div class="wrapper">

        <!-- Barra de cima -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Abrir ou fechar o menu">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                <span class="nav-link">
                    <i class="far fa-user"></i> <?= Html::encode(Yii::$app->user->identity?->username) ?>
                </span>
                </li>
                <li class="nav-item">
                    <?= Html::a(
                        '<i class="fas fa-sign-out-alt"></i> Sair',
                        ['/site/logout'],
                        ['class' => 'nav-link', 'data-method' => 'post']
                    ) ?>
                </li>
            </ul>
        </nav>

        <!-- Menu lateral -->
        <?= $this->render('_sidebar') ?>

        <!-- Conteudo -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><?= Html::encode($this->title) ?></h1>
                        </div>
                        <div class="col-sm-6">
                            <?= Breadcrumbs::widget([
                                'links' => $this->params['breadcrumbs'] ?? [],
                                'homeLink' => ['label' => 'Painel', 'url' => ['/site/index']],
                                'options' => ['class' => 'breadcrumb float-sm-right'],
                            ]) ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <?php foreach (Yii::$app->session->getAllFlashes() as $tipo => $mensagens): ?>
                        <?php foreach ((array) $mensagens as $mensagem): ?>
                            <div class="alert alert-<?= $tipo === 'error' ? 'danger' : Html::encode($tipo) ?> alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">&times;</button>
                                <?= Html::encode($mensagem) ?>
                            </div>
                        <?php endforeach ?>
                    <?php endforeach ?>

                    <?= $content ?>
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <strong>&copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?></strong> &middot; Back-office
        </footer>
    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage();