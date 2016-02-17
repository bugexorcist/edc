<?php

include_once __DIR__ . '/BaseParserTest.php';

class JsonTest extends BaseParserTest {

    protected function setUp() {
        parent::setUp();
        $this->parser = new \EasyDomainChange\Parsers\Json();
    }

    public function test_Test() {
        $this->assertTrue($this->parser->test($this->jsonString), 'JSON not recognized');
        $this->assertTrue($this->parser->test($this->hugeJsonText), 'JSON not recognized');
        $this->assertFalse($this->parser->test($this->serializedString), 'Must not recognize serialized');
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

        $this->assertEquals($this->parser->unpack(json_encode($x)), $x, 'Unpacked object is corrupted');
        $this->assertNotEmpty($this->parser->unpack($this->hugeJsonText, true), 'Could not unpack the JSON');
        $this->assertNotEmpty($this->parser->unpack($this->jsonString, true), 'Could not unpack the JSON');
    }
    
    public function test_Replace() {
        $finalString = '{"array":[1,2,3],"boolean":true,"null":null,"number":123,"object":{"a":{"domain":"new.example.com","details":{"a":"new.example.com"}},"c":"d","e":"f"},"string":"Hello World"}';
        $this->assertEquals($finalString, json_encode($this->parser->replace(json_decode($this->jsonString), 'sample.domain.com', 'new.example.com')), 'Could not do replacements');
    }
    
    public function test_Process() {
        $finalString = '{"array":[1,2,3],"boolean":true,"null":null,"number":123,"object":{"a":{"domain":"new.example.com","details":{"a":"new.example.com"}},"c":"d","e":"f"},"string":"Hello World"}';
        $this->assertEquals($finalString, $this->parser->process($this->jsonString, 'sample.domain.com', 'new.example.com'), 'Could not do replacements');
    }

    public function test_serializedTextException() {
        $this->setExpectedException('Exception');
        $this->parser->unpack($this->serializedString, true);
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
