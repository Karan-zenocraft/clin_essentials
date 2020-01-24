<?php

use yii\db\Migration;

/**
 * Class m191217_080637_users_relation
 */
class m191217_080637_users_relation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-users-role_id',
            'users',
            'role_id'
        );
        $this->addForeignKey(
            'fk-users-role_id',
            'users',
            'role_id',
            'user_roles',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191217_080637_users_relation cannot be reverted.\n";

        return false;
    }

    /*
// Use up()/down() to run migration code without a transaction.
public function up()
{

}

public function down()
{
echo "m191217_080637_users_relation cannot be reverted.\n";

return false;
}
 */
}
