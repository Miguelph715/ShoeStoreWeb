<?php

declare(strict_types=1);

use yii\db\Migration;

class m261022_100004_create_categoria_table extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%categoria}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(64)->notNull()->unique(),
            'descricao' => $this->text(),
        ], $tableOptions);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%categoria}}');
    }
}
