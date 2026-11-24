<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Carrinho do front-office web (a app Android guarda o seu em SQLite local).
 */
class m261022_100009_create_carrinho_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%carrinho}}', [
            'id' => $this->primaryKey(),
            //um carrinho por utilizador
            'user_id' => $this->integer()->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('fk_carrinho_user', '{{%carrinho}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%carrinho}}');
    }
}
