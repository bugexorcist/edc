<?php
namespace EasyDomainChange\Parsers;

class Serialized extends String implements \EasyDomainChange\Parser{
    
    protected static $priority = 1;
    
    public static function test(\string $data) {
        
    }
    
    public static function unpack(\string $data) {
        
    }
    
    public static function pack($data) {
        return $data;
    }
    
    public static function process(\string $data){
        
    }
    
}
