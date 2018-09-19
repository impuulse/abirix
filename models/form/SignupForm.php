<?php
namespace app\models\form;

use yii\base\Model;
use Yii;
use app\models\User;

/**
 * Signup form
 *
 * @property string $email
 * @property string $password
 */
class SignupForm extends Model
{
    public $email;
    public $password;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Email Already Exists'],

            ['password', 'string', 'min' => 6]
        ];
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

    /**
     * Signs user up
     * @return bool|null
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->setPassword($this->password);
            $user->email = $this->email;
            if ($user->save()) {
                return true;
            }
        }
        return null;
    }
}
