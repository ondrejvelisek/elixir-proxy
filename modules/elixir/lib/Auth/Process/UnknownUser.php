<?php

class sspmod_elixir_Auth_Process_UnknownUser extends SimpleSAML_Auth_ProcessingFilter
{
    private $redirect;
    private $eppnAttr;

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert('is_array($config)');

        if (!isset($config['eppnAttr'])) {
            throw new SimpleSAML_Error_Exception("elixir:UnknownUser: missing mandatory configuration option 'eppnAttr'.");
        }
        if (!isset($config['redirect'])) {
            throw new SimpleSAML_Error_Exception("elixir:UnknownUser: missing mandatory configuration option 'redirect'.");
        }
        $this->eppnAttr = (string) $config['eppnAttr'];
        $this->redirect = (string) $config['redirect'];
    }

    public function process(&$request)
    {
	assert('is_array($request)');

	$conn = $this->getLDAPConnection();

        $result = ldap_search($conn, "ou=People,dc=perun,dc=cesnet,dc=cz", 
		"eduPersonPrincipalNames=".$request['Attributes'][$this->eppnAttr][0],
            	array('perunUserId'));

        $entries = ldap_get_entries($conn, $result);

        if ($entries["count"] == 0) {
                $this->log('Unknown user ' . $request['Attributes'][$this->eppnAttr][0] . '. Redirecting to registration page.');
                header('Location: ' . $this->redirect);
                exit;
        }

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


    protected function log($message)
    {
        SimpleSAML_Logger::info('Elixir.UnknownUser: '.$message);    
    }


}
