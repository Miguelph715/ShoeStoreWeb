<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Painel';
?>
<div class="card">
    <div class="card-body">
        <h5>Olá, <?= Html::encode(Yii::$app->user->identity?->username) ?>!</h5>
        <p class="mb-0">Bem-vindo ao back-office da ShoeStore. Usa o menu da esquerda para gerir a loja.</p>
    </div>
</div>