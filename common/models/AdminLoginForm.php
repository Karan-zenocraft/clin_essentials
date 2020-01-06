<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class AdminLoginForm extends Model
{

    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;
    const ACTIVE_STATUS = '1';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'username' => 'Email',
            'password' => 'Password',
            'rememberMe' => 'Remember me',
        );
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || $user->password !== md5($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Incorrect username or password.'));
            } else if (!$user || $user->status != self::ACTIVE_STATUS) {
                $this->addError($attribute, Yii::t('app', 'Your Acount has been in active so please contact to administrator.'));
            } else if ($user->role_id == Yii::$app->params['userroles']['customer']) {
                $this->addError($attribute, Yii::t('app', 'You are not authourize to login.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
        if ($this->validate()) {

            if ($this->rememberMe == 1) {
                setcookie(\Yii::getAlias('site_title') . "_admin_email", $this->username, time() + 3600 * 24 * 4);
                setcookie(\Yii::getAlias('site_title') . "_admin_password", $this->password, time() + 3600 * 24 * 4);
            } else {
                setcookie(\Yii::getAlias('site_title') . "_admin_email", '');
                setcookie(\Yii::getAlias('site_title') . "_admin_password", '');
            }
            return Yii::$app->user->login($this->getUser(), $duration);

        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Users::findByUsername($this->username);
        }
        return $this->_user;
    }

}
