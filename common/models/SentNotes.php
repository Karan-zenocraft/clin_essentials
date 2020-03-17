<?php

namespace common\models;

use Yii;

class SentNotes extends \common\models\base\SentNotesBase
{
    public function beforeSave($insert)
    {
        date_default_timezone_set(Yii::$app->params['timezone']);
        if ($this->isNewRecord) {
            $this->setAttribute('created_at', date('Y-m-d H:i:s'));
        }
        $this->setAttribute('updated_at', date('Y-m-d H:i:s'));

        return parent::beforeSave($insert);
    }
}
