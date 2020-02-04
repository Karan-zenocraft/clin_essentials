<?php

namespace common\models\base;

use common\models\Notes;
use common\models\SentNotesQuery;
use common\models\Users;
use Yii;

/**
 * This is the model class for table "sent_notes".
 *
 * @property integer $id
 * @property integer $note_id
 * @property string $color_code
 * @property string $font_name
 * @property integer $font_size
 * @property string $title
 * @property string $description
 * @property integer $from_user_id
 * @property integer $to_patient_id
 * @property string $to_email_id
 * @property string $pdf_filename
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $fromUser
 * @property Notes $note
 */
class SentNotesBase extends \yii\db\ActiveRecord
{
/**
 * @inheritdoc
 */
    public static function tableName()
    {
        return 'sent_notes';
    }

/**
 * @inheritdoc
 */
    public function rules()
    {
        return [
            [['note_id', 'color_code', 'title', 'description', 'from_user_id', 'to_email_id', 'pdf_filename'], 'required'],
            [['note_id', 'font_size', 'from_user_id', 'to_patient_id'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'is_archive', 'mail_sent'], 'safe'],
            [['color_code', 'font_name', 'title', 'to_email_id', 'pdf_filename'], 'string', 'max' => 255],
            [['from_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['from_user_id' => 'id']],
            [['note_id'], 'exist', 'skipOnError' => true, 'targetClass' => Notes::className(), 'targetAttribute' => ['note_id' => 'id']],
        ];
    }

/**
 * @inheritdoc
 */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'note_id' => 'Note ID',
            'color_code' => 'Color Code',
            'font_name' => 'Font Name',
            'font_size' => 'Font Size',
            'title' => 'Title',
            'description' => 'Description',
            'from_user_id' => 'From User ID',
            'to_patient_id' => 'To Patient ID',
            'to_email_id' => 'To Email ID',
            'pdf_filename' => 'Pdf Filename',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_archive' => "Is Archive",
            'mail_sent' => "Is Mail Sent",
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFromUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'from_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNote()
    {
        return $this->hasOne(Notes::className(), ['id' => 'note_id']);
    }

    /**
     * @inheritdoc
     * @return SentNotesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SentNotesQuery(get_called_class());
    }
}
