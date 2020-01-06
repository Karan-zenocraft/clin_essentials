<?php

use yii\db\Migration;

/**
 * Class m191216_132958_users
 */
class m191216_132958_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => "bigint",
            'role_id' => "bigint",
            'user_name' => "string(255)",
            'email' => "string(255)",
            'password' => "string(255)",
            'age' => "int(11)",
            'gender' => "tinyint(5)",
            'photo' => "string(255)",
            'badge_count' => "int(11)",
            'status' => "smallint(6)",
            'created_at' => "datetime",
            'updated_at' => "datetime",
            'restaurant_id' => "bigint",

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }

    /*
// Use up()/down() to run migration code without a transaction.
public function up()
{

}

public function down()
{
echo "m191216_132958_users cannot be reverted.\n";

return false;
}
 */
}
