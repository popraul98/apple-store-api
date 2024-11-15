<?php

use yii\db\Migration;

/**
 * Class m241111_120149_transaction
 */
class m241111_120149_transactions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('transactions', [
            'id' => $this->primaryKey(),
            'notify_time' => $this->integer(11),
            'bundle' => $this->string(255),
            'base64_token' => $this->string(255)->notNull(),
            'created_at' => $this->integer(11),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('transactions');
    }
}
