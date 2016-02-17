<?php

include_once __DIR__ . '/BaseParserTest.php';

class SerializedTest extends BaseParserTest {

    protected function setUp() {
        parent::setUp();
        $this->parser = new \EasyDomainChange\Parsers\Serialized();
    }

    public function test_Test() {
        $this->assertTrue($this->parser->test($this->serializedString), 'Serialized string is not recognized');
        $this->assertTrue($this->parser->test($this->hugeSerializedString), 'Huge serialized string is not recognized');
        $this->assertFalse($this->parser->test($this->jsonString), 'Must not recognize JSON');
        $this->assertFalse($this->parser->test($this->hugeJsonText), 'Must not recognize JSON');
        $this->assertFalse($this->parser->test($this->simpleText), 'Must not recognize simple text');
        $this->assertFalse($this->parser->test($this->cssText), 'Must not recognize CSS');
    }

    public function test_Unpack() {

        $eggs = new \stdClass();
        $eggs->bacon = new \stdClass();
        $eggs->otherComponent = '22';
        $eggs->bacon->sausages = new \stdClass();
        $eggs->bacon->sausages->spam = 3;
        $x = new \stdClass();
        $x->d = 7;
        $x->z = $eggs;

        $this->assertEquals($this->parser->unpack(serialize($x)), $x, 'Unpacked object is corrupted');
        $this->assertNotEmpty($this->parser->unpack($this->hugeSerializedString, true), 'Could not unpack the Serialized data');
        $this->assertNotEmpty($this->parser->unpack($this->serializedString, true), 'Could not unpack the Serialized data');
    }
    
    public function test_Replace() {
        
        $sample = array(
            0 => '/sample.domain.com/public_html/wp-content/themes/edental/style.css',
            2 => array(
                0 => array(),
                1 => 'sample.domain.com/public_html/',
                2 => array(
                    'a' => 'http://sample.domain.com/public_html/',
                    'b' => 'http://sample.domain.com/public_html/themes/edental',
                    'c' => array('some_var' => 'http://sample.domain.com/public_html/')
                )
            ),
        );

        $resultingArray = array(
            0 => '/new.example.com/public_html/wp-content/themes/edental/style.css',
            2 => array(
                0 => array(),
                1 => 'new.example.com/public_html/',
                2 => array(
                    'a' => 'http://new.example.com/public_html/',
                    'b' => 'http://new.example.com/public_html/themes/edental',
                    'c' => array('some_var' => 'http://new.example.com/public_html/')
                )
            ),
        );

        $this->assertEquals($resultingArray, $this->parser->replace($sample, 'sample.domain.com', 'new.example.com'), 'Could not do replacements');
    }
    
    public function test_Process() {
        $this->assertEquals(file_get_contents(__DIR__ . '/serialized_text_resulting_sample.txt'), $this->parser->process(file_get_contents(__DIR__ . '/serialized_text_sample.txt'), 'sample.domain.com', 'new.example.com'), 'Could not do replacements');
    }

    public function test_serializedTextException() {
        $this->setExpectedException('Exception');
        $this->parser->unpack($this->jsonString, true);
    }

    public function test_simpleTextException() {
        $this->setExpectedException('Exception');
        $this->parser->unpack($this->simpleText, true);
    }

    public function test_cssTextException() {
        $this->setExpectedException('Exception');
        $this->parser->unpack($this->cssText, true);
    }

}
