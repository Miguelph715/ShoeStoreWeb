<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * A linha do carrinho passa a guardar tambem a cor escolhida (uma linha de produto_cor).
 * Nota: a coluna e obrigatoria, por isso a tabela tem de estar vazia (ainda nao ha carrinhos).
 */
class m261022_100016_add_produto_cor_id_to_linha_carrinho_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%linha_carrinho}}', 'produto_cor_id', $this->integer()->notNull());

        //o indice novo (que tambem comeca em carrinho_id) cria-se ANTES de apagar o antigo,
        //porque a chave estrangeira do carrinho precisa sempre de um indice
        $this->createIndex('uq_linha_carrinho_cor', '{{%linha_carrinho}}', ['carrinho_id', 'produto_tamanho_id', 'produto_cor_id'], true);
        $this->dropIndex('uq_linha_carrinho', '{{%linha_carrinho}}');
        $this->createIndex('idx_linha_carrinho_pc', '{{%linha_carrinho}}', 'produto_cor_id');

        //se o produto deixar de ter essa cor, as linhas do carrinho com ela desaparecem
        $this->addForeignKey('fk_lc_pc', '{{%linha_carrinho}}', 'produto_cor_id', '{{%produto_cor}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_lc_pc', '{{%linha_carrinho}}');
        $this->dropIndex('idx_linha_carrinho_pc', '{{%linha_carrinho}}');
        $this->createIndex('uq_linha_carrinho', '{{%linha_carrinho}}', ['carrinho_id', 'produto_tamanho_id'], true);
        $this->dropIndex('uq_linha_carrinho_cor', '{{%linha_carrinho}}');
        $this->dropColumn('{{%linha_carrinho}}', 'produto_cor_id');
    }
}
