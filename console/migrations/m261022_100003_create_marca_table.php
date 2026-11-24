<?php

declare(strict_types=1);

use yii\db\Migration;

class m261022_100003_create_marca_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%marca}}', [
            'id' => $this->primaryKey(),
            //nome unico: nao pode haver duas marcas iguais
            'nome' => $this->string(64)->notNull()->unique(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%marca}}');
    }
}
