<?php

namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $user_name;
    public $password;

    private $_user = false;

    public function rules()
    {
        return [
            // username and password are both required
            [['user_name', 'password'], 'required'],
            // password is validated by validatePassword()
             //['password', 'validatePassword'],
        ];
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

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    //登陆之后返回id，name，role,department,job,
    public function login()
    {
        if ($this->validate()) {
            if(Yii::$app->user->login($this->getUser())){
                return 1;
            }
        }
        return false;
    }
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->user_name);
        }

        return $this->_user;
    }
}
