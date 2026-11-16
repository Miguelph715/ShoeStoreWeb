<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Produto;
use frontend\components\Loja;
use frontend\models\ProdutoFiltro;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * O catalogo da loja (aberto a todos): lista com filtros, detalhe do produto e marcas.
 */
class ProdutoController extends Controller
{
    /**
     * A lista de produtos, com os filtros do endereco.
     */
    public function actionIndex(): string
    {
        $filtro = new ProdutoFiltro();
        $dataProvider = $filtro->search($this->request->queryParams);

        return $this->render('index', [
            'filtro' => $filtro,
            'dataProvider' => $dataProvider,
            'favoritos' => Loja::favoritosDoUtilizador(),
        ]);
    }

    /**
     * O detalhe de um produto: tamanhos com stock, cores e produtos parecidos.
     */
    public function actionView(int $id): string
    {
        $produto = Produto::find()
            ->with(['marca', 'categoria', 'produtoTamanhos.tamanho', 'cors'])
            ->where(['id' => $id, 'ativo' => 1])
            ->one();
        if ($produto === null) {
            throw new NotFoundHttpException('O produto não existe.');
        }

        //do mais pequeno para o maior tamanho
        $tamanhos = $produto->produtoTamanhos;
        usort($tamanhos, fn($a, $b) => (float) $a->tamanho->numero <=> (float) $b->tamanho->numero);

        $parecidos = Produto::find()
            ->with('marca')
            ->where(['categoria_id' => $produto->categoria_id, 'ativo' => 1])
            ->andWhere(['<>', 'id', $produto->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(4)
            ->all();

        return $this->render('view', [
            'produto' => $produto,
            'tamanhos' => $tamanhos,
            'parecidos' => $parecidos,
            'favoritos' => Loja::favoritosDoUtilizador(),
        ]);
    }

    /**
     * Todas as marcas, com o numero de produtos de cada uma.
     */
    public function actionMarcas(): string
    {
        $marcas = (new Query())
            ->select(['m.id', 'm.nome', 'n' => 'COUNT(p.id)'])
            ->from(['m' => 'marca'])
            ->leftJoin(['p' => 'produto'], 'p.marca_id = m.id AND p.ativo = 1')
            ->groupBy(['m.id', 'm.nome'])
            ->orderBy('m.nome')
            ->all();

        return $this->render('marcas', ['marcas' => $marcas]);
    }
}
