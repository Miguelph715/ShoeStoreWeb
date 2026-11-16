<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "produto".
 *
 * @property int $id
 * @property int $marca_id
 * @property int $categoria_id
 * @property string $nome
 * @property string|null $descricao
 * @property float $preco
 * @property string|null $imagem
 * @property int $ativo
 * @property int $created_at
 * @property int $updated_at
 * @property string $genero
 * @property string $uso
 * @property float|null $preco_promocional
 *
 * @property Categoria $categoria
 * @property Cor[] $cors
 * @property Favorito[] $favoritos
 * @property LinhaEncomenda[] $linhaEncomendas
 * @property Marca $marca
 * @property ProdutoCor[] $produtoCors
 * @property ProdutoTamanho[] $produtoTamanhos
 * @property Tamanho[] $tamanhos
 * @property User[] $users
 */
class Produto extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'produto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descricao', 'imagem', 'preco_promocional'], 'default', 'value' => null],
            [['ativo'], 'default', 'value' => 1],
            [['genero'], 'default', 'value' => 'unissexo'],
            [['uso'], 'default', 'value' => 'estilo'],
            [['marca_id', 'categoria_id', 'nome', 'preco', 'created_at', 'updated_at'], 'required'],
            [['marca_id', 'categoria_id', 'ativo', 'created_at', 'updated_at'], 'integer'],
            [['descricao'], 'string'],
            [['preco', 'preco_promocional'], 'number'],
            [['nome'], 'string', 'max' => 128],
            [['imagem'], 'string', 'max' => 255],
            [['genero', 'uso'], 'string', 'max' => 10],
            [['categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::class, 'targetAttribute' => ['categoria_id' => 'id']],
            [['marca_id'], 'exist', 'skipOnError' => true, 'targetClass' => Marca::class, 'targetAttribute' => ['marca_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'marca_id' => 'Marca ID',
            'categoria_id' => 'Categoria ID',
            'nome' => 'Nome',
            'descricao' => 'Descricao',
            'preco' => 'Preco',
            'imagem' => 'Imagem',
            'ativo' => 'Ativo',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'genero' => 'Genero',
            'uso' => 'Uso',
            'preco_promocional' => 'Preco Promocional',
        ];
    }

    /**
     * Gets query for [[Categoria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(Categoria::class, ['id' => 'categoria_id']);
    }

    /**
     * Gets query for [[Cors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCors()
    {
        return $this->hasMany(Cor::class, ['id' => 'cor_id'])->viaTable('produto_cor', ['produto_id' => 'id']);
    }

    /**
     * Gets query for [[Favoritos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavoritos()
    {
        return $this->hasMany(Favorito::class, ['produto_id' => 'id']);
    }

    /**
     * Gets query for [[LinhaEncomendas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinhaEncomendas()
    {
        return $this->hasMany(LinhaEncomenda::class, ['produto_id' => 'id']);
    }

    /**
     * Gets query for [[Marca]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMarca()
    {
        return $this->hasOne(Marca::class, ['id' => 'marca_id']);
    }

    /**
     * Gets query for [[ProdutoCors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutoCors()
    {
        return $this->hasMany(ProdutoCor::class, ['produto_id' => 'id']);
    }

    /**
     * Gets query for [[ProdutoTamanhos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProdutoTamanhos()
    {
        return $this->hasMany(ProdutoTamanho::class, ['produto_id' => 'id']);
    }

    /**
     * Gets query for [[Tamanhos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTamanhos()
    {
        return $this->hasMany(Tamanho::class, ['id' => 'tamanho_id'])->viaTable('produto_tamanho', ['produto_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('favorito', ['produto_id' => 'id']);
    }

}
