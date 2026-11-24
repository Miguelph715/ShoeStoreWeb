<?php

declare(strict_types=1);

namespace backend\models;

use common\models\Categoria;
use common\models\Cor;
use common\models\Marca;
use common\models\Produto;
use common\models\ProdutoCor;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Criar/editar um produto e as suas cores.
 */
class ProdutoForm extends Model
{
    public string $nome = '';
    public $descricao;
    public $marca_id;
    public $categoria_id;
    public $preco;
    public $preco_promocional;
    public string $genero = 'unissexo';
    public string $uso = 'estilo';
    public $imagem;
    public $ativo = 1;
    public $cores = [];

    //null ao criar
    private Produto|null $_produto = null;

    public static function generos()
    {
        return ['homem' => 'Homem', 'mulher' => 'Mulher', 'crianca' => 'Criança', 'unissexo' => 'Unissexo'];
    }

    public static function usos()
    {
        return ['desporto' => 'Desporto', 'estilo' => 'Estilo', 'formal' => 'Formal'];
    }

    public static function marcas()
    {
        return ArrayHelper::map(Marca::find()->orderBy('nome')->asArray()->all(), 'id', 'nome');
    }

    public static function categorias()
    {
        return ArrayHelper::map(Categoria::find()->orderBy('nome')->asArray()->all(), 'id', 'nome');
    }

    public static function listaCores()
    {
        return ArrayHelper::map(Cor::find()->orderBy('nome')->asArray()->all(), 'id', 'nome');
    }

    /**
     * Formulário preenchido com um produto existente.
     */
    public static function fromProduto(Produto $produto)
    {
        $form = new self();
        $form->_produto = $produto;
        foreach (['nome', 'descricao', 'marca_id', 'categoria_id', 'preco', 'preco_promocional', 'genero', 'uso', 'imagem', 'ativo'] as $campo) {
            $form->$campo = $produto->$campo;
        }
        $form->cores = array_map('intval', ProdutoCor::find()->select('cor_id')->where(['produto_id' => $produto->id])->column());

        return $form;
    }

    public function getProduto()
    {
        return $this->_produto;
    }

    public function rules()
    {
        return [
            [['nome', 'descricao', 'imagem'], 'trim'],
            [['nome', 'marca_id', 'categoria_id', 'preco', 'genero', 'uso'], 'required'],
            ['nome', 'string', 'max' => 128],
            ['descricao', 'string', 'max' => 5000],
            ['marca_id', 'in', 'range' => array_keys(self::marcas()), 'message' => 'Escolhe uma marca.'],
            ['categoria_id', 'in', 'range' => array_keys(self::categorias()), 'message' => 'Escolhe uma categoria.'],
            ['preco', 'number', 'min' => 0, 'max' => 99999],
            ['preco_promocional', 'number', 'min' => 0, 'max' => 99999],
            ['preco_promocional', 'default', 'value' => null],
            ['preco_promocional', 'validatePromocao'],
            ['genero', 'in', 'range' => array_keys(self::generos())],
            ['uso', 'in', 'range' => array_keys(self::usos())],
            ['imagem', 'url', 'validSchemes' => ['http', 'https'], 'message' => 'Indica um endereço completo (https://...).'],
            ['imagem', 'string', 'max' => 255],
            ['imagem', 'default', 'value' => null],
            ['ativo', 'boolean'],
            ['cores', 'each', 'rule' => ['in', 'range' => array_keys(self::listaCores())]],
        ];
    }

    //a promoção tem de ser mais barata que o preço
    public function validatePromocao(string $atributo)
    {
        if ($this->preco_promocional !== null && $this->preco_promocional !== '' && $this->preco !== null
            && (float) $this->preco_promocional >= (float) $this->preco) {
            $this->addError($atributo, 'O preço promocional tem de ser mais baixo do que o preço.');
        }
    }

    public function attributeLabels()
    {
        return [
            'nome' => 'Nome',
            'descricao' => 'Descrição',
            'marca_id' => 'Marca',
            'categoria_id' => 'Categoria',
            'preco' => 'Preço (€)',
            'preco_promocional' => 'Preço promocional (€)',
            'genero' => 'Género',
            'uso' => 'Para quê',
            'imagem' => 'Imagem (endereço)',
            'ativo' => 'Visível na loja',
            'cores' => 'Cores disponíveis',
        ];
    }

    /**
     * Grava o produto e as cores.
     */
    public function guardar()
    {
        if (!$this->validate()) {
            return false;
        }

        $produto = $this->_produto ?? new Produto();
        foreach (['nome', 'descricao', 'marca_id', 'categoria_id', 'preco', 'preco_promocional', 'genero', 'uso', 'imagem'] as $campo) {
            $produto->$campo = $this->$campo === '' ? null : $this->$campo;
        }
        $produto->ativo = (int) $this->ativo;
        if ($produto->isNewRecord) {
            $produto->created_at = time();
        }
        $produto->updated_at = time();
        if (!$produto->save()) {
            $this->addErrors($produto->errors);

            return false;
        }
        $this->_produto = $produto;

        //substitui as cores
        ProdutoCor::deleteAll(['produto_id' => $produto->id]);
        foreach (array_unique(array_map('intval', (array) $this->cores)) as $corId) {
            $pc = new ProdutoCor(['produto_id' => $produto->id, 'cor_id' => $corId]);
            if (!$pc->save()) {
                $this->addError('cores', 'Não foi possível gravar as cores.');

                return false;
            }
        }

        return true;
    }
}
