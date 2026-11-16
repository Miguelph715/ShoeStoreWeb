<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cor".
 *
 * @property int $id
 * @property string $nome
 * @property string $codigo_hex
 *
 * @property LinhaEncomenda[] $linhaEncomendas
 * @property ProdutoCor[] $produtoCors
 * @property Produto[] $produtos
 */
class Cor extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'codigo_hex'], 'required'],
            [['nome'], 'string', 'max' => 32],
            [['codigo_hex'], 'string', 'max' => 7],
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
            'codigo_hex' => 'Codigo Hex',
        ];
    }

    /**
     * Gets query for [[LinhaEncomendas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinhaEncomendas()
    {
        return $this->hasMany(LinhaEncomenda::class, ['cor_id' => 'id']);
    }

    /**
     * Gets query for [[ProdutoCors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutoCors()
    {
        return $this->hasMany(ProdutoCor::class, ['cor_id' => 'id']);
    }

    /**
     * Gets query for [[Produtos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutos()
    {
        return $this->hasMany(Produto::class, ['id' => 'produto_id'])->viaTable('produto_cor', ['cor_id' => 'id']);
    }

}
