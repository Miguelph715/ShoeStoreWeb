<?php

declare(strict_types=1);

namespace frontend\models;

use common\models\Categoria;
use common\models\Marca;
use common\models\Produto;
use frontend\components\Loja;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Os filtros da lista de produtos (menu, menu lateral, pesquisa e painel de filtros).
 * Todos os campos vem do endereco (GET), por exemplo ?genero=homem&uso=desporto&promocao=1.
 */
class ProdutoFiltro extends Model
{
    public $q;
    public $genero;
    public $uso;
    public $categoria;
    public $marca;
    public $promocao;
    public $novidades;
    public $preco_min;
    public $preco_max;
    public $ordem = 'novidades';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['q', 'trim'],
            ['q', 'string', 'max' => 60],
            ['genero', 'in', 'range' => array_keys(Loja::generos())],
            ['uso', 'in', 'range' => array_keys(Loja::usos())],
            [['categoria', 'marca'], 'integer'],
            [['promocao', 'novidades'], 'boolean'],
            [['preco_min', 'preco_max'], 'number', 'min' => 0],
            ['ordem', 'in', 'range' => array_keys(self::ordens())],
        ];
    }

    public static function ordens()
    {
        return [
            'novidades' => 'Mais recentes',
            'preco_asc' => 'Preço: do mais baixo',
            'preco_desc' => 'Preço: do mais alto',
            'nome' => 'Nome (A a Z)',
        ];
    }

    public static function categoriasLista()
    {
        return ArrayHelper::map(Categoria::find()->orderBy('nome')->asArray()->all(), 'id', 'nome');
    }

    public static function marcasLista()
    {
        return ArrayHelper::map(Marca::find()->orderBy('nome')->asArray()->all(), 'id', 'nome');
    }

    /**
     * Le os filtros do endereco e devolve os produtos (12 por pagina).
     * Um valor invalido (por exemplo ?genero=robot) e simplesmente ignorado.
     */
    public function search(array $params)
    {
        $this->load($params, '');
        $this->validate();
        foreach (array_keys($this->errors) as $atributo) {
            $this->$atributo = null;
        }
        if (empty($this->ordem)) {
            $this->ordem = 'novidades';
        }

        //o preco que o cliente paga: o promocional, se existir
        $preco = 'IFNULL(p.preco_promocional, p.preco)';

        $query = Produto::find()
            ->alias('p')
            ->joinWith(['marca m', 'categoria c'], true, 'INNER JOIN')
            ->with('produtoTamanhos')
            ->where(['p.ativo' => 1]);

        if (!empty($this->q)) {
            $query->andWhere(['or', ['like', 'p.nome', $this->q], ['like', 'm.nome', $this->q], ['like', 'c.nome', $this->q]]);
        }
        if (!empty($this->genero)) {
            //Homem e Mulher incluem tambem os produtos unissexo
            $query->andWhere(['p.genero' => $this->genero === 'crianca' ? 'crianca' : [$this->genero, 'unissexo']]);
        }
        if (!empty($this->uso)) {
            $query->andWhere(['p.uso' => $this->uso]);
        }
        if (!empty($this->categoria)) {
            $query->andWhere(['p.categoria_id' => (int) $this->categoria]);
        }
        if (!empty($this->marca)) {
            $query->andWhere(['p.marca_id' => (int) $this->marca]);
        }
        if (!empty($this->promocao)) {
            $query->andWhere(['not', ['p.preco_promocional' => null]]);
        }
        if ($this->preco_min !== null && $this->preco_min !== '') {
            $query->andWhere(['>=', new Expression($preco), (float) $this->preco_min]);
        }
        if ($this->preco_max !== null && $this->preco_max !== '') {
            $query->andWhere(['<=', new Expression($preco), (float) $this->preco_max]);
        }

        $query->orderBy(match ($this->ordem) {
            'preco_asc' => new Expression($preco . ' ASC, p.id DESC'),
            'preco_desc' => new Expression($preco . ' DESC, p.id DESC'),
            'nome' => ['p.nome' => SORT_ASC],
            default => ['p.id' => SORT_DESC],
        });

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => ['pageSize' => 12, 'pageSizeParam' => false],
        ]);
    }

    /**
     * O titulo da pagina, conforme o filtro principal.
     */
    public function titulo()
    {
        if (!empty($this->q)) {
            return 'Resultados para “' . $this->q . '”';
        }
        if (!empty($this->promocao)) {
            return 'Promoções';
        }
        if (!empty($this->novidades)) {
            return 'Novidades';
        }
        if (!empty($this->genero)) {
            return Loja::generos()[$this->genero];
        }
        if (!empty($this->uso)) {
            return Loja::usos()[$this->uso];
        }
        if (!empty($this->categoria)) {
            return self::categoriasLista()[(int) $this->categoria] ?? 'Produtos';
        }
        if (!empty($this->marca)) {
            return self::marcasLista()[(int) $this->marca] ?? 'Produtos';
        }

        return 'Todos os produtos';
    }
}
