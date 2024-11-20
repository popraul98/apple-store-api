<?php

namespace app\modules\v1\controllers;

use app\components\AppStoreServerApiExtend\EnvironmentExternalApi;
use app\components\SendExternalPurchaseReport\SendExternalPurchaseReportRequest;
use app\components\SendExternalPurchaseReport\SendExternalPurchaseReportRequestBody;
use app\components\SendExternalPurchaseReport\SendExternalPurchaseReportResponse;
use app\models\ExternalPurchaseToken;
use OpenApi\Annotations as OA;
use Readdle\AppStoreServerAPI\Environment;
use Readdle\AppStoreServerAPI\Exception\AppStoreServerAPIException;
use Readdle\AppStoreServerAPI\RequestBody\ConsumptionRequestBody;
use yii\db\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;


class SendExternalPurchaseReportController extends ClientController
{
    public array $lineItems = [
        'lineItemId' => '',
        'creationDate' => '',
        'eventType' => '',
        'productType' => '',
        'productIdentifier' => '',
        'amountTaxInclusive' => '',
        'amountTaxExclusive' => '',
        'taxAmount' => '',
        'netAmountTaxExclusive' => '',
        'reportingCurrency' => '',
        'pricingCurrency' => '',
        'taxCountry' => '',
        'quantity' => '',
        'restatement' => false,
        'erroneouslySubmitted' => false,
    ];
    
    /**
     * @OA\Post(
     *     path="/v1/send-external-purchase-report",
     *     summary="Send reports to Apple",
     *     description="Send reports to Apple, informations about transactions and tokens",
     *     tags={"Transaction Reports"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              required={"secret_client", "env", "issuerId", "bundleId", "keyId", "key", "base64_token"},
     *              @OA\Property(
     *                  property="secret_client",
     *                  type="string",
     *                  description="Client secret key."
     *              ),
     *              @OA\Property(
     *                  property="env",
     *                  type="string",
     *                  description="Environment identifier (e.g., Sandbox or Production)."
     *              ),
     *              @OA\Property(
     *                  property="issuerId",
     *                  type="string",
     *                  description="Issuer ID of the client."
     *              ),
     *              @OA\Property(
     *                  property="bundleId",
     *                  type="string",
     *                  description="Bundle ID of the application."
     *              ),
     *              @OA\Property(
     *                  property="keyId",
     *                  type="string",
     *                  description="Key ID associated with the private key."
     *              ),
     *              @OA\Property(
     *                  property="key",
     *                  type="string",
     *                  description="Private key in PEM format."
     *              ),
     *              @OA\Property(
     *                  property="base64_token",
     *                  type="string",
     *                  description="Base64-encoded token for transactions."
     *              ),
     *              @OA\Property(
     *                  property="lineItems",
     *                  type="array",
     *                  description="The report data",
     *                  @OA\Items(type="object")
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction report retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
                   @OA\Property(
     *                   property="requestIdentifier",
     *                   type="string",
     *                   description="requestIdentifier that have send with report"
     *               ),
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
        if (isset($this->data['status']) && !in_array($this->data['status'], [SendExternalPurchaseReportRequestBody::LINE_ITEM, SendExternalPurchaseReportRequestBody::NO_LINE_ITEM, SendExternalPurchaseReportRequestBody::UNRECOGNIZED_TOKEN])) {
            throw new BadRequestHttpException("Json contains invalid environment name: {$this->data['status']}");
        }
        
        try {
            $external_purchase_token = ExternalPurchaseToken::find()->where(['external_purchase_id' => $this->data['externalPurchaseId']])->one();
            if($external_purchase_token){
                $external_purchase_token->setNotifyTime();
            }
                
            if($this->data['status'] == SendExternalPurchaseReportRequestBody::LINE_ITEM){

                $this->mappingDataLineItems($this->data,$this->lineItems);
                
                $validationResult = $this->validateLineItem($this->lineItems);
                
                if ($validationResult !== true) {
                    throw new BadRequestHttpException($validationResult);
                }
                
                if (!isset($this->data['externalPurchaseId']) || !isset($this->data['requestIdentifier'])){
                    throw new BadRequestHttpException("Parameter externalPurchaseId or requestIdentifier is missing.");
                }

                $infoResponse = $this->api->sendExternalPurchaseReport([
                    'requestIdentifier' => $this->data['requestIdentifier'],
                    'externalPurchaseId' => $this->data['externalPurchaseId'],
                    'status' => SendExternalPurchaseReportRequestBody::LINE_ITEM,
                    'lineItems' => [$this->lineItems]
                ]); 
                
            } elseif ($this->data['status'] == SendExternalPurchaseReportRequestBody::NO_LINE_ITEM){

                $infoResponse = $this->api->sendExternalPurchaseReport([
                    'requestIdentifier' => $this->data['requestIdentifier'],
                    'externalPurchaseId' => $this->data['externalPurchaseId'],
                    'status' => SendExternalPurchaseReportRequestBody::NO_LINE_ITEM,
                ]);
                
            } elseif ($this->data['status'] == SendExternalPurchaseReportRequestBody::UNRECOGNIZED_TOKEN){

                $infoResponse = $this->api->sendExternalPurchaseReport([
                    'requestIdentifier' => $this->data['requestIdentifier'],
                    'externalPurchaseId' => $this->data['externalPurchaseId'],
                    'status' => SendExternalPurchaseReportRequestBody::UNRECOGNIZED_TOKEN,
                ]);
                
            }
        } catch (AppStoreServerAPIException $e) {
            
            \Yii::$app->response->statusCode = $e->getCode();
            return ['errors' => $e->getMessage()];
        }
        
        return $infoResponse->getResponse();
    }

    protected function mappingDataLineItems(array $sourceArray, array &$lineItems): void
    {
        /**
         * Populates elements in $sourceArray from $lineItems, only for keys present in both arrays.
         *
         * @param array $sourceArray The source array.
         * @param array &$lineItems The target array (passed by reference).
         */

