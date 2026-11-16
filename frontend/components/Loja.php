<?php

declare(strict_types=1);

namespace frontend\components;

use common\models\Favorito;
use common\models\Produto;
use Yii;

/**
 * Pequenas ajudas da loja: as ligacoes do menu (cabecalho, menu lateral e rodape),
 * o preco de um produto (com ou sem promocao) e os favoritos do utilizador.
 */
class Loja
{
    /**
     * As ligacoes que aparecem no meio da barra de cima.
     */
    public static function menuPrincipal(): array
    {
        return [
            ['Novidades', ['/produto/index', 'novidades' => 1]],
            ['Homem', ['/produto/index', 'genero' => 'homem']],
            ['Mulher', ['/produto/index', 'genero' => 'mulher']],
            ['Criança', ['/produto/index', 'genero' => 'crianca']],
            ['Desporto', ['/produto/index', 'uso' => 'desporto']],
        ];
    }

    public static function generos(): array
    {
        return ['homem' => 'Homem', 'mulher' => 'Mulher', 'crianca' => 'Criança'];
    }

    public static function usos(): array
    {
        return ['desporto' => 'Desporto', 'estilo' => 'Estilo', 'formal' => 'Formal'];
    }

    /**
     * O preco que o cliente paga: o promocional, se existir, senao o normal.
     */
    public static function precoAtual(Produto $p): float
    {
        return $p->preco_promocional !== null ? (float) $p->preco_promocional : (float) $p->preco;
    }

    public static function emPromocao(Produto $p): bool
    {
        return $p->preco_promocional !== null;
    }

    /**
     * A percentagem de desconto (arredondada), por exemplo 20.
     */
    public static function desconto(Produto $p): int
    {
        if (!self::emPromocao($p) || (float) $p->preco <= 0) {
            return 0;
        }

        return (int) round((1 - (float) $p->preco_promocional / (float) $p->preco) * 100);
    }

    /**
     * Um valor em euros, ao estilo portugues: 119,99 €
     */
    public static function euros(float $valor): string
    {
        return number_format($valor, 2, ',', '.') . ' €';
    }

    /**
     * Os ids dos produtos que o utilizador tem nos favoritos (vazio se nao tiver sessao).
     *
     * @return int[]
     */
    public static function favoritosDoUtilizador(): array
    {
        if (Yii::$app->user->isGuest) {
            return [];
        }

        return array_map('intval', Favorito::find()
            ->select('produto_id')
            ->where(['user_id' => Yii::$app->user->id])
            ->column());
    }

    /**
     * A cor do "badge" do estado de uma encomenda.
     */
    public static function estadoClasse(string $estado): string
    {
        return match ($estado) {
            'Pendente' => 'warning',
            'Confirmada' => 'info',
            'Enviada' => 'primary',
            'Entregue' => 'success',
            default => 'secondary',
        };
    }
}
