<?php

use yii\db\Migration;

/**
 * Class m191217_075341_users_update
 */
class m191217_075341_users_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('users', 'id', $this->primaryKey()); //timestamp new_data_type
        $this->alterColumn('users', 'id', "bigint");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191217_075341_users_update cannot be reverted.\n";

        return false;
    }

    /*
// Use up()/down() to run migration code without a transaction.
public function up()
{

}

public function down()
{
echo "m191217_075341_users_update cannot be reverted.\n";

return false;
}
 */
}