        foreach ($lineItems as $key => &$value) { //Note the & for pass by reference
            if (array_key_exists($key, $sourceArray)) {
                $value = $sourceArray[$key];
            }
        }
    }

    protected function validateLineItem(array $lineItem)
    {
        $requiredFields = array_keys($this->lineItems);

        foreach ($requiredFields as $field) {

            // Check for missing or empty required fields
            if (!isset($lineItem[$field]) || $lineItem[$field] === '') {
                return "The field '$field' is required and cannot be empty.";
            }

            // Handle numeric fields - ensure they are actually numeric
            if (in_array($field, ['amountTaxInclusive', 'amountTaxExclusive', 'taxAmount', 'netAmountTaxExclusive', 'quantity'])) {
                if (!is_numeric($lineItem[$field])) {
                    return "The field '$field' must be a numeric value.";
                }
            }
        }

        // Check for valid eventType
        if (!in_array($lineItem['eventType'], [SendExternalPurchaseReportRequestBody::BUY, SendExternalPurchaseReportRequestBody::REFUND])) {
            return "Invalid eventType: it must be either 'BUY' or 'REFUND'.";
        }

        // Check for valid productType
        if (!in_array($lineItem['productType'], [SendExternalPurchaseReportRequestBody::ONE_TIME_BUY, SendExternalPurchaseReportRequestBody::SUBSCRIPTION])) {
            return "Invalid productType: it must be either 'ONE_TIME_BUY' or 'SUBSCRIPTION'.";
        }

        // Check for valid currencies
        if (!in_array($lineItem['reportingCurrency'], SendExternalPurchaseReportRequestBody::ALLOWED_CURRENCIES) || !in_array($lineItem['pricingCurrency'], SendExternalPurchaseReportRequestBody::ALLOWED_CURRENCIES)) {
            return "Invalid currency: the reporting and pricing currencies must be allowed.";
        }

        // Check for valid taxCountry
        if (!in_array($lineItem['taxCountry'], [SendExternalPurchaseReportRequestBody::ROMANIA_PREFIX])) {
            return "Invalid taxCountry: '$lineItem[taxCountry]' is not allowed.";
        }

        return true; // All fields are valid
    }
    
    
}
