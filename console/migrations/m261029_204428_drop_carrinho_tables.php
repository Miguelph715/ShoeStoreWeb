<?php

use yii\db\Migration;

class m261029_204428_drop_carrinho_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //primeiro a filha (linha_carrinho, que aponta para o carrinho), depois a mae
        $this->dropTable('{{%linha_carrinho}}');
        $this->dropTable('{{%carrinho}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //os carrinhos eram dados temporarios: esta migration nao se desfaz
        echo "drop_carrinho_tables cannot be reverted.\n";
        echo "m261029_204428_drop_carrinho_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261029_204428_drop_carrinho_tables cannot be reverted.\n";

        return false;
    }
    */
}
