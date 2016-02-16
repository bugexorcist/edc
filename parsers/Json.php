<?php
namespace EasyDomainChange\Parsers;

class Json extends String implements \EasyDomainChange\Parser {
    
    public static function test($data) {
        return !empty(json_decode($data));
    }
    
    public static function unpack($data) {
        
    }
    
    public static function pack($data) {
        
    }
    
    public static function process($data){
        
    }
    
}
