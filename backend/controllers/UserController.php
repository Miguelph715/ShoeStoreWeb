<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\models\UserForm;
use backend\models\UserSearch;
use common\models\Perfil;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Gestao de utilizadores no back-office: CRUD completo com SOFT DELETE.
 * Eliminar nao apaga a linha, so muda o status para 0 (e da para restaurar).
 */
class UserController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                //so quem tem a permissao gerirUtilizadores (o admin) entra neste controlador
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['gerirUtilizadores'],
                        ],
                    ],
                ],
                //eliminar e restaurar alteram dados: so por POST
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'restore' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lista os utilizadores (com filtros por username, email, estado e role).
     */
    public function actionIndex(): string
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Mostra um utilizador, com o perfil e o role.
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'perfil' => Perfil::findOne(['user_id' => $model->id]),
            'roles' => array_keys(Yii::$app->authManager->getRolesByUser($model->id)),
        ]);
    }

    /**
     * Cria um utilizador (com perfil e role).
     */
    public function actionCreate(): string|Response
    {
        $model = new UserForm();

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Utilizador criado.');
            return $this->redirect(['view', 'id' => $model->user->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Altera um utilizador (dados, estado e role).
     */
    public function actionUpdate(int $id): string|Response
    {
        $model = UserForm::fromUser($this->findModel($id));

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Utilizador atualizado.');
            return $this->redirect(['view', 'id' => $model->user->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * SOFT DELETE: marca o utilizador como eliminado (status 0), sem apagar a linha.
     */
    public function actionDelete(int $id): Response
    {
        $model = $this->findModel($id);

        //o admin nao se pode eliminar a si proprio
        if ($model->id === (int) Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'Não podes eliminar a tua própria conta.');
            return $this->redirect(['index']);
        }

        $model->softDelete();
        Yii::$app->session->setFlash('success', 'Utilizador "' . $model->username . '" eliminado. Pode ser restaurado.');

        return $this->redirect(['index']);
    }

    /**
     * Reverte o soft delete: o utilizador volta a ficar ativo (status 10).
     */
    public function actionRestore(int $id): Response
    {
        $model = $this->findModel($id);
        $model->restore();
        Yii::$app->session->setFlash('success', 'Utilizador "' . $model->username . '" restaurado.');

        return $this->redirect(['index']);
    }

    /**
     * Procura o utilizador pelo id (tambem os eliminados, para os poder restaurar).
     *
     * @throws NotFoundHttpException se nao existir
     */
    protected function findModel(int $id): User
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('O utilizador não existe.');
    }
}