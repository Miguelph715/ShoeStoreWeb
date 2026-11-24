<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Encomenda. estado: Pendente -> Confirmada -> Enviada -> Entregue (ou Cancelada).
 * O email e a morada_envio sao copias do momento da compra.
 */
class m261022_100012_create_encomenda_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%encomenda}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'metodo_pagamento_id' => $this->integer()->notNull(),
            'email' => $this->string(255)->notNull(),
            'morada_envio' => $this->string(255)->notNull(),
            'estado' => $this->string(16)->notNull()->defaultValue('Pendente')
                ->check("estado IN ('Pendente', 'Confirmada', 'Enviada', 'Entregue', 'Cancelada')"),
            'total' => $this->decimal(10, 2)->notNull()->check('total >= 0'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_encomenda_user', '{{%encomenda}}', 'user_id');
        $this->createIndex('idx_encomenda_estado', '{{%encomenda}}', 'estado');
        $this->createIndex('idx_encomenda_metodo', '{{%encomenda}}', 'metodo_pagamento_id');

        //RESTRICT: um utilizador com encomendas nao se apaga (por isso o soft delete)
        $this->addForeignKey('fk_encomenda_user', '{{%encomenda}}', 'user_id', '{{%user}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_encomenda_metodo', '{{%encomenda}}', 'metodo_pagamento_id', '{{%metodo_pagamento}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%encomenda}}');
    }
}
