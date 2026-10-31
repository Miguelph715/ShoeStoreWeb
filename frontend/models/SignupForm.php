<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\Perfil;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Registo de um cliente no front-office.
 * A conta fica logo ativa, com o role "cliente" do RBAC e o perfil criado.
 */
class SignupForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $password = '';

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'Este nome de utilizador já existe.'],
            ['username', 'string', 'min' => 2, 'max' => 128],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Este email já está registado.'],

            ['password', 'required'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
        ];
    }

    /**
     * Regista o utilizador. Devolve o User criado, ou null se algo falhar.
     * As 3 gravacoes (user, perfil, role) fazem-se numa transacao: ou ficam todas, ou nenhuma.
     */
    public function signup(): User|null
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            //1. o utilizador, ativo logo (status 10), sem email de verificacao
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = User::STATUS_ACTIVE;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if (!$user->save()) {
                throw new \RuntimeException('Nao foi possivel criar o utilizador.');
            }

            //2. o perfil (o nome comeca igual ao username; o cliente pode mudar mais tarde)
            $perfil = new Perfil();
            $perfil->user_id = $user->id;
            $perfil->nome = $this->username;
            $perfil->user_type = Perfil::TYPE_CLIENTE;
            if (!$perfil->save()) {
                throw new \RuntimeException('Nao foi possivel criar o perfil.');
            }

            //3. o role do RBAC (grava na tabela auth_assignment)
            $auth = Yii::$app->authManager;
            $auth->assign($auth->getRole('cliente'), $user->id);

            $transaction->commit();

            return $user;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error('Registo falhou: ' . $e->getMessage());

            return null;
        }
    }
}