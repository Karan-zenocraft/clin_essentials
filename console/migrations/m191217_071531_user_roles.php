<?php

use yii\db\Migration;

/**
 * Class m191217_071531_user_roles
 */
class m191217_071531_user_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user_roles', [
            'id' => $this->primaryKey(),
            'role_name' => "bigint",
            'role_description' => "string(255)",
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_roles');
    }

    /*
// Use up()/down() to run migration code without a transaction.
public function up()
{

}

public function down()
{
echo "m191217_071531_user_roles cannot be reverted.\n";

return false;
}
 */
}
