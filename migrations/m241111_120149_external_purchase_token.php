<?php

use yii\db\Migration;

/**
 * Class m241111_120149_transaction
 */
class m241111_120149_external_purchase_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('external_purchase_token', [
            'id' => $this->primaryKey(),
            'external_purchase_id' => $this->string(255)->notNull(),
            'notify_time' => $this->integer(11),
            'bundle' => $this->string(255)->notNull(),
            'base64_token' => $this->string(255)->notNull(),
            'created_at' => $this->integer(11),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('external_purchase_token');
    }
}
