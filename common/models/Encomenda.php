<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "encomenda".
 *
 * @property int $id
 * @property int $user_id
 * @property int $metodo_pagamento_id
 * @property string $email
 * @property string $morada_envio
 * @property string $estado
 * @property float $total
 * @property int $created_at
 * @property int $updated_at
 *
 * @property LinhaEncomenda[] $linhaEncomendas
 * @property MetodoPagamento $metodoPagamento
 * @property User $user
 */
class Encomenda extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'encomenda';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado'], 'default', 'value' => 'Pendente'],
            [['user_id', 'metodo_pagamento_id', 'email', 'morada_envio', 'total', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'metodo_pagamento_id', 'created_at', 'updated_at'], 'integer'],
            [['total'], 'number'],
            [['email', 'morada_envio'], 'string', 'max' => 255],
            [['estado'], 'string', 'max' => 16],
            [['metodo_pagamento_id'], 'exist', 'skipOnError' => true, 'targetClass' => MetodoPagamento::class, 'targetAttribute' => ['metodo_pagamento_id' => 'id']],
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
            'metodo_pagamento_id' => 'Metodo Pagamento ID',
            'email' => 'Email',
            'morada_envio' => 'Morada Envio',
            'estado' => 'Estado',
            'total' => 'Total',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[LinhaEncomendas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinhaEncomendas()
    {
        return $this->hasMany(LinhaEncomenda::class, ['encomenda_id' => 'id']);
    }

    /**
     * Gets query for [[MetodoPagamento]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMetodoPagamento()
    {
        return $this->hasOne(MetodoPagamento::class, ['id' => 'metodo_pagamento_id']);
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
