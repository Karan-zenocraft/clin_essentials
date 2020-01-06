<?php

namespace common\models\base;

use Yii;
use common\models\UserRoles;

/**
 * This is the model class for table "user_rules".
 *
 * @property integer $id
 * @property integer $role_id
 * @property string $privileges_controller
 * @property string $privileges_actions
 * @property string $permission
 * @property string $permission_type
 *
 * @property UserRoles $role
 */
class UserRulesBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'privileges_controller', 'privileges_actions'], 'required'],
            [['role_id'], 'integer'],
            [['privileges_actions', 'permission'], 'string'],
            [['privileges_controller', 'permission_type'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'role_id' => Yii::t('app', 'Role ID'),
            'privileges_controller' => Yii::t('app', 'Privileges Controller'),
            'privileges_actions' => Yii::t('app', 'Privileges Actions'),
            'permission' => Yii::t('app', 'Permission'),
            'permission_type' => Yii::t('app', 'Permission Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(UserRoles::className(), ['id' => 'role_id']);
    }
}
