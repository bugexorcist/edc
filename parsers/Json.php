<?php
namespace EasyDomainChange\Parsers;

class Json extends Serialized implements \EasyDomainChange\Parser {
    
    private $decodedData = null;
    
    protected $priority = 2;
    
    /**
     * Verifies if specified data is actually a JSON string
     * 
     * @param string $data
     * @return boolean
     */
    public function test($data) {
        if(preg_match('/[\"\{\:]/', $data)) {
            if(function_exists('json_decode')) {
                $this->decodedData = json_decode($data);
            }
        }
        return !empty($this->decodedData);
    }
    
    /**
     * Unpacks the data from JSON string
     * 
     * @param string $data
     * @param boolean $ignoreTested
     * @return mixed
     * @throws \Exception
     */
    public function unpack($data, $ignoreTested = false) {
        if(!$ignoreTested && $this->decodedData)
            return $this->decodedData;
        if(!$d = json_decode($data)) {
            throw new \Exception('Could not parse data: "'. json_last_error_msg() . '"');
        }
        return $d;
    }
    
    /**
     * Encodes the passed object data
     * 
     * @param mixed $data
     * @return string
     */
    public function pack($data) {
        return json_encode($data);
    }
}
    