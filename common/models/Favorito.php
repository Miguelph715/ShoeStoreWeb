<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "favorito".
 *
 * @property int $id
 * @property int $user_id
 * @property int $produto_id
 * @property int $created_at
 *
 * @property Produto $produto
 * @property User $user
 */
class Favorito extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favorito';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'produto_id', 'created_at'], 'required'],
            [['user_id', 'produto_id', 'created_at'], 'integer'],
            [['user_id', 'produto_id'], 'unique', 'targetAttribute' => ['user_id', 'produto_id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::class, 'targetAttribute' => ['produto_id' => 'id']],
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
            'user_id' => 'User ID',
            'produto_id' => 'Produto ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Produto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduto()
    {
        return $this->hasOne(Produto::class, ['id' => 'produto_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
