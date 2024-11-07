<?php

namespace app\modules\v1\controllers;

use Readdle\AppStoreServerAPI\Exception\AppStoreServerAPIException;
use yii\rest\ActiveController;
use yii\web\Controller;

/**
 * Default controller for the `v1` module
 */
class DefaultController extends ActiveController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        echo "work";
    }
}
