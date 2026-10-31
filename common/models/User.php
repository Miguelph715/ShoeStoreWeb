<?php

declare(strict_types=1);

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * user model
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            //username e email: obrigatorios e unicos (para o formulario de edicao)
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique'],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique'],
        ];
    }
    /**
     * Os nomes dos campos, em portugues: aparecem nos titulos das colunas e nas mensagens de erro.
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Nome de utilizador',
            'email' => 'Email',
            'status' => 'Estado',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    /**
     * SOFT DELETE: marca o utilizador como eliminado (status 0) sem apagar a linha.
     * O login so aceita status 10, por isso um utilizador eliminado deixa de poder entrar.
     */
    public function softDelete(): bool
    {
        $this->status = self::STATUS_DELETED;
        //save(false, [...]): grava so estas colunas, sem validar o resto
        return $this->save(false, ['status', 'updated_at']);
    }

    /**
     * Reverte o soft delete: o utilizador volta a ficar ativo (status 10).
     */
    public function restore(): bool
    {
        $this->status = self::STATUS_ACTIVE;
        return $this->save(false, ['status', 'updated_at']);
    }

    /**
     * Os estados possiveis, para listas pendentes e filtros.
     */
    public static function statusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_INACTIVE => 'Inativo',
            self::STATUS_DELETED => 'Eliminado',
        ];
    }

    /**
     * O nome do estado do utilizador: $user->statusLabel
     */
    public function getStatusLabel(): string
    {
        return self::statusList()[$this->status] ?? 'Desconhecido';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): User|null
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): never
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername(string $username): User|null
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken(string $token): User|null
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken(string $token): User|null
    {
        return static::findOne(
            [
                'verification_token' => $token,
                'status' => self::STATUS_INACTIVE,
            ],
        );
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string|null $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid(string|null $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);

        $expire = Yii::$app->params['user.passwordResetTokenExpire'];

        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken(): void
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }
}
