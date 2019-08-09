<?php
namespace SiteMaster\Plugins\Metric_w3c_html;

use HtmlValidator\Validator;
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
            'service_url' => 'https://validator.w3.org/nu/',
            'help_text' => array()
        ), $options);

        parent::__construct($plugin_name, $options);
    }

    /**
     *  This will allow custom overrides manually defined in overrides table to be honored.
     *
     * @return bool
     */
    public function allowCustomOverridingErrors()
    {
        return true;
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
        
        foreach ($result->getErrors() as $error) {
            /**
             * @var $error \HtmlValidator\Message
             */
            $name = $this->getMarkNameFromMessage($error->getText());

            $machine_name = md5($name);
            
            $mark = $this->getMark($machine_name, $name, 1, '', $this->getHelpText($machine_name));
            
            $value_found = null;
            if ($name != $error->getText()) {
                $value_found = $error->getText();
            }

            $page->addMark($mark, array(
                'line'        => $error->getFirstLine(),
                'col'         => $error->getFirstColumn(),
                'context'     => $error->getExtract(), 
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
            case (preg_match('/The (?:.*?) attribute on the (?:.*?) element is obsolete\. Use CSS instead\./i', $error_message)) :
                $name = 'The _ attribute on the _ element is obsolete. Use CSS instead';
                break;
            case (preg_match('/The (?:.*?) element is obsolete\. Use CSS instead\./i', $error_message)) :
                $name = 'The _ element is obsolete. Use CSS instead';
                break;
            case (preg_match('/Bad value .*for attribute .* on element/i', $error_message)) :
                $name = 'Bad value _ for attribute _ on element _';
                break;
            case (preg_match('/Bad value .*for the attribute/i', $error_message)) :
                $name = 'Bad value _ for the attribute _';
                break;
            case (preg_match('/Duplicate ID (.*)./i', $error_message)) :
                $name = 'Duplicate ID _';
                break;
            case (preg_match('/Duplicate attribute (?:.*)\./i', $error_message)) :
                $name = 'Duplicate attribute _';
                break;
            case (preg_match('/(.*) is not a member of a group specified for any attribute/i', $error_message)) :
                $name = '_ is not a member of a group specified for any attribute';
                break;
            case (preg_match('/reference to entity (.*) for which no system identifier could be generated/i', $error_message)) :
                $name = 'reference to entity _ for which no system identifier could be generated';
                break;
            case (preg_match('/Element .* not allowed as child of element .* in this context\. \(Suppressing further errors from this subtree\.\)/', $error_message)):
                $name = 'Element _ not allowed as child of element _ in this context';
                break;
            case (preg_match('/Text not allowed in element .* in this context\./', $error_message)):
                $name = 'Text not allowed in element _ in this context';
                break;
            case (preg_match('/Stray (?:start|end) tag (?:.*)\./', $error_message)):
                $name = 'Stray _ tag _';
                break;
            case (preg_match('/Start tag (?:.*) seen in (?:.*)\./', $error_message)):
                $name = 'Start tag _ seen in _';
                break;
            case (preg_match('/Start tag (?:.*) but an element of the same type was already open\./', $error_message)):
                $name = 'Start tag _ seen but an element of the same type was already open';
                break;
            case (preg_match('/End tag .* seen, but there were open elements\./', $error_message)):
                $name = 'End tag _ seen, but there were open elements';
                break;
            case (preg_match('/End tag(?: for)? .* (?:seen|implied), but there were (?:unclosed|open) elements\./', $error_message)):
                $name = 'End tag _ seen, but there were unclosed elements';
                break;
            case (preg_match('/End tag .* violates nesting rules\./', $error_message)):
                $name = 'End tag _ violates nesting rules';
                break;
            case (preg_match('/No .* element in scope but a .* end tag seen\./', $error_message)):
                $name = 'No _ element in scope but a _ end tag seen';
                break;
            case (preg_match('/Unclosed element .*\./', $error_message)):
                $name = 'Unclosed element _';
                break;
            case (preg_match('/Forbidden code point U\+/', $error_message)):
                $name = 'Forbidden code point _';
                break;
            case (preg_match('/Malformed byte sequence: /', $error_message)):
                $name = 'Malformed byte sequence _';
                break;
            case (preg_match('/Unmappable byte sequence: /', $error_message)):
                $name = 'Unmappable byte sequence _';
                break;
            case (preg_match('/Bad character .* after/', $error_message)):
                $name = 'Bad character _ in tag open state';
                break;
            case (preg_match('/The element .* must not appear as a descendant of the .* element\./', $error_message)):
                $name = 'The element _ must not appear as a descendant of the _ element';
                break;
            case (preg_match('/Element .* is missing a required(?: instance of)? child element.*\./', $error_message)):
                $name = 'Element _ is missing a required child element';
                break;
            case (preg_match('/Element .* is missing required attribute .*\./', $error_message)):
                $name = 'Element _ is missing required attribute _';
                break;
            case (preg_match('/A table row was \d+ columns wide and exceeded the column count established using column markup/', $error_message)):
                $name = 'A table row was _ columns wide and exceeded the column count established using column markup';
                break;
            case (preg_match('/Table column(?:s in range)? .* established by element .* (?:have|has) no cells beginning in (?:them|it)\./', $error_message)):
                $name = 'Table column _ established by element _ has no cells beginning';
                break;
            case (preg_match('/Row \d+ of a row group established by a .* element has no cells beginning on it\./', $error_message)):
                $name = 'Row _ of a row group established by a _ element has no cells beginning on it';
                break;
            case (preg_match('/The [“"]headers[”"] attribute on the element [“"]t[dh][”"] refers to the ID .*, but there is no [“"]th[”"] element with that ID in the same table\./u', $error_message)):
                $name = 'The headers attribute on the element _ refers to the ID _, but there is no th element with that ID in the same table';
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
     * @return \HtmlValidator\HtmlValidator\Response|\HtmlValidator\Response
     */
    public function getResults($uri)
    {
        $validator = new Validator($this->options['service_url']);
        
        try {
            return $validator->validateUrl($uri, ['checkErrorPages'=>true]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
