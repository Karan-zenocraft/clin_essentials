<?php

namespace common\models\base;

use Yii;
use common\models\UserRules;
use common\models\Users;

/**
 * This is the model class for table "user_roles".
 *
 * @property integer $id
 * @property string $role_name
 * @property string $role_description
 *
 * @property UserRules[] $userRules
 * @property Users[] $users
 */
class UserRolesBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_name'], 'required'],
            [['role_name', 'role_description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'role_name' => Yii::t('app', 'Role Name'),
            'role_description' => Yii::t('app', 'Role Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRules()
    {
        return $this->hasMany(UserRules::className(), ['role_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['role_id' => 'id']);
    }
}
