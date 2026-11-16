<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "metodo_pagamento".
 *
 * @property int $id
 * @property string $nome
 * @property int $ativo
 *
 * @property Encomenda[] $encomendas
 */
class MetodoPagamento extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'metodo_pagamento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ativo'], 'default', 'value' => 1],
            [['nome'], 'required'],
            [['ativo'], 'integer'],
            [['nome'], 'string', 'max' => 64],
            [['nome'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'ativo' => 'Ativo',
        ];
    }

    /**
     * Gets query for [[Encomendas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEncomendas()
    {
        return $this->hasMany(Encomenda::class, ['metodo_pagamento_id' => 'id']);
    }

}
