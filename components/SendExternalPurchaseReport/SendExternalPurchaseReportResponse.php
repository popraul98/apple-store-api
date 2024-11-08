<?php

namespace app\components\SendExternalPurchaseReport;

use Readdle\AppStoreServerAPI\Request\AbstractRequest;
use Readdle\AppStoreServerAPI\Response\HistoryResponse;
use Readdle\AppStoreServerAPI\Response\AbstractResponse;

/**
 * @method static HistoryResponse createFromString(string $string, AbstractRequest $originalRequest)
 */
class SendExternalPurchaseReportResponse extends AbstractResponse
{
    protected array $response;

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    public function __construct(array $data)
    {
        $this->response = $data; // Or populate specific properties
    }
}