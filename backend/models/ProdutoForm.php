<?php

declare(strict_types=1);

namespace backend\models;

use common\models\Categoria;
use common\models\Cor;
use common\models\Marca;
use common\models\Produto;
use common\models\ProdutoCor;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Formulario do back-office para criar e editar um produto:
 * grava de uma vez o Produto e as suas cores (o stock tem a sua propria pagina).
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

    //o produto que se esta a editar (null quando se esta a criar)
    private Produto|null $_produto = null;

    public static function generos(): array
    {
        return ['homem' => 'Homem', 'mulher' => 'Mulher', 'crianca' => 'Criança', 'unissexo' => 'Unissexo'];
    }

    public static function usos(): array
    {
        return ['desporto' => 'Desporto', 'estilo' => 'Estilo', 'formal' => 'Formal'];
    }

    public static function marcas(): array
    {
        return ArrayHelper::map(Marca::find()->orderBy('nome')->asArray()->all(), 'id', 'nome');
    }

    public static function categorias(): array
    {
        return ArrayHelper::map(Categoria::find()->orderBy('nome')->asArray()->all(), 'id', 'nome');
    }

    /**
     * Todas as cores: id => nome.
     */
    public static function listaCores(): array
    {
        return ArrayHelper::map(Cor::find()->orderBy('nome')->asArray()->all(), 'id', 'nome');
    }

    /**
     * Cria o formulario ja preenchido com os dados de um produto existente.
     */
    public static function fromProduto(Produto $produto): self
    {
        $form = new self();
        $form->_produto = $produto;
        foreach (['nome', 'descricao', 'marca_id', 'categoria_id', 'preco', 'preco_promocional', 'genero', 'uso', 'imagem', 'ativo'] as $campo) {
            $form->$campo = $produto->$campo;
        }
        $form->cores = array_map('intval', ProdutoCor::find()->select('cor_id')->where(['produto_id' => $produto->id])->column());

        return $form;
    }

    public function getProduto(): Produto|null
    {
        return $this->_produto;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
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

    /**
     * Se houver preco promocional, tem de ser mais baixo do que o preco normal.
     */
    public function validatePromocao(string $atributo): void
    {
        if ($this->preco_promocional !== null && $this->preco_promocional !== '' && $this->preco !== null
            && (float) $this->preco_promocional >= (float) $this->preco) {
            $this->addError($atributo, 'O preço promocional tem de ser mais baixo do que o preço.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
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
     * Grava o produto e as cores numa transacao. Devolve true se correu bem.
     */
    public function guardar(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $transacao = Yii::$app->db->beginTransaction();
        try {
            $produto = $this->_produto ?? new Produto();
            foreach (['nome', 'descricao', 'marca_id', 'categoria_id', 'preco', 'preco_promocional', 'genero', 'uso', 'imagem'] as $campo) {
                $produto->$campo = $this->$campo === '' ? null : $this->$campo;
            }
            $produto->ativo = (int) $this->ativo;
            $agora = time();
            if ($produto->isNewRecord) {
                $produto->created_at = $agora;
            }
            $produto->updated_at = $agora;
            if (!$produto->save()) {
                throw new \RuntimeException('produto: ' . json_encode($produto->errors));
            }

            //as cores: apaga as antigas e grava as escolhidas
            ProdutoCor::deleteAll(['produto_id' => $produto->id]);
            foreach (array_unique(array_map('intval', (array) $this->cores)) as $corId) {
                $pc = new ProdutoCor();
                $pc->produto_id = $produto->id;
                $pc->cor_id = $corId;
                if (!$pc->save()) {
                    throw new \RuntimeException('cor: ' . json_encode($pc->errors));
                }
            }

            $transacao->commit();
            $this->_produto = $produto;

            return true;
        } catch (\Throwable $e) {
            $transacao->rollBack();
            $this->addError('nome', 'Não foi possível gravar o produto.');
            Yii::error('ProdutoForm: ' . $e->getMessage());

            return false;
        }
    }
}
