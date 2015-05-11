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
            $name = $this->getMarkNameFromMessage($error->message);

            $machine_name = md5($name);
            
            $mark = $this->getMark($machine_name, $name, 1, '', $this->getHelpText($machine_name));
            
            $value_found = null;
            if ($name != $error->message) {
                $value_found = $error->message;
            }

            $page->addMark($mark, array(
                'line'        => $error->line,
                'col'         => $error->col,
                'context'     => $error->source, 
                'value_found' => $value_found,
            ));
        }

        return true;
    }

    /**
     * Get the machine name for a given error message
     * 
     * @param $error_message
     * @return string
     */
    public function getMarkNameFromMessage($error_message)
    {
        switch (true) {
            case (preg_match('/attribute (.*) not allowed on element (.*) at this point./i', $error_message)) :
                $name = 'Attribute _ is not allowed on _ element';
                break;
            case (preg_match('/Bad value (.*) for attribute (.*) on element (.*): Illegal character in query: not a URL code point./i', $error_message)) :
                $name = 'Bad value _ for attribute _ on element _: Illegal character in query';
                break;
            case (preg_match('/Duplicate ID (.*)./i', $error_message)) :
                $name = 'Duplicate ID _';
                break;
            case (preg_match('/(.*) is not a member of a group specified for any attribute/i', $error_message)) :
                $name = '_ is not a member of a group specified for any attribute';
                break;
            case (preg_match('/Bad value (.*) for attribute (.*) on element (.*): Expected a digit but saw (.*) instead./i', $error_message)) :
                $name = 'Bad value _ for attribute _ on element _: Expected a digit but saw _ instead';
                break;
            case (preg_match('/reference to entity (.*) for which no system identifier could be generated/i', $error_message)) :
                $name = 'reference to entity _ for which no system identifier could be generated';
                break;
            default:
                $name = $error_message;
                break;
        }

        return $name;
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

        $request = new \HTTP_Request2();
        $request->setConfig('adapter', 'HTTP_Request2_Adapter_Curl');
        $validator->setRequest($request);
        
        $result = $validator->validate($uri);
        
        return $result;
    }
}
