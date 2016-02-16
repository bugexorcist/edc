<?php

include __DIR__ . '/BaseParserTest.php';

class JsonTest extends BaseParserTest {

    protected function setUp() {
        parent::setUp();
        $this->parser = new \EasyDomainChange\Parsers\Json();
    }

    public function test_Test() {
        $this->assertTrue($this->parser->test($this->jsonString), 'JSON not recognized');
        $this->assertFalse($this->parser->test($this->serializedString), 'Must not recognize serialized');
        $this->assertFalse($this->parser->test($this->simpleText), 'Must not recognize serialized');
        $this->assertFalse($this->parser->test($this->cssText), 'Must not recognize serialized');
    }

}
