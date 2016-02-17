<?php
namespace EasyDomainChange\Parsers;

class Serialized extends String implements \EasyDomainChange\Parser{
    
    protected $priority = 1;
    
    private $decodedData = null;
    
    public function test($data) {
        if(function_exists('is_serialized')) {
            return is_serialized($data);
        }
        return preg_match('/(a\:[0-9]{1,})?(i\:[0-9]{1,})?(s\:[0-9]{1,})?/', $data) && !empty($this->decodedData = unserialize($data));
    }
    
    public function unpack($data, $ignoreTested = false) {
        if(!$ignoreTested && $this->decodedData)
            return $this->decodedData;
        if(!$d = unserialize($data)) {
            throw new \Exception('Could not parse data');
        };
        return $d;
    }
    
    public function pack($data) {
        return serialize($data);
    }
    
    public function replace(&$data, $oldDomain, $newDomain){
        
        if(!empty($data)) {
            if(is_string($data)) {
                $data = parent::replace($data, $oldDomain, $newDomain);
            } elseif(is_object($data) || is_array($data)) {
                if (is_object($data)) {
                    $objectData = get_object_vars($data);
                } elseif(is_array($data)) {
                    $objectData = $data;
                }
                foreach($objectData as $field => $value) {
                    if (is_object($data)) {
                        $data->$field = $this->replace($data->$field, $oldDomain, $newDomain);
                    } elseif(is_array($data)) {
                        $data[$field] = $this->replace($data[$field], $oldDomain, $newDomain);
                    }
                }
            } elseif(is_integer($data)) {
                //Nothing
            } elseif(is_bool($data)) {
                //Nothing
            } else {
                ob_start();
                var_dump($data);
                throw new \Exception('Unsupported operand type: ' . ob_get_clean());
            }
        }
        
        return $data;
    }
}
