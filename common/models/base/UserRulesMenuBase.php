<?php

namespace common\models\base;

use Yii;
use common\models\UserRules;

/**
 * This is the model class for table "user_rules_menu".
 *
 * @property integer $id
 * @property string $category
 * @property integer $parent_id
 * @property integer $user_rules_id
 * @property string $label
 * @property string $url
 * @property integer $position
 * @property integer $status
 *
 * @property UserRules $userRules
 */
class UserRulesMenuBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_rules_menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category'], 'string'],
            [['parent_id', 'user_rules_id', 'position', 'status'], 'integer'],
            [['user_rules_id', 'label', 'url', 'position'], 'required'],
            [['label', 'url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category' => Yii::t('app', 'Category'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'user_rules_id' => Yii::t('app', 'User Rules ID'),
            'label' => Yii::t('app', 'Label'),
            'url' => Yii::t('app', 'Url'),
            'position' => Yii::t('app', 'Position'),
            'status' => Yii::t('app', '0 - inactive, 1 - active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRules()
    {
        return $this->hasOne(UserRules::className(), ['id' => 'user_rules_id']);
    }
}
