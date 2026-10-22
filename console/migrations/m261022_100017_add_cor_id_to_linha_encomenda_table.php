<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * A linha da encomenda guarda a cor comprada (copia do momento da compra, como o preco).
 * Nota: a coluna e obrigatoria, por isso a tabela tem de estar vazia (ainda nao ha encomendas).
 */
class m261022_100017_add_cor_id_to_linha_encomenda_table extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%linha_encomenda}}', 'cor_id', $this->integer()->notNull());
        $this->createIndex('idx_linha_encomenda_cor', '{{%linha_encomenda}}', 'cor_id');
        //RESTRICT: uma cor que ja foi encomendada nao se apaga
        $this->addForeignKey('fk_le_cor', '{{%linha_encomenda}}', 'cor_id', '{{%cor}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_le_cor', '{{%linha_encomenda}}');
        $this->dropIndex('idx_linha_encomenda_cor', '{{%linha_encomenda}}');
        $this->dropColumn('{{%linha_encomenda}}', 'cor_id');
    }
}
