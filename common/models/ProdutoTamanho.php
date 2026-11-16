<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "produto_tamanho".
 *
 * @property int $id
 * @property int $produto_id
 * @property int $tamanho_id
 * @property int $stock
 *
 * @property Produto $produto
 * @property Tamanho $tamanho
 */
class ProdutoTamanho extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'produto_tamanho';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stock'], 'default', 'value' => 0],
            [['produto_id', 'tamanho_id'], 'required'],
            [['produto_id', 'tamanho_id', 'stock'], 'integer'],
            [['produto_id', 'tamanho_id'], 'unique', 'targetAttribute' => ['produto_id', 'tamanho_id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::class, 'targetAttribute' => ['produto_id' => 'id']],
            [['tamanho_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tamanho::class, 'targetAttribute' => ['tamanho_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'produto_id' => 'Produto ID',
            'tamanho_id' => 'Tamanho ID',
            'stock' => 'Stock',
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
     * Gets query for [[Tamanho]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTamanho()
    {
        return $this->hasOne(Tamanho::class, ['id' => 'tamanho_id']);
    }

}
