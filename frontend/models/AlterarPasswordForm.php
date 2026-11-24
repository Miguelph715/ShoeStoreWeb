<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Alterar a password do cliente: pede a atual e a nova duas vezes.
 */
class AlterarPasswordForm extends Model
{
    public string $atual = '';
    public string $nova = '';
    public string $repetir = '';

    private User $_user;

    public function __construct(User $user, array $config = [])
    {
        $this->_user = $user;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atual', 'nova', 'repetir'], 'required'],
            ['atual', 'validateAtual'],
            ['nova', 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            ['repetir', 'compare', 'compareAttribute' => 'nova', 'message' => 'As passwords não são iguais.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atual' => 'Password atual',
            'nova' => 'Password nova',
            'repetir' => 'Repete a password nova',
        ];
    }

    public function validateAtual(string $attribute)
    {
        if (!$this->hasErrors() && !$this->_user->validatePassword($this->atual)) {
            $this->addError($attribute, 'A password atual está errada.');
        }
    }

    /**
     * Grava a password nova. Devolve true se correu bem.
     */
    public function guardar()
    {
        if (!$this->validate()) {
            return false;
        }
        $this->_user->setPassword($this->nova);

        return $this->_user->save(false, ['password_hash', 'updated_at']);
    }
}
