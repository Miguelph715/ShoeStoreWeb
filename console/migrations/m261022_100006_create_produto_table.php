<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * O sapato. ativo: 1 = visivel no front-office, 0 = desativado
 * (um produto que ja foi encomendado desativa-se, nao se apaga).
 */
class m261022_100006_create_produto_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%produto}}', [
            'id' => $this->primaryKey(),
            'marca_id' => $this->integer()->notNull(),
            'categoria_id' => $this->integer()->notNull(),
            'nome' => $this->string(128)->notNull(),
            'descricao' => $this->text(),
            'preco' => $this->decimal(8, 2)->notNull()->check('preco >= 0'),
            'imagem' => $this->string(255),
            'ativo' => $this->smallInteger()->notNull()->defaultValue(1)->check('ativo IN (0, 1)'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_produto_marca', '{{%produto}}', 'marca_id');
        $this->createIndex('idx_produto_categoria', '{{%produto}}', 'categoria_id');
        $this->createIndex('idx_produto_nome', '{{%produto}}', 'nome');
        $this->createIndex('idx_produto_ativo', '{{%produto}}', 'ativo');

        //RESTRICT: nao deixa apagar uma marca ou categoria que tenha produtos
        $this->addForeignKey('fk_produto_marca', '{{%produto}}', 'marca_id', '{{%marca}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_produto_categoria', '{{%produto}}', 'categoria_id', '{{%categoria}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropTable('{{%produto}}');
    }
}
