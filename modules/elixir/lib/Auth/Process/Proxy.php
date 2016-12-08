<?php

class sspmod_elixir_Auth_Process_Proxy extends SimpleSAML_Auth_ProcessingFilter
{

    private $targetClass;
    private $config;
    private $reserved;

    private $filterSP;

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert('is_array($config)');

        $this->targetClass = (string) $config['config']['class'];
	unset($config['config']['class']);
        $this->config = (array) $config['config'];
	$this->reserved = (array) $reserved;    
        $this->filterSP = (array) $config['filterSP'];

    }


    public function process(&$request)
    {
        assert('is_array($request)');

	foreach ($this->filterSP as $sp) {
		if ($sp == $request['SPMetadata']['entityid']) {
		     return;
		}
	}

	list($module, $simpleClass) = explode(":", $this->targetClass);
	$className = 'sspmod_'.$module.'_Auth_Process_'.$simpleClass;
	$authFilter = new $className($this->config, $this->reserved);	
   	$authFilter->process($request); 
    }



    protected function log($message)
    {
        SimpleSAML_Logger::info('Elixir.Proxy: '.$message);
    }

}

