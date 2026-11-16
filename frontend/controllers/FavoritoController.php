<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Favorito;
use common\models\Produto;
use frontend\components\Loja;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Os favoritos do cliente (so para quem tem sessao).
 */
class FavoritoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['alternar' => ['post']],
            ],
        ];
    }

    /**
     * A lista dos produtos favoritos.
     */
    public function actionIndex(): string
    {
        $produtos = Produto::find()
            ->alias('p')
            ->with(['marca', 'produtoTamanhos'])
            ->innerJoin('{{%favorito}} f', 'f.produto_id = p.id')
            ->where(['f.user_id' => Yii::$app->user->id, 'p.ativo' => 1])
            ->orderBy(['f.id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'produtos' => $produtos,
            'favoritos' => Loja::favoritosDoUtilizador(),
        ]);
    }

    /**
     * Marca o produto como favorito, ou desmarca se ja for.
     */
    public function actionAlternar(): Response
    {
        $produtoId = (int) $this->request->post('produto_id');
        $userId = (int) Yii::$app->user->id;

        if (!Produto::find()->where(['id' => $produtoId, 'ativo' => 1])->exists()) {
            Yii::$app->session->setFlash('error', 'Esse produto não existe.');

            return $this->redirect(['/produto/index']);
        }

        $favorito = Favorito::findOne(['user_id' => $userId, 'produto_id' => $produtoId]);
        if ($favorito !== null) {
            $favorito->delete();
            Yii::$app->session->setFlash('success', 'Retirado dos favoritos.');
        } else {
            $novo = new Favorito();
            $novo->user_id = $userId;
            $novo->produto_id = $produtoId;
            $novo->created_at = time();
            $novo->save();
            Yii::$app->session->setFlash('success', 'Adicionado aos favoritos.');
        }

        return $this->voltar(['index']);
    }

    /**
     * Volta a pagina de onde o pedido veio (so se for deste site), ou vai para a rota por omissao.
     */
    private function voltar(array $padrao): Response
    {
        $anterior = $this->request->referrer;
        if ($anterior && str_starts_with($anterior, $this->request->hostInfo)) {
            return $this->redirect($anterior);
        }

        return $this->redirect($padrao);
    }
}
