<?php

class sspmod_elixir_Auth_Process_ForceAup extends SimpleSAML_Auth_ProcessingFilter
{

	private $eppnAttr;
	private $perunForceAttr;
	private $perunAupAttr;	
	private $aupUrl;

	public function __construct($config, $reserved)
	{
		parent::__construct($config, $reserved);
	 
	       	if (!isset($config['eppnAttr'])) {
                        throw new SimpleSAML_Error_Exception("elixir:ForceAup: missing mandatory configuration option 'eppnAttr'.");
                }
                if (!isset($config['perunForceAttr'])) {
                        throw new SimpleSAML_Error_Exception("elixir:ForceAup: missing mandatory configuration option 'perunForceAttr'.");
                }
                if (!isset($config['perunAupAttr'])) {
                        throw new SimpleSAML_Error_Exception("elixir:ForceAup: missing mandatory configuration option 'perunAupAttr'.");
                }
                if (!isset($config['aupUrl'])) {
                        throw new SimpleSAML_Error_Exception("elixir:ForceAup: missing mandatory configuration option 'aupUrl'.");
                }
                $this->eppnAttr       = (string) $config['eppnAttr'];
                $this->perunForceAttr = (string) $config['perunForceAttr'];
                $this->perunAupAttr   = (string) $config['perunAupAttr'];
                $this->aupUrl         = (string) $config['aupUrl'];     
	}	
	public function process(&$request)
	{
		assert('is_array($request)');

		$this->log('Accessing filter');

		$user = sspmod_elixir_Perun::get('usersManager', 'getUserByExtSourceNameAndExtLogin', array(
			'extSourceName' => $request['IdPMetadata']['entityid'],
			'extLogin' => $request['Attributes'][$this->eppnAttr][0],
		));

		$this->log(var_export($user, true));

		$forceAup = sspmod_elixir_Perun::get('attributesManager', 'getAttribute', array(
			'user' => $user['id'],
			'attributeName' => $this->perunForceAttr,
		));

		if (!empty($forceAup['value'])) {
			$request['eppnAttr']  = $this->eppnAttr;
			$request['perunForceAttr'] = $this->perunForceAttr;
			$request['perunAupAttr'] = $this->perunAupAttr;
			$request['aupUrl'] = $this->aupUrl;
			$id  = SimpleSAML_Auth_State::saveState($request, 'elixir:forceAup');
			$url = SimpleSAML_Module::getModuleURL('elixir/aupPage.php');
			\SimpleSAML\Utils\HTTP::redirectTrustedURL($url, array('StateId' => $id));			
		}

	}

	protected function log($message)
	{
		SimpleSAML_Logger::info('Elixir.ForceAup: '.$message);
	}


}


