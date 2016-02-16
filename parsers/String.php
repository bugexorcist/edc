<?php
namespace EasyDomainChange\Parsers;

class String implements \EasyDomainChange\Parser {
    
    protected $priority = 999;
    
    public static function test($data) {
        
    }
    
    public static function unpack($data) {
        
    }
    
    public static function pack($data) {
        
    }
    
    public static function process($data){
        
    }
    
    public function getPriority() {
        return $this->priority;
    }
    
}
