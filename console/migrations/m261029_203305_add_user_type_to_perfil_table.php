<?php

use yii\db\Migration;

class m261029_203305_add_user_type_to_perfil_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //defaultValue('cliente'): os perfis que ja existem ficam como cliente
        //check(...): a base recusa qualquer outro valor
        $this->addColumn(
            '{{%perfil}}',
            'user_type',
            $this->string(16)->notNull()->defaultValue('cliente')->check("user_type IN ('cliente', 'gestor', 'admin')")
        );

        //indice para filtrar a lista de utilizadores por tipo
        $this->createIndex('idx_perfil_user_type', '{{%perfil}}', 'user_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //desfazer o safeUp, pela ordem inversa: primeiro o indice, depois a coluna
        $this->dropIndex('idx_perfil_user_type', '{{%perfil}}');
        $this->dropColumn('{{%perfil}}', 'user_type');
        echo "m261029_203305_add_user_type_to_perfil_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261029_203305_add_user_type_to_perfil_table cannot be reverted.\n";

        return false;
    }
    */
}
