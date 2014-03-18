<?php
namespace SiteMaster\Plugins\Metric_w3c_html;

use SiteMaster\Core\Auditor\Logger\Metrics;
use SiteMaster\Core\Auditor\MetricInterface;
use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Auditor\Site\Page;

class Metric extends MetricInterface
{

    /**
     * @param string $plugin_name
     * @param array $options
     */
    public function __construct($plugin_name, array $options = array())
    {
        $options = array_replace_recursive(array(
            'service_url' => 'http://validator.w3.org/check',
            'help_text' => array()
        ), $options);

        parent::__construct($plugin_name, $options);
    }

    /**
     * Get the human readable name of this metric
     *
     * @return string The human readable name of the metric
     */
    public function getName()
    {
        return 'W3C HTML Validator';
    }

    /**
     * Get the Machine name of this metric
     *
     * This is what defines this metric in the database
     *
     * @return string The unique string name of this metric
     */
    public function getMachineName()
    {
        return 'w3c_html';
    }

    /**
     * Determine if this metric should be graded as pass-fail
     *
     * @return bool True if pass-fail, False if normally graded
     */
    public function isPassFail()
    {
        return true;
    }

    /**
     * Scan a given URI and apply all marks to it.
     *
     * All that this
     *
     * @param string $uri The uri to scan
     * @param \DOMXPath $xpath The xpath of the uri
     * @param int $depth The current depth of the scan
     * @param \SiteMaster\Core\Auditor\Site\Page $page The current page to scan
     * @param \SiteMaster\Core\Auditor\Logger\Metrics $context The logger class which calls this method, you can access the spider, page, and scan from this
     * @throws \Exception
     * @return bool True if there was a successful scan, false if not.  If false, the metric will be graded as incomplete
     */
    public function scan($uri, \DOMXPath $xpath, $depth, Page $page, Metrics $context)
    {
        $result = $this->getResults($uri);
        
        if (!$result) {
            return false;
        }

        foreach ($result->errors as $error) {
            $machine_name = md5($error->message);
            $mark = $this->getMark($machine_name, $error->message, 1, '', $this->getHelpText($machine_name));

            $page->addMark($mark, array(
                'line'    => $error->line,
                'col'     => $error->col,
                'context' => $error->source,
            ));
        }

        return true;
    }

    /**
     * Get the help text for a mark by machine_name
     * 
     * @param string $machine_name
     * @return null|string
     */
    public function getHelpText($machine_name)
    {
        if (isset($this->options['help_text'][$machine_name])) {
            return $this->options['help_text'][$machine_name];
        }
        
        return null;
    }

    /**
     * Get the results for a given uri
     *
     * @param $uri
     * @return bool|mixed
     */
    public function getResults($uri)
    {
        $validator = new \Services_W3C_HTMLValidator(array(
            'validator_uri' => $this->options['service_url']
        ));
        $result = $validator->validate($uri);
        
        return $result;
    }
}
