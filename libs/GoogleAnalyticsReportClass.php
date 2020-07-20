<?php


namespace src\libs;


use Dotenv\Dotenv;
use Google_Client;
use Google_Exception;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_Dimension;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_ReportRequest;
use Google_Service_AnalyticsReporting_ReportRow;

class GoogleAnalyticsReportClass
{
    private $viewId;
    private $credentialFilePath;

    /**
     * GoogleAnalyticsReportClass constructor.
     */
    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $this->viewId = $_ENV['GOOGLE_ANALYTICS_VIEW_ID'];
        $this->credentialFilePath = __DIR__ . $_ENV['GOOGLE_ANALYTICS_CREDENTIAL_FILE_PATH'];
    }

    /**
     * GA オブジェクトの初期設定
     *
     * @return Google_Service_AnalyticsReporting
     * @throws Google_Exception
     */
    private function getGoogleAnalyticsObj() {

        $client = new Google_Client();
        $client->setApplicationName('ga-api-test');
        $client->setAuthConfig($this->credentialFilePath);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

        return new Google_Service_AnalyticsReporting($client);
    }

    /**
     * @return Google_Service_AnalyticsReporting_ReportRow[]
     * @throws Google_Exception
     */
    public function getReportData() {
        // 抽出対象期間の指定
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate('2020-01-01');
        $dateRange->setEndDate('2020-07-16');

        // Metrics 指定

        // ユニークユーザー数の指定
        $usersMetric = new Google_Service_AnalyticsReporting_Metric();
        $usersMetric->setExpression('ga:users');

        // ページビュー数の指定
        $pageviewsMetric = new Google_Service_AnalyticsReporting_Metric();
        $pageviewsMetric->setExpression('ga:pageviews');

        // Dimension 指定

        // ページパスの指定
        $pagePathDimension = new Google_Service_AnalyticsReporting_Dimension();
        $pagePathDimension->setName('ga:pagePath');

        // Google Analytics へリクエストを送信
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->viewId);
        $request->setDateRanges($dateRange);
        $request->setDimensions([$pagePathDimension]);
        $request->setMetrics([
            $usersMetric,
            $pageviewsMetric,
        ]);

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests([$request]);

        $response = $this->getGoogleAnalyticsObj()->reports->batchGet($body);

        return $response->getReports()[0]->getData()->getRows();
    }
}