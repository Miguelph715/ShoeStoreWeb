<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Perfil;
use frontend\models\AlterarPasswordForm;
use frontend\models\PerfilForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * O perfil do cliente: ver, editar e alterar a password (so para quem tem sessao).
 */
class PerfilController extends Controller
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
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index', ['perfil' => $this->findPerfil()]);
    }

    public function actionEditar(): string|Response
    {
        $perfil = $this->findPerfil();
        $form = PerfilForm::fromPerfil($perfil);

        if ($form->load($this->request->post()) && $form->guardar($perfil)) {
            Yii::$app->session->setFlash('success', 'Perfil atualizado.');

            return $this->redirect(['index']);
        }

        return $this->render('editar', ['form' => $form]);
    }

    public function actionPassword(): string|Response
    {
        $form = new AlterarPasswordForm(Yii::$app->user->identity);

        if ($form->load($this->request->post()) && $form->guardar()) {
            Yii::$app->session->setFlash('success', 'Password alterada.');

            return $this->redirect(['index']);
        }

        return $this->render('password', ['form' => $form]);
    }

    /**
     * O perfil do utilizador com sessao.
     *
     * @throws NotFoundHttpException se nao existir
     */
    protected function findPerfil(): Perfil
    {
        $perfil = Perfil::findOne(['user_id' => Yii::$app->user->id]);
        if ($perfil === null) {
            throw new NotFoundHttpException('O perfil não existe.');
        }

        return $perfil;
    }
}
