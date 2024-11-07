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
    protected array $report;

    /**
     * @return array
     */
    public function getReport(): array
    {
        return $this->report;
    }

    public function __construct(array $data)
    {
        $this->report = $data; // Or populate specific properties
    }
}