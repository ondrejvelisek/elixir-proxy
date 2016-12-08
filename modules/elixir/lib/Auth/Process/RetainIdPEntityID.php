<?php

class sspmod_elixir_Auth_Process_RetainIdPEntityID extends SimpleSAML_Auth_ProcessingFilter
{

    private $targetClass;
    private $reserved;
    private $defaultAttribute = 'https://login.elixir-czech.org/attr-name/sourceIdPEntityID';

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

	$this->reserved = (array) $reserved;    

	# Target attribute can be set in config, if not, the the default is used
	if (isset($config['attribute'])) {
		$this->defaultAttribute = $config['attribute'];
	}

    }


    public function process(&$request)
    {
        assert('is_array($request)');
	assert('array_key_exists("Attributes", $request)');

	if (!isset($request['Source']['entityid'])) {
		throw new SimpleSAML_Error_Exception("elixir:RetainIdPEntityID: Missing source entityID attribute in the reposnse from the IdP.");	
	}

	$attributes = $request['Attributes'];
	$attributes[$this->defaultAttribute] = array($request['Source']['entityid']);
	$request['Attributes'] = $attributes;
    }



    protected function log($message)
    {
        SimpleSAML_Logger::info('Elixir.RetainIdPEntityID: '.$message);
    }

}

