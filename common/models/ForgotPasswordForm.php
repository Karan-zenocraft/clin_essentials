<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * ForgotPasswordForm form
 */
class ForgotPasswordForm extends Model {

    public $email;
    
   /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['email'], 'required'],
            ['email', 'email'],
            ['email','validateEmail','on'=>'forgot_password'],
            // Front side validate email.
            ['email','frontvalidateEmail','on'=>'front_forgot_password'],
            ];
    }

    public function validateEmail(){
        $ASvalidateemail = Users::find()->where("email = '".$this->email."' && role_id IN(1,2)")->all();
        if(empty($ASvalidateemail)){
             $this->addError('email', 'Incorrect Email Address.');
             return true;
        }

    }
    public function frontvalidateEmail(){
        $ASvalidateemail = Users::find()->where("email = '".$this->email."' && role_id IN(3,4)")->all();
        if(empty($ASvalidateemail)){
             $this->addError('email', 'Incorrect Email Address.');
             return true;
        }

    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
           'email' => Yii::t('app', 'Email'),
        );
    } 

    
}
