<?php

declare(strict_types=1);

namespace frontend\components;

use common\models\Produto;
use common\models\ProdutoCor;
use common\models\ProdutoTamanho;
use Yii;

/**
 * O carrinho de compras. Vive na SESSAO do servidor (nao na base de dados):
 * cada linha e um produto + tamanho + cor + quantidade.
 */
class Cart
{
    private const CHAVE = 'carrinho';

    //o maximo de pares que se pode ter da mesma linha
    private const MAXIMO_POR_LINHA = 10;

    /**
     * As linhas guardadas na sessao: chave => [produto_id, tamanho_id, cor_id, quantidade].
     */
    public static function linhas(): array
    {
        return Yii::$app->session->get(self::CHAVE, []);
    }

    public static function vazio(): bool
    {
        return self::linhas() === [];
    }

    /**
     * O numero total de pares no carrinho (o numerozinho do icone do saco).
     */
    public static function quantidadeTotal(): int
    {
        return (int) array_sum(array_column(self::linhas(), 'quantidade'));
    }

    /**
     * Junta uma linha ao carrinho. Devolve null se correu bem, ou o texto do erro.
     */
    public static function adicionar(int $produtoId, int $tamanhoId, int $corId, int $quantidade): string|null
    {
        if ($quantidade < 1) {
            return 'Indica uma quantidade válida.';
        }
        $produto = Produto::findOne(['id' => $produtoId, 'ativo' => 1]);
        if ($produto === null) {
            return 'Este produto já não está disponível.';
        }
        if (!ProdutoCor::find()->where(['produto_id' => $produtoId, 'cor_id' => $corId])->exists()) {
            return 'Escolhe uma cor deste produto.';
        }
        $stock = self::stock($produtoId, $tamanhoId);
        if ($stock === null) {
            return 'Escolhe um tamanho deste produto.';
        }
        if ($stock <= 0) {
            return 'Esse tamanho está esgotado.';
        }

        $chave = "$produtoId-$tamanhoId-$corId";
        $linhas = self::linhas();
        $atual = (int) ($linhas[$chave]['quantidade'] ?? 0);
        $nova = $atual + $quantidade;
        $limite = min($stock, self::MAXIMO_POR_LINHA);
        $aviso = null;
        if ($nova > $limite) {
            $nova = $limite;
            $aviso = "Só há $limite par(es) disponíveis deste tamanho: ajustámos a quantidade.";
        }
        $linhas[$chave] = ['produto_id' => $produtoId, 'tamanho_id' => $tamanhoId, 'cor_id' => $corId, 'quantidade' => $nova];
        Yii::$app->session->set(self::CHAVE, $linhas);

        return $aviso;
    }

    /**
     * Muda a quantidade de uma linha (0 ou menos tira a linha). Devolve null ou o texto do aviso.
     */
    public static function atualizar(string $chave, int $quantidade): string|null
    {
        $linhas = self::linhas();
        if (!isset($linhas[$chave])) {
            return null;
        }
        if ($quantidade < 1) {
            self::remover($chave);

            return null;
        }
        $stock = self::stock((int) $linhas[$chave]['produto_id'], (int) $linhas[$chave]['tamanho_id']) ?? 0;
        $limite = min($stock, self::MAXIMO_POR_LINHA);
        $aviso = null;
        if ($quantidade > $limite) {
            $quantidade = $limite;
            $aviso = "Só há $limite par(es) disponíveis deste tamanho: ajustámos a quantidade.";
        }
        if ($quantidade < 1) {
            self::remover($chave);

            return 'Esse tamanho esgotou e foi retirado do carrinho.';
        }
        $linhas[$chave]['quantidade'] = $quantidade;
        Yii::$app->session->set(self::CHAVE, $linhas);

        return $aviso;
    }

    public static function remover(string $chave): void
    {
        $linhas = self::linhas();
        unset($linhas[$chave]);
        Yii::$app->session->set(self::CHAVE, $linhas);
    }

    public static function limpar(): void
    {
        Yii::$app->session->remove(self::CHAVE);
    }

    /**
     * As linhas completas, prontas a mostrar: o produto, o tamanho, a cor, o preco e o subtotal.
     * `problema` vem preenchido quando a linha ja nao se pode comprar (esgotou ou o produto saiu).
     */
    public static function detalhe(): array
    {
        $resultado = [];
        foreach (self::linhas() as $chave => $l) {
            $produto = Produto::find()->with(['marca', 'cors'])->where(['id' => $l['produto_id']])->one();
            $pt = ProdutoTamanho::find()->with('tamanho')->where(['produto_id' => $l['produto_id'], 'tamanho_id' => $l['tamanho_id']])->one();
            if ($produto === null || $pt === null) {
                self::remover($chave);
                continue;
            }
            $cor = null;
            foreach ($produto->cors as $c) {
                if ((int) $c->id === (int) $l['cor_id']) {
                    $cor = $c;
                }
            }
            $preco = Loja::precoAtual($produto);
            $problema = null;
            if ((int) $produto->ativo !== 1) {
                $problema = 'Este produto já não está disponível.';
            } elseif ((int) $pt->stock < (int) $l['quantidade']) {
                $problema = (int) $pt->stock > 0 ? 'Só há ' . (int) $pt->stock . ' par(es) em stock.' : 'Esgotado.';
            }
            $resultado[] = [
                'chave' => $chave,
                'produto' => $produto,
                'tamanho' => $pt->tamanho,
                'cor' => $cor,
                'quantidade' => (int) $l['quantidade'],
                'preco' => $preco,
                'subtotal' => $preco * (int) $l['quantidade'],
                'stock' => (int) $pt->stock,
                'problema' => $problema,
            ];
        }

        return $resultado;
    }

    public static function total(): float
    {
        return (float) array_sum(array_column(self::detalhe(), 'subtotal'));
    }

    /**
     * O stock de um tamanho de um produto (null se o produto nao tem esse tamanho).
     */
    private static function stock(int $produtoId, int $tamanhoId): int|null
    {
        $pt = ProdutoTamanho::findOne(['produto_id' => $produtoId, 'tamanho_id' => $tamanhoId]);

        return $pt === null ? null : (int) $pt->stock;
    }
}
