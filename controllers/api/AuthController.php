<?php

namespace app\controllers\api;

use app\components\RoleTrait;
use app\controllers\BaseController;
use app\models\LoginForm;
use app\models\Token;
use Throwable;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;

class AuthController extends BaseController
{
    use RoleTrait;

    public $modelClass = 'app\models\LoginForm';

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        if (\Yii::$app->request->isPost) {
            $behaviors['authenticator'] = [
                'class' => HttpBearerAuth::class,
                'only' => ['logout']
            ];

        }
        return $behaviors;
    }

    /**
     * @return Response
     */
    public function actionLogin(): Response
    {
        try {
            $model = new LoginForm();
            $model->load(Yii::$app->request->post(), '');
            $login = $model->login();

            if ($login) {
                return $this->asJson([
                    'success' => true,
                    'message' => 'Login successful',
                    'user_data' => [
                        'id' => $model->user->id,
                        'name' => $model->user->username,
                        'email' => $model->user->email,
                        'access_token' => $login,
                        'role' => $this->getUserRoleName($model->user)
                    ]
                ]);
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Login failed. Provide correct credentials',
                    'errors' => $model->getErrors(),
                ])->setStatusCode(401);
            }
        } catch (\Exception $exception) {
            return $this->asJson([
                'success' => false,
                'message' => $exception->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * @return Response
     * @throws Throwable
     */
    public function actionLogout(): Response
    {
        try {
            $user = Yii::$app->user;

            $tokens = Token::find()
                ->where(['user_id' => $user->id])
                ->all();

            foreach ($tokens as $token) {
                $token->delete();
            }

            $user->logout();

            return $this->asJson([
                'success' => true,
                'message' => 'Logged out'
            ]);
        } catch (\Exception $exception) {
            return $this->asJson([
                'success' => false,
                'message' => $exception->getMessage()
            ])->setStatusCode(400);
        }

    }

}
