<?php
namespace SiteMaster\Plugins\Metric_w3c_html;

class MetricTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getMarkNameFromMessage()
    {
        $metric = new Metric('metric_w3c_html');
        
        $this->assertEquals(
            'Attribute _ is not allowed on _ element',
            $metric->getMarkNameFromMessage('Attribute &" not allowed on element iframe at this point.')
        );
        
        $this->assertEquals(
            'Bad value _ for attribute _ on element _: Illegal character in query',
            $metric->getMarkNameFromMessage('Bad value mailto:?body=Check%20out%20this%20page%20http://otoe.unl.edu/educational-areas&subject=Water & Environment; UNL Extension in Otoe County for attribute href on element a: Illegal character in query: not a URL code point.')
        );
        
        $this->assertEquals(
            'Duplicate ID _',
            $metric->getMarkNameFromMessage('Duplicate ID s-lg-col-2.')
        );

        $this->assertEquals(
            '_ is not a member of a group specified for any attribute',
            $metric->getMarkNameFromMessage('"SOFT" is not a member of a group specified for any attribute')
        );

        $this->assertEquals(
            'Bad value _ for attribute _ on element _: Expected a digit but saw _ instead',
            $metric->getMarkNameFromMessage('Bad value 130px for attribute width on element img: Expected a digit but saw p instead.')
        );
    }
}
