<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "device_details".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $device_tocken
 * @property string $type
 * @property string $gcm_id
 * @property string $created_at
 */
class DeviceDetailsBase extends \yii\db\ActiveRecord
{
/**
 * @inheritdoc
 */
    public static function tableName()
    {
        return 'device_details';
    }

/**
 * @inheritdoc
 */
    public function rules()
    {
        return [
            [['user_id', 'type'], 'required'],
            [['user_id'], 'integer'],
            [['type', 'gcm_id'], 'string'],
            [['created_at'], 'safe'],
            [['device_tocken'], 'string', 'max' => 255],
        ];
    }

/**
 * @inheritdoc
 */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'device_tocken' => 'Device Tocken',
            'type' => 'Type',
            'gcm_id' => 'Gcm ID',
            'created_at' => 'Created At',
        ];
    }
}
