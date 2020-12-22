<?php

use app\models\Page;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%page}}`.
 */
class m201221_140817_create_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(Page::tableName(), [
            'id' => $this->primaryKey(),
            'source' => $this->string(1023)->notNull()->unique(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->string(1023)->notNull(),
            'body' => $this->text()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Page::tableName());
    }
}
