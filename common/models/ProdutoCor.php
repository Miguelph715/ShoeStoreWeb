<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "produto_cor".
 *
 * @property int $id
 * @property int $produto_id
 * @property int $cor_id
 *
 * @property Cor $cor
 * @property Produto $produto
 */
class ProdutoCor extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'produto_cor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['produto_id', 'cor_id'], 'required'],
            [['produto_id', 'cor_id'], 'integer'],
            [['produto_id', 'cor_id'], 'unique', 'targetAttribute' => ['produto_id', 'cor_id']],
            [['cor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cor::class, 'targetAttribute' => ['cor_id' => 'id']],
            [['produto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Produto::class, 'targetAttribute' => ['produto_id' => 'id']],
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
            'cor_id' => 'Cor ID',
        ];
    }

    /**
     * Gets query for [[Cor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCor()
    {
        return $this->hasOne(Cor::class, ['id' => 'cor_id']);
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

}
