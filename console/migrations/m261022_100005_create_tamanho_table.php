<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Lista de tamanhos da loja (numeracao europeia).
 */
class m261022_100005_create_tamanho_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tamanho}}', [
            'id' => $this->primaryKey(),
            //decimal(3,1) permite meios tamanhos, por exemplo 42.5
            'numero' => $this->decimal(3, 1)->notNull()->unique()->check('numero BETWEEN 20 AND 55'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%tamanho}}');
    }
}
