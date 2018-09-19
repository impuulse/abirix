<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $auth_key
 * @property string $auth_key_expired_at
 * @property string $password_hash
 * @property string $email
 */
class User extends ActiveRecord implements IdentityInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'email'],
            ['email', 'unique', 'message' => 'Такой email уже существует'],
            [['auth_key', 'password_hash', 'email'], 'string', 'max' => 255],
            [['auth_key_expired_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_key' => 'Auth Key',
            'auth_key_expired_at' => 'Access Token Expired At',
            'password_hash' => 'Password Hash',
            'email' => 'Email'
        ];
    }

    /**
     * Find by email
     * @param $email
     * @return User|array|null
     */
    public static function findByEmail($email)
    {
        return self::find()
            ->where(['email' => $email])
            ->one();
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return null|static
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['auth_key' => $token]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
}
