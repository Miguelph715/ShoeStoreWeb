<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\Encomenda;
use common\models\LoginForm;
use common\models\Produto;
use Yii;
use yii\filters\AccessControl;
use yii\db\Expression;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
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
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        //tudo o resto: so quem pode aceder ao back-office (gestor e admin)
                        'allow' => true,
                        'roles' => ['acederBackoffice'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $db = Yii::$app->db;
        //o stock total de cada produto ativo (soma dos tamanhos)
        $stockPorProduto = '(SELECT COALESCE(SUM(pt.stock), 0) FROM {{%produto_tamanho}} pt WHERE pt.produto_id = p.id)';

        $estatisticas = [
            'produtos' => (int) $db->createCommand('SELECT COUNT(*) FROM {{%produto}} WHERE ativo = 1')->queryScalar(),
            'esgotados' => (int) $db->createCommand("SELECT COUNT(*) FROM {{%produto}} p WHERE p.ativo = 1 AND $stockPorProduto = 0")->queryScalar(),
            'stockBaixo' => (int) $db->createCommand("SELECT COUNT(*) FROM {{%produto}} p WHERE p.ativo = 1 AND $stockPorProduto BETWEEN 1 AND 5")->queryScalar(),
            'pendentes' => (int) $db->createCommand("SELECT COUNT(*) FROM {{%encomenda}} WHERE estado = 'Pendente'")->queryScalar(),
            'encomendas' => (int) $db->createCommand('SELECT COUNT(*) FROM {{%encomenda}}')->queryScalar(),
            'vendas' => (float) $db->createCommand("SELECT COALESCE(SUM(total), 0) FROM {{%encomenda}} WHERE estado <> 'Cancelada'")->queryScalar(),
            'clientes' => (int) $db->createCommand("SELECT COUNT(*) FROM {{%auth_assignment}} WHERE item_name = 'cliente'")->queryScalar(),
        ];

        return $this->render('index', [
            'estatisticas' => $estatisticas,
            'ultimas' => Encomenda::find()->with('user')->orderBy(['id' => SORT_DESC])->limit(5)->all(),
            //os produtos com menos stock (so os ativos), para repor
            'poucoStock' => Produto::find()
                ->alias('p')
                ->where(['p.ativo' => 1])
                ->andWhere(new Expression("$stockPorProduto <= 5"))
                ->orderBy(new Expression("$stockPorProduto ASC, p.nome ASC"))
                ->with('produtoTamanhos')
                ->limit(6)
                ->all(),
        ]);
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin(): string|Response
    {
        //quem ja tem sessao e permissao entra direto
        if (!Yii::$app->user->isGuest && Yii::$app->user->can('acederBackoffice')) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (Yii::$app->user->can('acederBackoffice')) {
                return $this->goBack();
            }
            //a password estava certa, mas e so cliente: fecha a sessao e mostra o erro
            Yii::$app->user->logout();
            $model->addError('username', 'Não tens acesso ao back-office.');
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
