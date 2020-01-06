<?php

namespace common\models;

Use Yii;

class EmailFormat extends \common\models\base\EmailFormatBase
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
                    [['title', 'subject', 'body'], 'required'],
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
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}