<?php
namespace SiteMaster\Plugins\Metric_w3c_html;

class MetricTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     * @dataProvider messageNameProvider
     */
    public function getMarkNameFromMessage($name, $message)
    {
        $metric = new Metric('metric_w3c_html');
        $this->assertEquals($name, $metric->getMarkNameFromMessage($message));
    }

    public function messageNameProvider()
    {
        return [
            [
                'Attribute _ is not allowed on _ element',
                'Attribute &" not allowed on element iframe at this point.'
            ],
            [
                'Bad value _ for attribute _ on element _',
                'Bad value mailto:?body=Check%20out%20this%20page%20http://otoe.unl.edu/educational-areas&subject=Water & Environment; UNL Extension in Otoe County for attribute href on element a: Illegal character in query: not a URL code point.'
            ],
            [
                'Bad value _ for the attribute _',
                'Bad value “” for the attribute “xmlns” (only “http://www.w3.org/1999/xhtml” permitted here).'
            ],
            [
                'Duplicate ID _',
                'Duplicate ID s-lg-col-2.'
            ],
            [
                '_ is not a member of a group specified for any attribute',
                '"SOFT" is not a member of a group specified for any attribute'
            ],
            [
                'Bad value _ for attribute _ on element _',
                'Bad value 130px for attribute width on element img: Expected a digit but saw p instead.'
            ],
            [
                'Duplicate attribute _',
                'Duplicate attribute hello.'
            ],
            [
                'Stray _ tag _',
                'Stray end tag dvi.'
            ],
            [
                'No _ element in scope but a _ end tag seen',
                'No "p" element in scope but a "p" end tag seen.'
            ],
            [
                'Stray _ tag _',
                'Stray start tag "title".'
            ],
            [
                'Start tag _ seen in _',
                'Start tag "p" seen in "table".'
            ],
            [
                'Start tag _ seen but an element of the same type was already open',
                'Start tag body seen but an element of the same type was already open.'
            ],
            [
                'End tag _ seen, but there were unclosed elements',
                'End tag "aside" seen, but there were unclosed elements.'
            ],
            [
                'Unclosed element _',
                'Unclosed element ul.'
            ],
            [
                'Text not allowed in element _ in this context',
                'Text not allowed in element ul in this context.'
            ],
            [
                'Bad character _ in tag open state',
                'Bad character "3" after "<". Probable cause: Unescaped "<". Try escaping it as "<".'
            ],
            [
                'The element _ must not appear as a descendant of the _ element',
                'The element "form" must not appear as a descendant of the "form" element.'
            ],
            [
                'Element _ is missing a required child element',
                'Element "dl" is missing a required instance of child element "dd".'
            ],
            [
                'Element _ is missing a required child element',
                'Element dl is missing a required child element.'
            ],
            [
                'Element _ is missing required attribute _',
                'Element img is missing required attribute src.'
            ],
            [
                'A table row was _ columns wide and exceeded the column count established using column markup',
                'A table row was 99 columns wide and exceeded the column count established using column markup (5).'
            ],
            [
                'Table column _ established by element _ has no cells beginning',
                'Table column 3 established by element “th” has no cells beginning in it.'
            ],
            [
                'Table column _ established by element _ has no cells beginning',
                'Table columns in range 2…5 established by element “td” have no cells beginning in them.'
            ],
            [
                'Row _ of a row group established by a _ element has no cells beginning on it',
                'Row 48 of a row group established by a “tbody” element has no cells beginning on it.'
            ],
            [
                'The headers attribute on the element _ refers to the ID _, but there is no th element with that ID in the same table',
                'The “headers” attribute on the element “td” refers to the ID “sresearch”, but there is no “th” element with that ID in the same table.'
            ],
        ];
    }
}
