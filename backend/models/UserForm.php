<?php

declare(strict_types=1);

namespace backend\models;

use common\models\Perfil;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Formulario do back-office para criar e editar um utilizador:
 * grava de uma vez o User, o Perfil (nome e tipo) e o role do RBAC.
 */
class UserForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $nome = '';
    public string $role = 'cliente';
    public $status = User::STATUS_ACTIVE;

    //o utilizador que se esta a editar (null quando se esta a criar)
    private User|null $_user = null;

    /**
     * Os roles que se podem dar a um utilizador.
     */
    public static function roleList(): array
    {
        return ['cliente' => 'Cliente', 'gestor' => 'Gestor', 'admin' => 'Admin'];
    }

    /**
     * Cria o formulario ja preenchido com os dados de um utilizador existente.
     */
    public static function fromUser(User $user): self
    {
        $form = new self();
        $form->_user = $user;
        $form->username = $user->username;
        $form->email = $user->email;
        $form->status = $user->status;

        $perfil = Perfil::findOne(['user_id' => $user->id]);
        $form->nome = $perfil?->nome ?? $user->username;

        $roles = array_keys(Yii::$app->authManager->getRolesByUser($user->id));
        $form->role = $roles[0] ?? 'cliente';

        return $form;
    }

    public function getUser(): User|null
    {
        return $this->_user;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        //na edicao, o username e o email do proprio utilizador nao contam como repetidos
        $semOProprio = fn($query) => $this->_user ? $query->andWhere(['<>', 'id', $this->_user->id]) : $query;

        return [
            [['username', 'email', 'nome'], 'trim'],
            [['username', 'email', 'nome', 'role', 'status'], 'required'],
            ['username', 'string', 'min' => 2, 'max' => 128],
            ['username', 'unique', 'targetClass' => User::class, 'filter' => $semOProprio, 'message' => 'Este nome de utilizador já existe.'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'filter' => $semOProprio, 'message' => 'Este email já está registado.'],
            ['nome', 'string', 'max' => 128],
            //a password e obrigatoria ao criar; ao editar, vazia = manter a atual
            ['password', 'required', 'when' => fn() => $this->_user === null, 'enableClientValidation' => false, 'message' => 'Indica uma password.'],
            ['password', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['role', 'in', 'range' => array_keys(self::roleList())],
            ['status', 'in', 'range' => array_keys(User::statusList())],
            ['role', 'validateNaoSeTrancar'],
        ];
    }

    /**
     * O admin nao pode tirar o role de admin nem desativar a propria conta (ficava trancado fora).
     */
    public function validateNaoSeTrancar(string $attribute): void
    {
        if ($this->_user !== null && $this->_user->id === (int) Yii::$app->user->id) {
            if ($this->role !== 'admin' || (int) $this->status !== User::STATUS_ACTIVE) {
                $this->addError($attribute, 'Não podes retirar o role de admin nem desativar a tua própria conta.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'username' => 'Nome de utilizador',
            'email' => 'Email',
            'password' => 'Password',
            'nome' => 'Nome',
            'role' => 'Role',
            'status' => 'Estado',
        ];
    }

    /**
     * Grava tudo numa transacao: o utilizador, o perfil e o role. Devolve true se correu bem.
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            //1. o utilizador
            $user = $this->_user ?? new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = (int) $this->status;
            if ($this->password !== '') {
                $user->setPassword($this->password);
            }
            if ($user->isNewRecord) {
                $user->generateAuthKey();
            }
            if (!$user->save()) {
                throw new \RuntimeException('utilizador: ' . json_encode($user->errors));
            }

            //2. o perfil: o nome e o tipo (user_type) igual ao role
            $perfil = Perfil::findOne(['user_id' => $user->id]) ?? new Perfil(['user_id' => $user->id]);
            $perfil->nome = $this->nome;
            $perfil->user_type = $this->role;
            if (!$perfil->save()) {
                throw new \RuntimeException('perfil: ' . json_encode($perfil->errors));
            }

            //3. o role do RBAC: tira os antigos e da o novo
            $auth = Yii::$app->authManager;
            $auth->revokeAll($user->id);
            $auth->assign($auth->getRole($this->role), $user->id);

            $transaction->commit();
            $this->_user = $user;

            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('username', 'Não foi possível guardar (' . $e->getMessage() . ').');

            return false;
        }
    }
}