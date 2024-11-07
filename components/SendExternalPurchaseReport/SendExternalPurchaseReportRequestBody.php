<?php

namespace app\components\SendExternalPurchaseReport;

use Readdle\AppStoreServerAPI\RequestBody\AbstractRequestBody;

final class SendExternalPurchaseReportRequestBody extends AbstractRequestBody
{
    //status of the transaction
    CONST LINE_ITEM = "LINE_ITEM";
    CONST NO_LINE_ITEM = "NO_LINE_ITEM";

    /**
     * When token is UNRECOGNIZED_TOKEN need:
     * "externalPurchaseId": "<externalPurchaseId from notification>"
     */
    CONST UNRECOGNIZED_TOKEN = "UNRECOGNIZED_TOKEN";
    
    
    protected string $requestIdentifier;
    protected string $externalPurchaseId;
    protected string $status;

    /**
     * #1
     * Correct data in a line item
     * To submit a line item with corrections, use the line item’s original lineItemId and include the restatement flag set to true. 
     * Make corrections to any type of line item: OneTimeBuyLineItem, SubscriptionBuyLineItem, and RefundLineItem.
     * 
     * IMPORTANT
     * Restated line items overwrite the originally reported line item. 
     * Include all the data in the line item — even fields that are the same as the previous version.
     * 
     * #2
     * Retract an erroneously submitted line item
     * If you submitted a line item in error and want Apple to ignore it, use the same lineItemId as in the original submission. 
     * Set both the restatement and erroneouslySubmitted fields to true. 
     * (You may undo this action by submitting the line item again, with restatement set to true, and erroneouslySubmitted set to false.) 
     * Be sure to include all of the original line item data fields, and recalculate the netAmountTaxExclusive field to correctly represent 
     * the net amount with the erroneously submitted line item accounted for.
     * 
     * https://developer.apple.com/documentation/externalpurchaseserverapi/reportcorrections
     */
    public static array $lineItems = [
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

    public static function mappingDataLineItems(array $sourceArray, array &$lineItems): void
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

}