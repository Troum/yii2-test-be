<?php

namespace app\controllers\api;

use app\controllers\BaseController;
use app\models\User;
use Yii;
use yii\base\Exception;

class RegistrationController extends BaseController
{
    public $modelClass = 'app\models\User';

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function actionRegister(): array
    {
        $request = Yii::$app->request->post();

        $user = new User();
        $user->attributes = $request;

        if($user->register())
        {
            $user->assignRole();
            return ['status' => 'success', 'message' => 'Registration successful'];
        }

        Yii::$app->response->statusCode = 400;
        return ['status' => 'error', 'errors' => $user->getErrors()];
    }
}
