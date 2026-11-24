<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Lista de cores da loja (Preto, Branco, Azul...). O codigo_hex serve para desenhar a amostra de cor no site.
 */
class m261022_100014_create_cor_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cor}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(32)->notNull()->unique(),
            //cor em hexadecimal, por exemplo #1E40AF
            'codigo_hex' => $this->string(7)->notNull()->check("codigo_hex REGEXP '^#[0-9A-Fa-f]{6}$'"),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%cor}}');
    }
}
