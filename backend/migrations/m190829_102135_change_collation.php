<?php

use yii\db\Migration;

/**
 * Class m190829_102135_change_collation
 */
class m190829_102135_change_collation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $db = \Yii::$app->getDb();
        $db->createCommand("ALTER TABLE `questions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci")->execute();
        $db->createCommand("ALTER TABLE `quizzes` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci")->execute();
        $db->createCommand("ALTER TABLE `answers` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci")->execute();
        $db->createCommand("ALTER TABLE `event2quizzes` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_102135_change_collation cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190829_102135_change_collation cannot be reverted.\n";

        return false;
    }
    */
}
