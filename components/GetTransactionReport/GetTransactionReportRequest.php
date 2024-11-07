<?php
namespace app\components\GetTransactionReport;

use Readdle\AppStoreServerAPI\Request\AbstractRequest;

class GetTransactionReportRequest extends AbstractRequest
{
    public function getHTTPMethod(): string
    {
        return self::HTTP_METHOD_GET;
    }

    protected function getURLPattern(): string
    {
        return '{baseUrl}/v1/reports/{requestIdentifier}';
    }
}