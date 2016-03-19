<?php

namespace mongoyii;

use mongoyii\Collection;
use mongoyii\Exception;

class Database
{
    public $client;
    public $database;

    public function __debugInfo()
    {
        return $this->database->__deugInfo();
    }

    public function __get($collectionName)
    {
        return $this->database->__get($collectionName);
    }
    
    public function __toString()
    {
        return $this->database->__toString();
    }

	public function __call($name, $parameters = [])
	{
	    if(method_exists($this->collection, $name)){
	        return call_user_func_array(array($this->collection, $name), $parameters);
	    }
	    throw new Exception("$name is not a callable function");
	}
	
	public function __construct($database, $client)
	{
	    $this->database = $database;
	    $this->client = $client;
	}
	
	public function selectCollection($collectionName, array $options = [])
	{
        return new Collection(
	        $this->database->selectCollection($collectionName, $options),
	        $this->client
        );
	}
}