<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\components\Encomendas;
use common\models\Encomenda;
use common\models\Perfil;
use frontend\components\Cart;
use frontend\models\EncomendaForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * As encomendas do cliente: finalizar, historico, detalhe e cancelar (so para quem tem sessao).
 * Cada cliente so ve as suas proprias encomendas.
 */
class EncomendaController extends Controller
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
                'actions' => ['cancelar' => ['post']],
            ],
        ];
    }

    /**
     * O historico de encomendas do cliente.
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Encomenda::find()->where(['user_id' => Yii::$app->user->id])->orderBy(['id' => SORT_DESC]),
            'pagination' => ['pageSize' => 10, 'pageSizeParam' => false],
            'sort' => false,
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * O ecra de finalizar: os dados de envio e o metodo de pagamento.
     */
    public function actionFinalizar(): string|Response
    {
        if (Cart::vazio()) {
            Yii::$app->session->setFlash('warning', 'O teu carrinho está vazio.');

            return $this->redirect(['/carrinho/index']);
        }

        $form = new EncomendaForm();
        if ($this->request->isPost) {
            if ($form->load($this->request->post()) && $form->validate()) {
                $encomenda = $form->criar((int) Yii::$app->user->id);
                if ($encomenda !== null) {
                    Yii::$app->session->setFlash('success', 'Encomenda #' . $encomenda->id . ' registada. Obrigado pela compra!');

                    return $this->redirect(['view', 'id' => $encomenda->id]);
                }
                Yii::$app->session->setFlash('error', $form->erro);
            }
        } else {
            //os dados do perfil ja vem preenchidos
            $form->email = (string) Yii::$app->user->identity->email;
            $perfil = Perfil::findOne(['user_id' => Yii::$app->user->id]);
            if ($perfil !== null) {
                $form->morada_envio = trim(implode(', ', array_filter([$perfil->morada, trim($perfil->codigo_postal . ' ' . $perfil->cidade)])));
            }
        }

        return $this->render('finalizar', [
            'form' => $form,
            'linhas' => Cart::detalhe(),
            'total' => Cart::total(),
        ]);
    }

    /**
     * O detalhe de uma encomenda (so a do proprio cliente).
     */
    public function actionView(int $id): string
    {
        return $this->render('view', ['encomenda' => $this->findDoCliente($id)]);
    }

    /**
     * Cancela uma encomenda pendente e devolve o stock.
     */
    public function actionCancelar(int $id): Response
    {
        $encomenda = $this->findDoCliente($id);
        if ($encomenda->estado !== 'Pendente') {
            Yii::$app->session->setFlash('error', 'Só se podem cancelar encomendas pendentes.');

            return $this->redirect(['view', 'id' => $id]);
        }

        //as regras (e a devolucao do stock) estao em common\components\Encomendas
        $erro = Encomendas::mudarEstado($encomenda, 'Cancelada');
        if ($erro === null) {
            Yii::$app->session->setFlash('success', 'Encomenda #' . $id . ' cancelada.');
        } else {
            Yii::$app->session->setFlash('error', 'Não foi possível cancelar a encomenda.');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Procura a encomenda pelo id, mas so se for do cliente com sessao.
     *
     * @throws NotFoundHttpException se nao existir ou for de outro cliente
     */
    protected function findDoCliente(int $id): Encomenda
    {
        $encomenda = Encomenda::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if ($encomenda === null) {
            throw new NotFoundHttpException('A encomenda não existe.');
        }

        return $encomenda;
    }
}
