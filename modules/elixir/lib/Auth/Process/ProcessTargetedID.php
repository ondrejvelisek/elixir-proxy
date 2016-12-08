<?php

/*
* Module checks whether UID attribute contains @ which means there is a scope. 
* If not then it gets UID, compute hash and construct new eduPersonPrincipalName 
* which consists of elixirEPTID_[hash]@[schacHomeOrganization]
*
* Author: Michal Prochazka <michalp@ics.muni.cz>
* Date: 21. 11. 2016
*/

class sspmod_elixir_Auth_Process_ProcessTargetedID extends SimpleSAML_Auth_ProcessingFilter
{
    private $eppnAttr;

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert('is_array($config)');

        if (!isset($config['eppnAttr'])) {
            throw new SimpleSAML_Error_Exception("elixir:ProcessTargetedID: missing mandatory configuration option 'eppnAttr'.");
        }
        $this->eppnAttr = (string) $config['eppnAttr'];
    }

    public function process(&$request)
    {
	assert('is_array($request)');

	$uid = $request['Attributes'][$this->eppnAttr][0];

	# Do not continue if we have user id with scope
	if (strpos($uid, '@') !== false) {
		return;
	}

	# Get scope from schacHomeOrganization
	# We are relying on previous module which fills the schacHomeOrganization
	$scope = $request['Attributes']['schacHomeOrganization'][0];

	if (empty($scope)) {
		throw new SimpleSAML_Error_Exception("elixir:ProcessTargetedID: missing mandatory attribute 'schacHomeOrganization'.");
	}

	# Generate hash from uid (eduPersonTargetedID)
	$hash = hash('sha256',$uid);

	# Construct new eppn
	$newEduPersonPrincipalName = 'elixirEPTID_' . $hash . '@' . $scope;
	
	$this->log("Converting eduPersonTargetedID '" . $uid . "' to the new ID '" . $newEduPersonPrincipalName . "'"); 

	# Set attributes back to the response
	# Set uid and also eduPersonPrincipalName, so all the modules and Perun will be happy
	$request['Attributes'][$this->eppnAttr] = array($newEduPersonPrincipalName);
	$request['Attributes']['eduPersonPrincipalName'] = array($newEduPersonPrincipalName);

    }

    protected function log($message)
    {
        SimpleSAML_Logger::info('Elixir.ProcessTargetedID: '.$message);    
    }
}
