<?php

class sspmod_elixir_Auth_Process_FetchFromLdap extends SimpleSAML_Auth_ProcessingFilter
{
    private $eppnAttr;
    private $attrMap;

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert('is_array($config)');

        if (!isset($config['eppnAttr'])) {
            throw new SimpleSAML_Error_Exception("elixir:FetchFromLdap: missing mandatory configuration option 'eppnAttr'.");
        }
        if (!isset($config['attrMap'])) {
            throw new SimpleSAML_Error_Exception("elixir:FetchFromLdap: missing mandatory configuration option 'attrMap'.");
        }
        $this->eppnAttr = (string) $config['eppnAttr'];
        $this->attrMap = (array) $config['attrMap'];
    }

    public function process(&$request)
    {
	assert('is_array($request)');
        assert('array_key_exists("Attributes", $request)');

	$conn = $this->getLDAPConnection();

	$attributes = $request['Attributes'];

	foreach ($this->attrMap as $ldapAttr => $sspAttr) {
		$val = $this->findAttr($conn, $attributes[$this->eppnAttr][0], $ldapAttr);
		$attributes[$sspAttr] = array($val);
	}

	$request['Attributes'] = $attributes;

	ldap_close($conn);	
    }

    function getLDAPConnection() {
        $conn = ldap_connect("ldaps://elixir.ics.muni.cz");
        if (!$conn) {
	    throw new SimpleSAML_Error_Exception('Unable to connect to the backend.');
        }

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (ldap_bind($conn) === FALSE) {
	    throw new SimpleSAML_Error_Exception('Unable to connect to the backend.');
        }

        return $conn;
    }

    function findAttr($conn, $ldapEppn, $ldapAttrName) {

        $result = ldap_search($conn, "ou=People,dc=perun,dc=cesnet,dc=cz", "eduPersonPrincipalNames=".$ldapEppn,
            array($ldapAttrName));

        $entries = ldap_get_entries($conn, $result);

	if ($entries["count"] == 0) {
                throw new SimpleSAML_Error_Exception('ERROR: No user with your identity was found in ELIXIR AAI.');
	}

	if ($entries["count"] > 1) {
		throw new SimpleSAML_Error_Exception('FATAL: More users with your identity was found in ELIXIR AAI.');
        }

        $attr = $entries[0][strtolower($ldapAttrName)][0];

        return $attr;
    }


    protected function log($message)
    {
        SimpleSAML_Logger::info('Elixir.FetchFromLdap: '.$message);    
    }


}
