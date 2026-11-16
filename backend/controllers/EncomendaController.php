<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\models\EncomendaSearch;
use common\components\Encomendas;
use common\models\Encomenda;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Gestao de encomendas no back-office: listar, ver e mudar o estado (so quem tem gerirEncomendas).
 * As encomendas nao se criam nem se apagam aqui: vem do front-office e ficam para o historico.
 */
class EncomendaController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        ['allow' => true, 'roles' => ['gerirEncomendas']],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => ['estado' => ['POST']],
                ],
            ]
        );
    }

    public function actionIndex(): string
    {
        $searchModel = new EncomendaSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView(int $id): string
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    /**
     * Muda o estado (Pendente, Confirmada, Enviada, Entregue ou Cancelada), respeitando o fluxo.
     */
    public function actionEstado(int $id): Response
    {
        $encomenda = $this->findModel($id);
        $erro = Encomendas::mudarEstado($encomenda, (string) $this->request->post('estado'));

        if ($erro !== null) {
            Yii::$app->session->setFlash('error', $erro);
        } else {
            Yii::$app->session->setFlash('success', 'Encomenda #' . $id . ' passou a "' . $encomenda->estado . '".');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @throws NotFoundHttpException se a encomenda nao existir
     */
    protected function findModel(int $id): Encomenda
    {
        $encomenda = Encomenda::find()->with(['user', 'metodoPagamento', 'linhaEncomendas.produto', 'linhaEncomendas.tamanho', 'linhaEncomendas.cor'])->where(['id' => $id])->one();
        if ($encomenda === null) {
            throw new NotFoundHttpException('A encomenda não existe.');
        }

        return $encomenda;
    }
}
