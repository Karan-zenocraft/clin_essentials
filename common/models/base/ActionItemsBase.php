<?php

namespace common\models\base;

use common\models\ActionItemsQuery;
use common\models\Users;
use Yii;

/**
 * This is the model class for table "action_items".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $protocol
 * @property string $investigator
 * @property string $date
 * @property array $action_items
 * @property string $patient_id
 * @property string $pdf_file_name
 * @property string $to_patient_email
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 */
class ActionItemsBase extends \yii\db\ActiveRecord
{
/**
 * @inheritdoc
 */
    public static function tableName()
    {
        return 'action_items';
    }

/**
 * @inheritdoc
 */
    public function rules()
    {
        return [
            [['user_id', 'protocol', 'investigator', 'date', 'action_items', 'pdf_file_name', 'to_patient_email', 'created_at', 'updated_at'], 'required'],
            [['user_id'], 'integer'],
            [['date', 'action_items', 'created_at', 'updated_at'], 'safe'],
            [['protocol', 'investigator', 'patient_id', 'pdf_file_name', 'to_patient_email'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'protocol' => 'Protocol',
            'investigator' => 'Investigator',
            'date' => 'Date',
            'action_items' => 'Action Items',
            'patient_id' => 'Patient ID',
            'pdf_file_name' => 'Pdf File Name',
            'to_patient_email' => 'To Patient Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return ActionItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ActionItemsQuery(get_called_class());
    }
}
