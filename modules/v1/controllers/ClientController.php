<?php

namespace app\modules\v1\controllers;

use app\components\AppStoreServerApiExtend\AppStoreServerApiDecorator;
use Readdle\AppStoreServerAPI\Exception\WrongEnvironmentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use Readdle\AppStoreServerAPI\Environment;
use app\components\AppStoreServerApiExtend\EnvironmentExternalApi;

class ClientController extends Controller
{
    public ?AppStoreServerAPIDecorator $api = null;
    public ?array $credentials = null;

    public function beforeAction($action)
    {
        $keys = ['issuerId', 'bundleId', 'keyId', 'key', 'env'];
        
        $credentialsRaw = json_encode(\Yii::$app->getRequest()->getBodyParams());
        
        $credentials = json_decode($credentialsRaw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Body contains invalid JSON");
        }

        if (array_diff($keys, array_keys($credentials))) {
            throw new \Exception("Check Json body for presence of the following keys: " . join(',', $keys));
        }

        if (!in_array($credentials['env'], [Environment::PRODUCTION, Environment::SANDBOX, EnvironmentExternalApi::PRODUCTION_EXTERNAL, EnvironmentExternalApi::SANDBOX_EXTERNAL])) {
            throw new BadRequestHttpException("Json contains invalid environment name: {$credentials['env']}");
        }

        try {
            $this->api = new AppStoreServerApiDecorator(
                $credentials['env'],
                $credentials['issuerId'],
                $credentials['bundleId'],
                $credentials['keyId'],
                $credentials['key']
            );
            $this->credentials = $credentials;
        } catch (WrongEnvironmentException $e) {
            throw new \Exception($e->getMessage());
        }

        return parent::beforeAction($action);
    }
}