<?php
namespace EasyDomainChange\Parsers;

class String implements \EasyDomainChange\Parser {
    
    protected $priority = 999;
    
    public function test($data) {
        return is_string($data);
    }
    
    public function unpack($data) {
        return $data;
    }
    
    public function pack($data) {
        return $data;
    }
    
    public function process($data, $oldDomain, $newDomain) {
        $data = $this->unpack($data);
        return $this->pack($this->replace($data, $oldDomain, $newDomain));
    }
    
    public function replace(&$data, $oldDomain, $newDomain){
        return str_replace($oldDomain, $newDomain, $data);
    }
    
    public function getPriority() {
        return $this->priority;
    }
    
}
