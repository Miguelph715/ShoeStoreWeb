<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Metodos de pagamento simulados (MB Way, Multibanco, cartao...).
 */
class m261022_100011_create_metodo_pagamento_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%metodo_pagamento}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(64)->notNull()->unique(),
            'ativo' => $this->smallInteger()->notNull()->defaultValue(1)->check('ativo IN (0, 1)'),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%metodo_pagamento}}');
    }
}
