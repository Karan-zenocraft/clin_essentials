<?php

namespace common\models\base;

use Yii;

/**
* This is the model class for table "email_format".
*
    * @property integer $id
    * @property string $title
    * @property string $subject
    * @property string $body
    * @property integer $status
    * @property string $created_at
    * @property string $updated_at
*/
class EmailFormatBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'email_format';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['title', 'subject', 'body', 'created_at'], 'required'],
            [['body'], 'string'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'subject'], 'string', 'max' => 255]
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => Yii::t('app', 'ID'),
    'title' => Yii::t('app', 'Title'),
    'subject' => Yii::t('app', 'Subject'),
    'body' => Yii::t('app', 'Body'),
    'status' => Yii::t('app', '1=Active, 0=In-Active'),
    'created_at' => Yii::t('app', 'Created At'),
    'updated_at' => Yii::t('app', 'Updated At'),
];
}
}