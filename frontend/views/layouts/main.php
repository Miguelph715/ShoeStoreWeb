<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\helpers\Html;

//a fonte dos titulos grandes (se nao houver internet, o navegador usa a Impact)
$this->registerCssFile('https://fonts.googleapis.com/css2?family=Anton&display=swap');

$this->render('_head');
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100" data-bs-theme="dark">
    <head>
        <?php $this->head() ?>
        <title><?= Html::encode($this->title) ?> | <?= Html::encode(Yii::$app->name) ?></title>
    </head>
    <body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <?= $this->render('_header') ?>

    <main id="main" class="flex-grow-1" role="main">
        <?php if (!empty($this->params['fullWidth'])): ?>
            <!-- paginas de largura total (como a inicial): so as mensagens ficam num contentor -->
            <?php if (Yii::$app->session->getAllFlashes()): ?>
                <div class="container pt-3"><?= Alert::widget() ?></div>
            <?php endif ?>
            <?= $content ?>
        <?php else: ?>
            <div class="container">
                <?php if (!empty($this->params['breadcrumbs'])): ?>
                    <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
                <?php endif ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        <?php endif ?>
    </main>

    <?= $this->render('_footer') ?>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>