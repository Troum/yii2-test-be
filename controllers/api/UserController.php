<?php

namespace app\controllers\api;

use app\controllers\BaseController;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;

class UserController extends BaseController
{
    public $defaultAction = 'users';
    public $modelClass = 'app\models\User';

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['create', 'update', 'index', 'set-role']
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['set-role'],
            'rules' => [
                [
                    'actions' => ['set-role'],
                    'allow' => true,
                    'roles' => ['admin'],
                    'matchCallback' => function ($rule, $action) {
                        return \Yii::$app->user->can('setRole');
                    },
                ]
            ],
        ];
        return $behaviors;
    }

    /**
     * @return Response
     */
    public function actionUsers(): Response
    {
        $users = User::find()->all();
        $authManager = Yii::$app->authManager;
        $userData = [];

        foreach ($users as $user) {
            $roles = $authManager->getRolesByUser($user->id);
            $roleNames = array_keys($roles);
            $names = reset($roleNames);
            $userData[] = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $names === false ? 'customer' : $names,
            ];
        }

        return $this->asJson($userData);
    }

    /**
     * @return Response
     */
    public function actionSetRole(): Response
    {
        try {
            $user_id = Yii::$app->request->post('id');
            $role = Yii::$app->request->post('role');
            $user = User::findIdentity($user_id);
            if (!$user) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'User wasn\'t found'
                ])->setStatusCode(404);
            }
            $user->assignRole($role);
            return $this->asJson([
                'success' => true,
                'message' => 'User role was changed to ' . $role
            ]);
        } catch (\Exception $exception) {
            return $this->asJson([
                'success' => false,
                'message' => 'Something went wrong'
            ])->setStatusCode(400);
        }
    }

    /**
     * @return Response
     * @throws \Throwable
     */
    public function actionDeleteUser(): Response
    {
        try {
            $user_id = Yii::$app->request->post('id');
            $user = User::findIdentity($user_id);
            if (!$user) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'User wasn\'t found'
                ])->setStatusCode(404);
            }
            $user->revokeAllRoles();
            $user->delete();
            return $this->asJson([
                'success' => true,
                'message' => 'User was deleter'
            ]);
        } catch (\Exception $exception) {
            return $this->asJson([
                'success' => false,
                'message' => 'Something went wrong'
            ])->setStatusCode(400);
        }
    }

}
