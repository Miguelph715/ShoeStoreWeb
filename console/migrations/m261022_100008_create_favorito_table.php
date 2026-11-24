<?php

declare(strict_types=1);

use yii\db\Migration;

class m261022_100008_create_favorito_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%favorito}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'produto_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);

        //um utilizador nao marca o mesmo produto duas vezes
        $this->createIndex('uq_favorito', '{{%favorito}}', ['user_id', 'produto_id'], true);
        $this->createIndex('idx_favorito_produto', '{{%favorito}}', 'produto_id');

        $this->addForeignKey('fk_favorito_user', '{{%favorito}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_favorito_produto', '{{%favorito}}', 'produto_id', '{{%produto}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%favorito}}');
    }
}
