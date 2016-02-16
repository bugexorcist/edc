<?php
namespace EasyDomainChange\Parsers;

class Serialized extends String implements \EasyDomainChange\Parser{
    
    protected $priority = 1;
    
    public static function test($data) {
        
    }
    
    public static function unpack($data) {
        
    }
    
    public static function pack($data) {
        return $data;
    }
    
    public static function process($data){
        
    }
    
}
