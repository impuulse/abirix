<?php

namespace app\models\form;

use yii\base\Model;
use app\models\User;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 * @property string $ios_push_token
 * @property string $android_push_token
 *
 */
class LoginForm extends Model
{
    public $email;
    public $password;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['email', 'email'],
            [['email', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword']
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
                $this->addError($attribute, 'Incorrect Email Or Password');
            }
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
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    public function auth()
    {
        if ($this->validate() && $this->generateToken()) {
            return $this->getUser()->auth_key;
        } else {
            return null;
        }
    }

    public function generateToken()
    {
        /* @var $user User */
        $user = $this->getUser();
        $user->auth_key = \Yii::$app->security->generateRandomString();
        $user->auth_key_expired_at = (new \DateTime())->modify('+ 1 month')->getTimestamp();
        if(!$user->save()){
            return false;
        }

        return true;
    }
}
