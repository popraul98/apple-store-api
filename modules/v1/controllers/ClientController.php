<?php

namespace app\modules\v1\controllers;

use app\components\AppStoreServerApiExtend\AppStoreServerApiDecorator;
use Readdle\AppStoreServerAPI\Exception\WrongEnvironmentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use Readdle\AppStoreServerAPI\Environment;
use app\components\AppStoreServerApiExtend\EnvironmentExternalApi;
use yii\web\UnauthorizedHttpException;

class ClientController extends Controller
{
    const secret_client = "4491e8e92fffd6508bd6508db06c89de";

    public ?AppStoreServerAPIDecorator $api = null;
    public ?array $data = null;

    public function beforeAction($action)
    {
        $keys = ['issuerId', 'bundleId', 'keyId', 'key', 'env'];
        
        $credentialsRaw = json_encode(\Yii::$app->getRequest()->getBodyParams());
        
        $credentials = json_decode($credentialsRaw, true);

        if(!isset($credentials['secret_client']) || $credentials['secret_client'] != self::secret_client){
            throw new UnauthorizedHttpException(\Yii::t('yii', 'Unauthorized'));
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException("Body contains invalid JSON");
        }

        if (array_diff($keys, array_keys($credentials))) {
            throw new BadRequestHttpException("Check Json body for presence of the following keys: " . join(',', $keys));
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
            $this->data = $credentials;
        } catch (WrongEnvironmentException $e) {
            throw new \Exception($e->getMessage());
        }

        return parent::beforeAction($action);
    }
}