<?php

class MainTest extends PHPUnit_Framework_TestCase {

    private $main;

    protected function setUp() {
        require_once dirname(__DIR__) . '/easy_domain_change.php';
        $this->main = new \EasyDomainChange\Main();
    }

    public function test_GetParsers() {
        $parsers = $this->main->getParsers();
        $this->assertEquals(3, count($parsers), 'Incorrect number of parsers');
        foreach($parsers as $p) {
            $this->assertTrue(is_a($p, 'EasyDomainChange\Parser'), 'Invalid parser object');
        }
    }

}
