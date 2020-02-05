<?php

namespace common\models\base;

use Yii;

/**
 * This is the model class for table "notes".
*
    * @property integer $id
    * @property string $color_code
    * @property string $title
*/
class NotesBase extends \yii\db\ActiveRecord
{
/**
* @inheritdoc
*/
public static function tableName()
{
return 'notes';
}

/**
* @inheritdoc
*/
public function rules()
{
        return [
            [['color_code', 'title'], 'required'],
            [['color_code', 'title'], 'string', 'max' => 255],
        ];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => 'ID',
    'color_code' => 'Color Code',
    'title' => 'Title',
];
}
}