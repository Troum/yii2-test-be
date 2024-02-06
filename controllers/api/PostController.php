<?php

namespace app\controllers\api;

use app\components\RoleTrait;
use app\controllers\BaseController;
use app\models\Post;
use Carbon\Carbon;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;

class PostController extends BaseController
{
    use RoleTrait;

    public $defaultAction = 'posts';
    public $modelClass = 'app\models\Post';

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        if (Yii::$app->request->isPost) {
            $behaviors['authenticator'] = [
                'class' => HttpBearerAuth::class,
                'only' => ['create', 'update', 'publish-post']
            ];

            $behaviors['access'] = [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'publish-post'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['customer', 'admin', 'moderator'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->can('createPost');
                        },
                    ],
                    [
                        'actions' => ['publish-post'],
                        'allow' => true,
                        'roles' => ['admin', 'moderator'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->can('publishPost');
                        },
                    ],
                ],
            ];
        } else if (\Yii::$app->request->isGet) {
            $behaviors['authenticator'] = [
                'class' => HttpBearerAuth::class,
                'only' => ['my-posts']
            ];
        }
        return $behaviors;
    }

    /**
     * @return ActiveDataProvider
     */
    public function actionPosts(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Post::find()->with('user')->andWhere(['is_published' => 1]),
            'sort' => [
                'defaultOrder' => [
                    'published_at' => SORT_DESC,
                ]
            ]
        ]);
    }

    /**
     * @return Response
     */
    public function actionMyPosts(): Response
    {
        $user = Yii::$app->user->identity;
        $userId = $user->id;
        $roleName = $this->getUserRoleName($user);

        if ($this->isCustomer($roleName)) {
            return $this->asJson($this->modelClass::find()->with('user')->where(['user_id' => $userId])->all());
        } else if ($this->isModerator($roleName) || $this->isAdmin($roleName)) {
            return $this->asJson($this->modelClass::find()->with('user')->all());
        } else {
            return $this->asJson([
                'success' => false,
                'message' => 'Something went wrong'
            ])->setStatusCode(400);
        }

    }

    /**
     * @return Response
     */
    public function actionPublishPost(): Response
    {
        try {
            $post_id = Yii::$app->request->post('post_id');
            $post = Post::findById($post_id);
            if (!$post) {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Post wasn\'t found'
                ])->setStatusCode(404);
            }
            $post->is_published = true;
            $post->published_at = Carbon::now()->format('Y-m-d');
            if ($post->save(false)) {
                return $this->asJson([
                    'success' => true,
                    'message' => 'Post was published'
                ]);
            }
            return $this->asJson([
                'success' => false,
                'message' => 'Something went wrong'
            ])->setStatusCode(400);
        } catch (\Exception $exception) {
            return $this->asJson([
                'success' => false,
                'message' => 'Something went wrong'
            ])->setStatusCode(400);
        }
    }

}
