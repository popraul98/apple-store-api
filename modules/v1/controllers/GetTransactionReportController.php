<?php

namespace app\modules\v1\controllers;

use OpenApi\Annotations as OA;
use Readdle\AppStoreServerAPI\Exception\AppStoreServerAPIException;
use yii\web\Controller;


class GetTransactionReportController extends ClientController
{
    /**
     * @OA\Post(
     *     path="/v1/get-transaction-report",
     *     summary="Retrieve transaction report",
     *     description="Fetches an external purchase report based on the provided request identifier.",
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
        try {
            $reportInfoResponse = $this->api->retrieveExternalPurchaseReport($this->credentials['requestIdentifier']);
        } catch (AppStoreServerAPIException $e) {
            exit($e->getMessage());
        }

        $reportInfo = $reportInfoResponse->getReport();

        return json_encode($reportInfo);
    }
}
