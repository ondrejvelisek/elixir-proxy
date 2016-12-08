<?php

class sspmod_elixir_Auth_Process_CoCoHash extends SimpleSAML_Auth_ProcessingFilter
{

    private $cocoAttr;
    private $salt;
    private $attrName;


    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert('is_array($config)');

        if (!isset($config['cocoAttr'])) {
            throw new SimpleSAML_Error_Exception("elixir:CoCoHash: missing mandatory configuration option 'cocoAttr'.");
        }
        if (!isset($config['attrName'])) {
            throw new SimpleSAML_Error_Exception("elixir:CoCoHash: missing mandatory configuration option 'attrName'.");
        }
        if (!isset($config['salt'])) {
            throw new SimpleSAML_Error_Exception("elixir:CoCoHash: missing mandatory configuration option 'salt'.");
        }

        $this->cocoAttr = (string) $config['cocoAttr'];
        $this->attrName = (string) $config['attrName'];
        $this->salt = (string) $config['salt'];    
    }


    public function process(&$request)
    {
        assert('is_array($request)');
        assert('array_key_exists("Attributes", $request)');
        assert('array_key_exists("SPMetadata", $request)');

	if (!($request['SPMetadata'][$this->cocoAttr])) {

	    foreach ($request['Attributes'][$this->attrName] as $key => $val) {
            	$hash = hash('sha256', $val . $this->salt);
            	$request['Attributes'][$this->attrName][$key] = "hashed_" . $hash;	
	    }

	}	
    }


    protected function log($message)
    {
        SimpleSAML_Logger::info('Elixir.CoCoFilter: '.$message);
    }


}

