<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Linhas de uma encomenda. O preco_unitario e uma copia do preco no momento da compra.
 */
class m261022_100013_create_linha_encomenda_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%linha_encomenda}}', [
            'id' => $this->primaryKey(),
            'encomenda_id' => $this->integer()->notNull(),
            'produto_id' => $this->integer()->notNull(),
            'tamanho_id' => $this->integer()->notNull(),
            'preco_unitario' => $this->decimal(8, 2)->notNull()->check('preco_unitario >= 0'),
            'quantidade' => $this->integer()->notNull()->check('quantidade > 0'),
        ], $tableOptions);

        $this->createIndex('idx_linha_encomenda_encomenda', '{{%linha_encomenda}}', 'encomenda_id');
        $this->createIndex('idx_linha_encomenda_produto', '{{%linha_encomenda}}', 'produto_id');
        $this->createIndex('idx_linha_encomenda_tamanho', '{{%linha_encomenda}}', 'tamanho_id');

        //apagar a encomenda apaga as linhas; o produto e o tamanho nao se podem apagar se tiverem sido encomendados
        $this->addForeignKey('fk_le_encomenda', '{{%linha_encomenda}}', 'encomenda_id', '{{%encomenda}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_le_produto', '{{%linha_encomenda}}', 'produto_id', '{{%produto}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_le_tamanho', '{{%linha_encomenda}}', 'tamanho_id', '{{%tamanho}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%linha_encomenda}}');
    }
}
