<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Cores disponiveis de cada produto (relacao N:N entre produto e cor).
 * O stock continua por tamanho (tabela produto_tamanho).
 */
class m261022_100015_create_produto_cor_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%produto_cor}}', [
            'id' => $this->primaryKey(),
            'produto_id' => $this->integer()->notNull(),
            'cor_id' => $this->integer()->notNull(),
        ], $tableOptions);

        //a mesma cor nao pode aparecer duas vezes no mesmo produto
        $this->createIndex('uq_produto_cor', '{{%produto_cor}}', ['produto_id', 'cor_id'], true);
        $this->createIndex('idx_produto_cor_cor', '{{%produto_cor}}', 'cor_id');

        //apagar o produto apaga as suas cores; uma cor usada por algum produto nao se apaga
        $this->addForeignKey('fk_pc_produto', '{{%produto_cor}}', 'produto_id', '{{%produto}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_pc_cor', '{{%produto_cor}}', 'cor_id', '{{%cor}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%produto_cor}}');
    }
}
