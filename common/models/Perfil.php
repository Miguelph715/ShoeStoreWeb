<?php

declare(strict_types=1);

namespace common\models;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "perfil". Dados pessoais de um utilizador (tabela perfil, relacao 1:1 com user).
 *
 * @property int $id
 * @property int $user_id
 * @property string $nome
 * @property string|null $telefone
 * @property string|null $morada
 * @property string|null $codigo_postal
 * @property string|null $cidade
 * @property string|null $nif
 * @property int $created_at
 * @property int $updated_at
 * @property string $user_type cliente, gestor ou admin (igual ao role do RBAC)
 *
 * @property User $user
 */
class Perfil extends \yii\db\ActiveRecord
{


    public const TYPE_CLIENTE = 'cliente';
    public const TYPE_GESTOR = 'gestor';
    public const TYPE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%perfil}}';
    }

    /**
     * Preenche sozinho o created_at e o updated_at (colunas obrigatorias da tabela).
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
    public function rules()
    {
        return [
            [['telefone', 'morada', 'codigo_postal', 'cidade', 'nif'], 'default', 'value' => null],
            [['user_type'], 'default', 'value' => 'cliente'],
            //o created_at e o updated_at nao estao aqui: quem os preenche e o TimestampBehavior
            [['user_id', 'nome'], 'required'],
            [['user_id'], 'integer'],
            [['nome'], 'string', 'max' => 128],
            [['telefone'], 'string', 'max' => 20],
            [['morada'], 'string', 'max' => 255],
            [['codigo_postal'], 'string', 'max' => 10],
            [['cidade'], 'string', 'max' => 64],
            [['nif'], 'string', 'max' => 9],
            [['user_type'], 'string', 'max' => 16],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'user ID',
            'nome' => 'Nome',
            'telefone' => 'Telefone',
            'morada' => 'Morada',
            'codigo_postal' => 'Codigo Postal',
            'cidade' => 'Cidade',
            'nif' => 'Nif',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_type' => 'user Type',
        ];
    }

    /**
     * Os tipos de utilizador (os mesmos nomes dos roles do RBAC).
     */
    public static function typeList(): array
    {
        return [
            self::TYPE_CLIENTE => 'Cliente',
            self::TYPE_GESTOR => 'Gestor',
            self::TYPE_ADMIN => 'Admin',
        ];
    }

    /**
     * Gets query for [[user]].
     *
     * @return \yii\db\ActiveQuery
     */
    /**
     * O utilizador a quem pertence este perfil.
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
