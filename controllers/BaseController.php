<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\web\Response;

class BaseController extends ActiveController
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['corsFilter'] = [
            'class' => '\yii\filters\Cors',
            'cors' => [
                'Origin' => ['http://localhost:3001'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Allow-Headers' => ['*'],
                'Access-Control-Allow-Methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
            ],
        ];
        return $behaviors;
    }
}