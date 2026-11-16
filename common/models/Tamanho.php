<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tamanho".
 *
 * @property int $id
 * @property float $numero
 *
 * @property LinhaEncomenda[] $linhaEncomendas
 * @property ProdutoTamanho[] $produtoTamanhos
 * @property Produto[] $produtos
 */
class Tamanho extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tamanho';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['numero'], 'required'],
            [['numero'], 'number'],
            [['numero'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'numero' => 'Numero',
        ];
    }

    /**
     * Gets query for [[LinhaEncomendas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinhaEncomendas()
    {
        return $this->hasMany(LinhaEncomenda::class, ['tamanho_id' => 'id']);
    }

    /**
     * Gets query for [[ProdutoTamanhos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutoTamanhos()
    {
        return $this->hasMany(ProdutoTamanho::class, ['tamanho_id' => 'id']);
    }

    /**
     * Gets query for [[Produtos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['id' => 'produto_id'])->viaTable('produto_tamanho', ['tamanho_id' => 'id']);
    }

}
