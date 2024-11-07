<?php

namespace app\modules\v1\controllers;

use yii\console\Controller;
/**
 * @OA\Info(
 *     title="Apple Server External Api",
 *     version="1.0",
 *      ),
 * @OA\SecurityScheme(
 *       securityScheme="bearerAuth",
 *       in="header",
 *       name="Authorization",
 *       type="http",
 *       scheme="Bearer",
 *       bearerFormat="JWT",
 *  ),
 * @OA\Server(
 *       url="http://localhost/yii-apple/web/v1/",
 *       description="Local API Server",
 *  ),
 */
class SwaggerController extends Controller
{
    
    public function actions()
    {   
        return [
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => \yii\helpers\Url::to(['/v1/swagger/api'], true),
            ],
            //The resultUrl action.
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                'scanDir' => [
                    \Yii::getAlias('@app/modules/v1/controllers'),
                ],
                'api_key' => 'yii-appple=api',
            ],
        ];
    }
}
