<?php

namespace common\models\base;

use common\models\ClinicalStudyProtocolQuery;
use common\models\Users;
use Yii;

/**
 * This is the model class for table "clinical_study_protocol".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $my_notes
 * @property array $protocol_array
 * @property string $patient_id
 * @property string $pdf_file_name
 * @property string $to_patient_email
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 */
class ClinicalStudyProtocolBase extends \yii\db\ActiveRecord
{
/**
 * @inheritdoc
 */
    public static function tableName()
    {
        return 'clinical_study_protocol';
    }

/**
 * @inheritdoc
 */
    public function rules()
    {
        return [
            [['user_id', 'my_notes', 'protocol_array', 'pdf_file_name', 'to_patient_email', 'created_at', 'updated_at'], 'required'],
            [['user_id'], 'integer'],
            [['my_notes'], 'string'],
            [['protocol_array', 'created_at', 'updated_at', 'pdf_password'], 'safe'],
            [['patient_id', 'pdf_file_name', 'to_patient_email'], 'string', 'max' => 255],
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
            'my_notes' => 'My Notes',
            'protocol_array' => 'Protocol Array',
            'patient_id' => 'Patient ID',
            'pdf_file_name' => 'Pdf File Name',
            'to_patient_email' => 'To Patient Email',
            'pdf_password' => 'Password of PDF',
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
     * @return ClinicalStudyProtocolQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClinicalStudyProtocolQuery(get_called_class());
    }
}
