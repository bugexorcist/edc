<?php
namespace EasyDomainChange\Parsers;

class String implements \EasyDomainChange\Parser {
    
    protected static $priority = 999;
    
    public static function test(\string $data) {
        
    }
    
    public static function unpack(\string $data) {
        
    }
    
    public static function pack($data) {
        
    }
    
    public static function process(\string $data){
        
    }
    
    public static function getPriority() {
        return self::$priority;
    }
    
}
