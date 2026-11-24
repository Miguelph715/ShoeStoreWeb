<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\models\ProdutoForm;
use backend\models\ProdutoSearch;
use backend\models\StockForm;
use common\models\LinhaEncomenda;
use common\models\Produto;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Gestao de produtos no back-office: criar, ver, editar, desativar e eliminar, e o stock por tamanho.
 * Um produto com encomendas nunca se apaga (so se desativa), para nao perder o historico.
 */
class ProdutoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        //o stock tem a sua propria permissao
                        ['allow' => true, 'actions' => ['stock'], 'roles' => ['gerirStock']],
                        ['allow' => true, 'roles' => ['gerirProdutos']],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'desativar' => ['POST'],
                        'ativar' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new ProdutoSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $form = new ProdutoForm();

        if ($this->request->isPost && $form->load($this->request->post()) && $form->guardar()) {
            Yii::$app->session->setFlash('success', 'Produto criado. Define agora o stock por tamanho.');

            //um produto novo ainda nao tem tamanhos: leva logo ao stock (se tiver permissao)
            return $this->redirect([Yii::$app->user->can('gerirStock') ? 'stock' : 'view', 'id' => $form->produto->id]);
        }

        return $this->render('create', ['model' => $form]);
    }

    public function actionUpdate(int $id)
    {
        $form = ProdutoForm::fromProduto($this->findModel($id));

        if ($this->request->isPost && $form->load($this->request->post()) && $form->guardar()) {
            Yii::$app->session->setFlash('success', 'Produto atualizado.');

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', ['model' => $form]);
    }

    /**
     * O stock de cada tamanho do produto.
     */
    public function actionStock(int $id)
    {
        $produto = $this->findModel($id);
        $form = new StockForm($produto);

        if ($this->request->isPost) {
            $form->stock = (array) $this->request->post('stock', []);
            if ($form->guardar()) {
                Yii::$app->session->setFlash('success', 'Stock atualizado.');

                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('stock', ['form' => $form]);
    }

    /**
     * Desativar: o produto deixa de aparecer na loja, mas continua na base (e nas encomendas antigas).
     */
    public function actionDesativar(int $id)
    {
        $produto = $this->findModel($id);
        $produto->ativo = 0;
        $produto->updated_at = time();
        $produto->save(false, ['ativo', 'updated_at']);
        Yii::$app->session->setFlash('success', 'Produto "' . $produto->nome . '" desativado. Já não aparece na loja.');

        return $this->redirect(['index']);
    }

    public function actionAtivar(int $id)
    {
        $produto = $this->findModel($id);
        $produto->ativo = 1;
        $produto->updated_at = time();
        $produto->save(false, ['ativo', 'updated_at']);
        Yii::$app->session->setFlash('success', 'Produto "' . $produto->nome . '" ativado. Voltou à loja.');

        return $this->redirect(['index']);
    }

    /**
     * Eliminar de vez. So e possivel se o produto nunca foi encomendado.
     */
    public function actionDelete(int $id)
    {
        $produto = $this->findModel($id);

        if (LinhaEncomenda::find()->where(['produto_id' => $id])->exists()) {
            Yii::$app->session->setFlash('error', 'Este produto já foi encomendado e não se pode eliminar. Desativa-o para o tirar da loja.');

            return $this->redirect(['index']);
        }

        //os tamanhos, as cores e os favoritos do produto saem em cascata
        $produto->delete();
        Yii::$app->session->setFlash('success', 'Produto "' . $produto->nome . '" eliminado.');

        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException se o produto nao existir
     */
    protected function findModel(int $id)
    {
        $produto = Produto::find()->with(['marca', 'categoria', 'produtoTamanhos.tamanho', 'cors'])->where(['id' => $id])->one();
        if ($produto === null) {
            throw new NotFoundHttpException('O produto não existe.');
        }

        return $produto;
    }
}
