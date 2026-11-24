<?php

declare(strict_types=1);

namespace frontend\controllers;

use frontend\components\Cart;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * O carrinho de compras (so para quem tem sessao). O carrinho vive na sessao do servidor.
 */
class CarrinhoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'roles' => ['@']],
                ],
            ],
            //adicionar, mudar e tirar alteram dados: so por POST
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'adicionar' => ['post'],
                    'atualizar' => ['post'],
                    'remover' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'linhas' => Cart::detalhe(),
            'total' => Cart::total(),
        ]);
    }

    public function actionAdicionar()
    {
        $post = $this->request->post();
        $erro = Cart::adicionar(
            (int) ($post['produto_id'] ?? 0),
            (int) ($post['tamanho_id'] ?? 0),
            (int) ($post['cor_id'] ?? 0),
            (int) ($post['quantidade'] ?? 1),
        );

        //so e erro "a sério" se nada foi adicionado; um aviso de ajuste tambem passa por aqui
        if ($erro !== null && str_starts_with($erro, 'Só há')) {
            Yii::$app->session->setFlash('warning', $erro);
        } elseif ($erro !== null) {
            Yii::$app->session->setFlash('error', $erro);

            return $this->voltar(['/produto/index']);
        } else {
            Yii::$app->session->setFlash('success', 'Adicionado ao carrinho.');
        }

        return $this->redirect(['index']);
    }

    public function actionAtualizar()
    {
        $aviso = Cart::atualizar((string) $this->request->post('chave'), (int) $this->request->post('quantidade'));
        if ($aviso !== null) {
            Yii::$app->session->setFlash('warning', $aviso);
        }

        return $this->redirect(['index']);
    }

    public function actionRemover()
    {
        Cart::remover((string) $this->request->post('chave'));
        Yii::$app->session->setFlash('success', 'Produto retirado do carrinho.');

        return $this->redirect(['index']);
    }

    /**
     * Volta a pagina de onde o pedido veio (so se for deste site), ou vai para a rota por omissao.
     */
    private function voltar(array $padrao)
    {
        $anterior = $this->request->referrer;
        if ($anterior && str_starts_with($anterior, $this->request->hostInfo)) {
            return $this->redirect($anterior);
        }

        return $this->redirect($padrao);
    }
}
