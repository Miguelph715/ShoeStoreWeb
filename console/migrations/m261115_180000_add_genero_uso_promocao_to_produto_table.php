<?php

use yii\db\Migration;

/**
 * Acrescenta ao produto o que o menu da loja precisa:
 * - genero: homem, mulher, crianca ou unissexo (Homem / Mulher / Crianca)
 * - uso: desporto, estilo ou formal (Desporto / Estilo / Formal)
 * - preco_promocional: se tiver valor, o produto esta em promocao (Promocoes)
 */
class m261115_180000_add_genero_uso_promocao_to_produto_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //defaultValue: os produtos que ja existem ficam unissexo e estilo
        //check(...): a base recusa qualquer outro valor
        $this->addColumn(
            '{{%produto}}',
            'genero',
            $this->string(10)->notNull()->defaultValue('unissexo')->check("genero IN ('homem', 'mulher', 'crianca', 'unissexo')")
        );
        $this->addColumn(
            '{{%produto}}',
            'uso',
            $this->string(10)->notNull()->defaultValue('estilo')->check("uso IN ('desporto', 'estilo', 'formal')")
        );
        //null = sem promocao
        $this->addColumn('{{%produto}}', 'preco_promocional', $this->decimal(8, 2)->null());
        //se tiver valor, tem de ser mais baixo do que o preco normal. Como compara duas colunas,
        //o MySQL so aceita esta regra como "check" da tabela (e nao da coluna)
        $this->addCheck(
            'chk_produto_promocao',
            '{{%produto}}',
            'preco_promocional IS NULL OR (preco_promocional >= 0 AND preco_promocional < preco)'
        );

        //indices para filtrar a lista de produtos
        $this->createIndex('idx_produto_genero', '{{%produto}}', 'genero');
        $this->createIndex('idx_produto_uso', '{{%produto}}', 'uso');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_produto_uso', '{{%produto}}');
        $this->dropIndex('idx_produto_genero', '{{%produto}}');
        $this->dropCheck('chk_produto_promocao', '{{%produto}}');
        $this->dropColumn('{{%produto}}', 'preco_promocional');
        $this->dropColumn('{{%produto}}', 'uso');
        $this->dropColumn('{{%produto}}', 'genero');
    }
}
