<?php

namespace app\modules\v1\controllers;

use app\components\AppStoreServerApiExtend\EnvironmentExternalApi;
use app\components\SendExternalPurchaseReport\SendExternalPurchaseReportRequest;
use app\components\SendExternalPurchaseReport\SendExternalPurchaseReportRequestBody;
use app\components\SendExternalPurchaseReport\SendExternalPurchaseReportResponse;
use OpenApi\Annotations as OA;
use Readdle\AppStoreServerAPI\Environment;
use Readdle\AppStoreServerAPI\Exception\AppStoreServerAPIException;
use yii\web\Controller;


class SendExternalPurchaseReportController extends ClientController
{
    /**
     * @OA\Post(
     *     path="/v1/send-external-purchase-report",
     *     summary="Send reports to Apple",
     *     description="Send reports to Apple, informations about transactions and tokens",
     *     tags={"Transaction Reports"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="requestIdentifier",
     *                 type="string",
     *                 description="Unique identifier for the request."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction report retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="reportData",
     *                 type="array",
     *                 description="The report data",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request parameters"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function actionIndex()
    {
        if (!in_array($this->credentials['status'], [SendExternalPurchaseReportRequestBody::LINE_ITEM, SendExternalPurchaseReportRequestBody::NO_LINE_ITEM, SendExternalPurchaseReportRequestBody::UNRECOGNIZED_TOKEN])) {
            throw new \Exception("Json contains invalid environment name: {$this->credentials['status']}");
        }
        
        try {
            if($this->credentials['status'] == SendExternalPurchaseReportRequestBody::LINE_ITEM){

                SendExternalPurchaseReportRequestBody::mappingDataLineItems($this->credentials,SendExternalPurchaseReportRequestBody::$lineItems);
                
                //TODO: verificare toate fileds din $lineItems sunt populate si respecta tipul(CONST).
                
                var_dump('ssad');die;
                
                $this->api->sendExternalPurchaseReport([
                    'requestIdentifier' => $this->credentials['requestIdentifier'],
                    'externalPurchaseId' => $this->credentials['externalPurchaseId'],
                    'status' => SendExternalPurchaseReportRequestBody::LINE_ITEM,
                    'lineItems' => SendExternalPurchaseReportRequestBody::$lineItems 
                ]); 
                
            } elseif ($this->credentials['status'] == SendExternalPurchaseReportRequestBody::NO_LINE_ITEM){
                
                $this->api->sendExternalPurchaseReport([
                    'requestIdentifier' => $this->credentials['requestIdentifier'],
                    'externalPurchaseId' => $this->credentials['externalPurchaseId'],
                    'status' => SendExternalPurchaseReportRequestBody::NO_LINE_ITEM,
                ]);
                
            } elseif ($this->credentials['status'] == SendExternalPurchaseReportRequestBody::UNRECOGNIZED_TOKEN){
                
                $this->api->sendExternalPurchaseReport([
                    'requestIdentifier' => $this->credentials['requestIdentifier'],
                    'externalPurchaseId' => $this->credentials['externalPurchaseId'],
                    'status' => SendExternalPurchaseReportRequestBody::UNRECOGNIZED_TOKEN,
                ]);
                
            }
            
            
        } catch (AppStoreServerAPIException $e) {
            exit($e->getMessage());
        }

        $reportInfo = $reportInfoResponse->getReport();

        return json_encode($reportInfo);
    }
}
