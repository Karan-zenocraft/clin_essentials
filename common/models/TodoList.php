<?php

namespace common\models;

class TodoList extends \common\models\base\TodoListBase
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
