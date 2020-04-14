<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%orders}}`.
 */
class m200411_071334_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%orders}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(50),
            'last_name' => $this->string(50),
            'email' => $this->string(150),
            'phone_number' => $this->string(20),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'order_type' => $this->tinyInteger(),
            'order_value' => $this->integer(),
            'date' => $this->date(),
            'address' => $this->text(),
            'city' => $this->string(50),
            'state' => $this->string(50),
            'postal' => $this->integer(),
            'country' => $this->tinyInteger(),
            'latitude' => $this->decimal(11,8),
            'longitude' => $this->decimal(11,8),
            'updated_at' => $this->timestamp(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%orders}}');
    }
}
