<?php
/**
 * SAML 2.0 IdP configuration for SimpleSAMLphp.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-hosted
 */

require '/etc/elixir-proxy/saml20-idp-hosted.php';

// SPs we serve original attributes from idps
$rawAttributesSps = array(
	'https://perun.elixir-czech.cz/shibboleth', // should be removed in future
	'https://perun.elixir-czech.cz/shibboleth/google', 
	'https://perun.elixir-czech.cz/shibboleth/raw-attributes', 
	'https://perun.elixir-czech.cz/shibboleth/raw-attributes-all-idps',
	'https://perun.elixir-czech.cz/shibboleth/sp-registrar',
	'https://perun.elixir-czech.cz/shibboleth/sp-consolidator',
	'https://perun.elixir-czech.cz/shibboleth/sp-conformance',
);

$metadata[ENTITY_ID] = array(
	/*
	 * The hostname of the server (VHOST) that will use this SAML entity.
	 *
	 * Can be '__DEFAULT__', to use this entry by default.
	 */
	'host' => '__DEFAULT__',

	// X.509 key and certificate. Relative to the cert directory.
	'privatekey' => 'saml.key',
	'certificate' => 'saml.pem',

	/*
	 * Authentication source to use. Must be one that is configured in
	 * 'config/authsources.php'.
	 */
	'auth' => 'default-sp',
	
	// Name of unique attribute of user. Used by consent module.
	// DEPRECATED
	'userid.attribute' => 'uid',

	/*
	 * WARNING: SHA-1 is disallowed starting January the 1st, 2014.
	 *
	 * Uncomment the following option to start using SHA-256 for your signatures.
	 * Currently, SimpleSAMLphp defaults to SHA-1, which has been deprecated since
	 * 2011, and will be disallowed by NIST as of 2014. Please refer to the following
	 * document for more information:
	 * 
	 * http://csrc.nist.gov/publications/nistpubs/800-131A/sp800-131A.pdf
	 *
	 * If you are uncertain about service providers supporting SHA-256 or other
	 * algorithms of the SHA-2 family, you can configure it individually in the
	 * SP-remote metadata set for those that support it. Once you are certain that
	 * all your configured SPs support SHA-2, you can safely remove the configuration
	 * options in the SP-remote metadata set and uncomment the following option.
	 *
	 * Please refer to the IdP hosted reference for more information.
	 */
	'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

	/* 
	 * Signing logout redirect request and response. Default is false. 
	 * Added because of cesnet's Perun SP, especially consolidator. 
	*/	
	'redirect.sign' => true,

	'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',

	/* Uncomment the following to use the uri NameFormat on attributes. */
	'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri',
	'authproc' => array(
		/* done in SP config
                10 => array('class' => 'core:AttributeMap', 'oid2name'),
		# Extract NameID attribute
		19 => array(
		    'class' => 'saml:NameIDAttribute',
		),
       	       	20 => array(
			'class' => 'smartattributes:SmartID',
			'candidates' => array('eduPersonUniqueId', 'eduPersonPrincipalName', 'eduPersonTargetedID', 'nameid'),
			'id_attribute' => 'uid',
			'add_authority' => FALSE,
			'add_candidate' => FALSE,
		),
		*/
		20 => array(
                        'class' => 'elixir:Proxy',
                        'filterSP' => array('https://perun.elixir-czech.cz/shibboleth/sp-conformance'),
                        'config' => array(
                                'class' => 'elixir:ProcessTargetedID',
                                'eppnAttr' => 'uid',
			),
                ),
		24 => array(
                        'class' => 'elixir:Proxy',
                        'filterSP' => $rawAttributesSps,
                        'config' => array(
                                'class' => 'elixir:UnknownUser',
                                'eppnAttr' => 'uid',
                        	'redirect' => 'https://www.elixir-europe.org/register/',
			),
                ),
		25 => array(
                        'class' => 'elixir:Proxy',
                        'filterSP' => $rawAttributesSps,
                        'config' => array(
				'class' => 'elixir:FetchFromLdap',
        	                'eppnAttr' => 'uid',
				'attrMap' => array(
					'cn' => 'displayName',
                	        	'preferredMail' => 'mail',
					'login;x-ns-elixir' => 'eduPersonPrincipalName',
	                        	'login;x-ns-elixir-persistent-shadow' => 'eduPersonUniqueId',
				),
			),
                ),
		26 => array(
                        'class' => 'elixir:Proxy',
                        'filterSP' => $rawAttributesSps,
                        'config' => array(
				'class' => 'elixir:FetchGroups',
				'eppnAttr' => 'uid',
				'attrName' => 'eduPersonEntitlement',	        
                	),
		),
		30 => array(
                        'class' => 'elixir:Proxy',
                        'filterSP' => $rawAttributesSps,
                        'config' => array(
				'class' => 'core:AttributeAdd', '%replace', 
        	                'schacHomeOrganization' => array(),
                	),
		),
		35 => array(
                        'class' => 'elixir:Proxy',
                        'filterSP' => $rawAttributesSps,
                        'config' => array(
	                    	'class' => 'core:PHP',
    				'code'  => '$attributes["eduPersonPrincipalName"][0] .= "@elixir-europe.org";',
			),
		),
		// Temporary commented out, nameID attribute cannot be XML, see https://simplesamlphp.org/docs/1.5/simplesamlphp-authproc#section_2_5_1
	/*	36 => array(
                        'class' => 'elixir:Proxy',
                        'filterSP' => $rawAttributesSps,
                        'config' => array(
				'class' => 'core:TargetedID',
                        	'attributename' => 'eduPersonPrincipalName',
                        	'nameId' => false,
                        ),
		),
*/
	
		37 => array(
                        'class' => 'elixir:Proxy',
                        'filterSP' => $rawAttributesSps,
			'config' => array(
				'class' => 'elixir:ForceAup',
				'perunForceAttr' => 'urn:perun:user:attribute-def:def:forceAup',
                                'perunAupAttr' => 'urn:perun:user:attribute-def:def:aup',
				'eppnAttr' => 'uid',
				'aupUrl' => 'https://www.elixir-europe.org/services/compute/aai/aup',
			),
		),

		// 60 - 69 reserved for manipulation per SP
		90 => array(
                        'class' => 'elixir:Filter',
                        'filterAttr' => 'CoCo',
                        'filterOut' => array(
				'schacHomeOrganization',
				'eduPersonScopedAffiliation'
			),
                ),
		
		91 => array(
                        'class' => 'core:AttributeLimit',
			'default' => TRUE,
                        'eduPersonPrincipalName',
			'eduPersonUniqueId',
                        'displayName',
                        'mail',
			'schacHomeOrganization',
			'eduPersonScopedAffiliation',
			'eduPersonEntitlement',
			'eduPersonTargetedID',
		),
                95 => array(
                        'class' => 'consent:Consent',
                        'store' => 'consent:Cookie',
                        'focus' => 'yes',
                        'checked' => TRUE
                ),
                99 => array('class' => 'core:AttributeMap', 'eduPersonUniqueId' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.13'),
                100 => array('class' => 'core:AttributeMap', 'name2oid'),	
	),

	'attributeencodings' => array( 
		'urn:oid:1.3.6.1.4.1.5923.1.1.1.10' => 'raw', 
		'eduPersonTargetedID' => 'raw', 
	), 

	'name' => array(
		'en' => NAME,
	),

	'OrganizationName' => array(
		'en' => 'ELIXIR',
	),

	'OrganizationDisplayName' => array(
		'en' => 'ELIXIR',
	),
	
	'OrganizationURL' => array(
		'en' => 'https://www.elixir-europe.org',
	),

	'privacypolicy' => 'http://www.elixir-europe.org/services/compute/aai',

	// SCOPE
	'scope' => array(
		'elixir-europe.org',
	),

	// MDUI element 
	'UIInfo' => array(
		'DisplayName' => array(
		    'en' => DISPLAY_NAME,
		),
		'Description' => array(
		    'en' => DESCRIPTION,
		),
		'InformationURL' => array(
		    'en' => 'https://www.elixir-europe.org',
		),
		'PrivacyStatementURL' => array(
		    'en' => 'http://www.elixir-europe.org/services/compute/aai',
		),
		'Keywords' => array(
		    'en' => array('ELIXIR','proxy','biology','life','sciences'),
		),
		'Logo' => array(
		    array(
			'url'    => 'https://login.elixir-czech.org/media/elixir-96x96.jpg',
			'height' => 96,
			'width'  => 96,
		    ),
		),
	    ),

	/*
	 * Uncomment the following to specify the registration information in the
	 * exported metadata. Refer to:
     * http://docs.oasis-open.org/security/saml/Post2.0/saml-metadata-rpi/v1.0/cs01/saml-metadata-rpi-v1.0-cs01.html
	 * for more information.
	 */
	/*
	'RegistrationInfo' => array(
		'authority' => 'urn:mace:example.org',
		'instant' => '2008-01-17T11:28:03Z',
		'policies' => array(
			'en' => 'http://example.org/policy',
			'es' => 'http://example.org/politica',
		),
	),
	*/
);

