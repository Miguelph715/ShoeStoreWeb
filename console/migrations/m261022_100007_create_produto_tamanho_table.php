<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Stock de cada produto em cada tamanho.
 */
class m261022_100007_create_produto_tamanho_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%produto_tamanho}}', [
            'id' => $this->primaryKey(),
            'produto_id' => $this->integer()->notNull(),
            'tamanho_id' => $this->integer()->notNull(),
            //o stock nunca fica negativo
            'stock' => $this->integer()->notNull()->defaultValue(0)->check('stock >= 0'),
        ], $tableOptions);

        //o mesmo tamanho nao pode aparecer duas vezes no mesmo produto
        $this->createIndex('uq_produto_tamanho', '{{%produto_tamanho}}', ['produto_id', 'tamanho_id'], true);
        $this->createIndex('idx_produto_tamanho_tamanho', '{{%produto_tamanho}}', 'tamanho_id');

        $this->addForeignKey('fk_pt_produto', '{{%produto_tamanho}}', 'produto_id', '{{%produto}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_pt_tamanho', '{{%produto_tamanho}}', 'tamanho_id', '{{%tamanho}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%produto_tamanho}}');
    }
}
