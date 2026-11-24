<?php

declare(strict_types=1);

use yii\db\Migration;

class m261022_100010_create_linha_carrinho_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%linha_carrinho}}', [
            'id' => $this->primaryKey(),
            'carrinho_id' => $this->integer()->notNull(),
            //o carrinho guarda produto + tamanho de uma vez (a linha de produto_tamanho)
            'produto_tamanho_id' => $this->integer()->notNull(),
            'quantidade' => $this->integer()->notNull()->check('quantidade > 0'),
        ], $tableOptions);

        $this->createIndex('uq_linha_carrinho', '{{%linha_carrinho}}', ['carrinho_id', 'produto_tamanho_id'], true);
        $this->createIndex('idx_linha_carrinho_pt', '{{%linha_carrinho}}', 'produto_tamanho_id');

        $this->addForeignKey('fk_lc_carrinho', '{{%linha_carrinho}}', 'carrinho_id', '{{%carrinho}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_lc_pt', '{{%linha_carrinho}}', 'produto_tamanho_id', '{{%produto_tamanho}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%linha_carrinho}}');
    }
}
