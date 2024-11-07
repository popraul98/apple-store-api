<?php

namespace app\components\SendExternalPurchaseReport;

use Readdle\AppStoreServerAPI\Request\AbstractRequest;

class SendExternalPurchaseReportRequest extends AbstractRequest
{
    public function getHTTPMethod(): string
    {
        return self::HTTP_METHOD_PUT;
    }

    protected function getURLPattern(): string
    {
        return '{baseUrl}/v1/reports';
    }
}