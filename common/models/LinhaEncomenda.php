<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "linha_encomenda".
 *
 * @property int $id
 * @property int $encomenda_id
 * @property int $produto_id
 * @property int $tamanho_id
 * @property float $preco_unitario
 * @property int $quantidade
 * @property int $cor_id
 *
 * @property Cor $cor
 * @property Encomenda $encomenda
 * @property Produto $produto
 * @property Tamanho $tamanho
 */
class LinhaEncomenda extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'linha_encomenda';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['encomenda_id', 'produto_id', 'tamanho_id', 'preco_unitario', 'quantidade', 'cor_id'], 'required'],
            [['encomenda_id', 'produto_id', 'tamanho_id', 'quantidade', 'cor_id'], 'integer'],
            [['preco_unitario'], 'number'],
            [['cor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cor::class, 'targetAttribute' => ['cor_id' => 'id']],
            [['encomenda_id'], 'exist', 'skipOnError' => true, 'targetClass' => Encomenda::class, 'targetAttribute' => ['encomenda_id' => 'id']],
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
            'encomenda_id' => 'Encomenda ID',
            'produto_id' => 'Produto ID',
            'tamanho_id' => 'Tamanho ID',
            'preco_unitario' => 'Preco Unitario',
            'quantidade' => 'Quantidade',
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
     * Gets query for [[Encomenda]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEncomenda()
    {
        return $this->hasOne(Encomenda::class, ['id' => 'encomenda_id']);
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
