<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\Perfil;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Registo de cliente no front-office.
 */
class SignupForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $password = '';

    /**
     * {@inheritdoc}
     */
    public function rules()
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Nome de utilizador',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }

    /**
     * Cria o user (ativo), o perfil e o role cliente.
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        if (!$user->save()) {
            return null;
        }

        $perfil = new Perfil();
        $perfil->user_id = $user->id;
        $perfil->nome = $this->username;
        $perfil->user_type = Perfil::TYPE_CLIENTE;
        if (!$perfil->save()) {
            //sem perfil a conta fica incompleta
            $user->delete();

            return null;
        }

        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole('cliente'), $user->id);

        return $user;
    }
}