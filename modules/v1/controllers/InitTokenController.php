<?php

namespace app\modules\v1\controllers;

use app\models\ExternalPurchaseToken;
use OpenApi\Annotations as OA;

class InitTokenController extends ClientController
{
    /**
     * @OA\Post(
     *     path="/v1/init-token",
     *     summary="Receive tokens",
     *     description="Receives tokens and stores transaction details.",
     *     tags={"Tokens"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"secret_client", "env", "issuerId", "bundleId", "keyId", "key", "base64_token"},
     *             @OA\Property(
     *                 property="secret_client",
     *                 type="string",
     *                 description="Client secret key."
     *             ),
     *             @OA\Property(
     *                 property="env",
     *                 type="string",
     *                 description="Environment identifier (e.g., Sandbox or Production)."
     *             ),
     *             @OA\Property(
     *                 property="issuerId",
     *                 type="string",
     *                 description="Issuer ID of the client."
     *             ),
     *             @OA\Property(
     *                 property="bundleId",
     *                 type="string",
     *                 description="Bundle ID of the application."
     *             ),
     *             @OA\Property(
     *                 property="keyId",
     *                 type="string",
     *                 description="Key ID associated with the private key."
     *             ),
     *             @OA\Property(
     *                 property="key",
     *                 type="string",
     *                 description="Private key in PEM format."
     *             ),
     *             @OA\Property(
     *                 property="base64_token",
     *                 type="string",
     *                 description="Base64-encoded token for transactions."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token saved processed.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="code",
     *                 type="integer",
     *                 description="Response code."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Response message."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Save error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="code",
     *                 type="integer",
     *                 example="1",
     *                 description="Error code."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="success",
     *                 description="Error message."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="code",
     *                 type="integer",
     *                 description="Error code."
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message."
     *             )
     *         )
     *     )
     * )
     */
    public function actionIndex()
    {
        $external_purchase_token = ExternalPurchaseToken::find()->where(['base64_token' => $this->data['base64_token']])->one();

        if (!$external_purchase_token) {
            $external_purchase_token = new ExternalPurchaseToken();
            $external_purchase_token->notify_time = null;
            $external_purchase_token->external_purchase_id = $this->data['externalPurchaseId'];
            $external_purchase_token->base64_token = $this->data['base64_token'];
            $external_purchase_token->bundle = $this->data['bundleId'];
            $external_purchase_token->created_at = time();

            if ($external_purchase_token->save()) {
                return array([
                    'code' => 0,
                    'message' => "success"
                ]);
            }

            return array([
                'code' => 1,
                'message' => $external_purchase_token->validate()
            ]);
        }
        return array([
            'code' => 1,
            'message' => "Token already exists."
        ]);
    }

}
