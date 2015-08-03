<?php
namespace SiteMaster\Plugins\Metric_w3c_html;

use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Auditor\Site\Page;
use SiteMaster\Core\DBTests\AbstractMetricDBTest;
use SiteMaster\Core\Registry\Site;

class MetricDBTest extends AbstractMetricDBTest
{
    /**
     * @test
     */
    public function markPage()
    {
        $this->setUpDB();

        $site = Site::getByBaseURL(self::INTEGRATION_TESTING_URL);

        //Schedule a scan
        $site->scheduleScan();

        $this->runScan();

        //get the scan
        $scan = $site->getLatestScan();

        $metric = new Metric('w3_html');
        $metric_record = $metric->getMetricRecord();
        
        $this->assertEquals(Scan::STATUS_COMPLETE, $scan->status, 'the scan should be completed');

        foreach ($scan->getPages() as $page) {
            return;
            if ($page->uri != self::INTEGRATION_TESTING_URL) {
                continue;
            }

            /**
             * @var $page Page
             */
            $grade = $page->getMetricGrade($metric_record->id);
            $errors = $grade->getErrors();

            $this->assertGreaterThan(0, $errors->count(), 'some errors should be logged');
        }
    }
    
    public function getPlugin()
    {
        return new Plugin();
    }
}
