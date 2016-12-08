<?php

class sspmod_elixir_Auth_Process_Filter extends SimpleSAML_Auth_ProcessingFilter
{

    private $filterAttr;

    private $filterOut;


    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert('is_array($config)');

        if (!isset($config['filterAttr'])) {
            throw new SimpleSAML_Error_Exception("elixir:Filter: missing mandatory configuration option 'filterAttr'.");
        }
        $this->filterAttr = (string) $config['filterAttr'];
        $this->filterOut  =  (array) $config['filterOut'];
    }


    public function process(&$request)
    {
        assert('is_array($request)');
        assert('array_key_exists("Attributes", $request)');
        assert('array_key_exists("SPMetadata", $request)');

	# If attributes is not defined in SP metadata at all or if it is set to false, then do the filtering
	if (!array_key_exists($this->filterAttr, $request['SPMetadata']) || !$request['SPMetadata'][$this->filterAttr]) {
            foreach ($this->filterOut as $attrName) {
		unset($request['Attributes'][$attrName]);
	    }
	}
	
    }



    protected function log($message)
    {
        SimpleSAML_Logger::info('Elixir.Filter: '.$message);
    }


}

