<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Dados pessoais do utilizador (relacao 1:1 com a tabela user, que nao se altera).
 */
class m261022_100001_create_perfil_table extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%perfil}}', [
            'id' => $this->primaryKey(),
            //unique: cada utilizador tem no maximo um perfil (1:1)
            'user_id' => $this->integer()->notNull()->unique(),
            'nome' => $this->string(128)->notNull(),
            'telefone' => $this->string(20),
            'morada' => $this->string(255),
            'codigo_postal' => $this->string(10),
            'cidade' => $this->string(64),
            //o NIF, se existir, tem 9 digitos
            'nif' => $this->string(9)->check('nif IS NULL OR CHAR_LENGTH(nif) = 9'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        //apagar o utilizador apaga o perfil
        $this->addForeignKey('fk_perfil_user', '{{%perfil}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%perfil}}');
    }
}
