<?php


class sspmod_elixir_Auth_Process_FetchGroups extends SimpleSAML_Auth_ProcessingFilter
{
    private $eppnAttr;
    private $attrName;

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        assert('is_array($config)');

        if (!isset($config['eppnAttr'])) {
            throw new SimpleSAML_Error_Exception("elixir:FetchGroups: missing mandatory configuration option 'eppnAttr'.");
        }
        if (!isset($config['attrName'])) {
            throw new SimpleSAML_Error_Exception("elixir:FetchGroups: missing mandatory configuration option 'attrName'.");
        }
        $this->eppnAttr = (string) $config['eppnAttr'];
        $this->attrName = (string) $config['attrName'];
    }



    public function process(&$request)
    {
        assert('is_array($request)');
        assert('array_key_exists("Attributes", $request)');

	$conn = $this->getLDAPConnection();

	$attributes = $request['Attributes'];
	$attributes[$this->attrName] = array();
	
	if (!empty($attributes[$this->eppnAttr][0])) {
		$entityId = $request["Destination"]["entityid"];
                $resources = $this->getResourcesByEntityId($conn, $entityId);
		if (!empty($resources) && is_array($resources)) {
			foreach ($resources as $resource) {
				$groups = $this->getGroupsForResource($conn, $attributes[$this->eppnAttr][0], $resource, $request);
				$this->log("Groups :" . print_r($groups, true));

				$attributes[$this->attrName] = array_unique(array_merge($attributes[$this->attrName], $groups));
			}
		}

		$request['Attributes'] = $attributes;
	}


	ldap_close($conn);	
	
    }




    function getLDAPConnection() {
        $conn = ldap_connect("ldaps://elixir.ics.muni.cz");
        if (!$conn) {
            $amFout = new EngineBlock_Attributes_Manipulator_CustomException("AM_ERROR unable to connect to the backend", EngineBlock_Attributes_Manipulator_CustomException::CODE_NOTICE);
            $amFout->setFeedbackTitle(array("en" => "Not Allowed"));
            $amFout->setFeedbackDescription(array("en" => "You are not allowed to use this service due to an internal error."));
            throw $amFout;
        }

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (ldap_bind($conn) === FALSE) {
            $amFout = new EngineBlock_Attributes_Manipulator_CustomException("AM_ERROR unable to connect to the backend", EngineBlock_Attributes_Manipulator_CustomException::CODE_NOTICE);
            $amFout->setFeedbackTitle(array("en" => "Not Allowed"));
            $amFout->setFeedbackDescription(array("en" => "You are not allowed to use this service due to an internal error."));
            throw $amFout;
        }

        return $conn;
    }




    function getPerunUserId ($conn, $eppn) {
        
	$result = ldap_search($conn,
                "ou=People,dc=perun,dc=cesnet,dc=cz",
                "eduPersonPrincipalNames=".$eppn,
                array("perunUserId")
        );

        $entries = ldap_get_entries($conn, $result);

        if ($entries["count"] == 0) {
                echo "No entry found";
                return;
        }

        if ($entries["count"] != 1) {
                echo "More entries found";
                return;
        }

        $perunUserId = $entries[0]["perunuserid"];
        if ($perunUserId["count"] == 1) {
                return $perunUserId[0];
        } else {
                return;
        }
    }




    function getGroupsForResource($conn, $eppn, $resourceId, $request) {
        $perunUserId = $this->getPerunUserId($conn, $eppn);

        $result = ldap_search($conn,
                "dc=perun,dc=cesnet,dc=cz",
                "(&(uniquemember=perunUserId=" . $perunUserId . ",ou=people,dc=perun,dc=cesnet,dc=cz)(assignedToResourceId=$resourceId))",
                array("perunUniqueGroupName")
        );

        $entries = ldap_get_entries($conn, $result);

        if ($entries["count"] == 0) {
                return;
        }

        $groups = array();
        for($i=0; $i<$entries["count"]; $i++) {
                if (isset($entries[$i]["perununiquegroupname"][0])) {
                         array_push($groups, $this->mapGroupNameToEntitlement($request, $entries[$i]["perununiquegroupname"][0]));
                }
        }
        return $groups;
    }

/*
 * If the SP metadata contains attribute 'entittlementID' that means the SP
 * needs to receive standardized eduPersonEntitlement.
 * Entitlement syntax: urn:mace:[entitlementID]:elixir-europe.org:[role]@vo.elixir-europe.org
 */

    function mapGroupNameToEntitlement($request, $groupName) {
	if (isset($request["SPMetadata"]["groupMapping"]) && isset($request["SPMetadata"]["groupMapping"][$groupName])) {
		$this->log("Mapping $groupName to " . $request["SPMetadata"]["groupMapping"][$groupName]);
		return $request["SPMetadata"]["groupMapping"][$groupName];
	} else {
		# No mapping defined
		$this->log("No mapping found.");
		return $groupName;
	}
    }


    function getResourcesByEntityId($conn, $entityId) {
        
	$result = ldap_search($conn,
                "dc=perun,dc=cesnet,dc=cz",
                "(entityID=$entityId)",
                array("perunResourceId")
        );

        $entries = ldap_get_entries($conn, $result);

        if ($entries["count"] == 0) {
                return;
        }

        $resources = array();
        for($i=0; $i<$entries["count"]; $i++) {
                if (isset($entries[$i]["perunresourceid"][0])) {
                         array_push($resources, $entries[$i]["perunresourceid"][0]);
                }
        }

        return $resources;
    }




    protected function log($message)
    {
        SimpleSAML_Logger::info('Elixir.FetchGroups: '.$message);
    }



}
