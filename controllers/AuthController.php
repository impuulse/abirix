<?php

namespace app\controllers;

use app\models\form\LoginForm;
use app\models\form\SignupForm;
use Yii;

/**
 * Контроллер аутентификации
 */
class AuthController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options', 'login', 'register'];
        return $behaviors;
    }

    /**
     * @return LoginForm|array
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if ($model->auth()) {
            $this->response['token'] = $model->getUser()->auth_key;
            $this->response['email'] = $model->getUser()->email;
            $this->response['message'] = 'Все ок, мы авторизовались!';
        } else {
            $this->response['message'] = $model->getUser()->errors;
        }
        return $this->response;
    }

    /**
     * @return SignupForm|array
     */
    public function actionRegister()
    {
        $params = Yii::$app->request->bodyParams;
        $signupForm = new SignupForm();
        $signupForm->setAttributes($params);
        if ($signupForm->signup()) {
            $this->response['message'] = 'Регистрация успешна...';
        } else {
            $this->response['message'] = 'Что то пошло не так...';
        }
        return $this->response;
    }
}
