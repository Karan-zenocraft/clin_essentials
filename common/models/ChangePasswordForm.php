<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * ChangePasswordForm form
 */
class ChangePasswordForm extends Model
{

    public $currentPassword;
    public $newPassword;
    public $retypePassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currentPassword', 'newPassword', 'retypePassword'], 'required'],
            ['currentPassword', 'validatepassword'],
            ['retypePassword', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    public function validatepassword()
    {
        $ASvalidatemodel = Users::findOne(Yii::$app->user->id);
        if (md5($this->currentPassword) != $ASvalidatemodel->password) {
            $this->addError('currentPassword', 'Please enter correct Password');
            return true;
        }

    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'currentPassword' => Yii::t('app', 'Current  Password'),
            'newPassword' => Yii::t('app', 'New Password'),
            'retypePassword' => Yii::t('app', 'Repeat New Password'),
        );
    }

}
