<?php

require '/etc/elixir-proxy/authsources.php';

$config = array(

    // This is a authentication source which handles admin authentication.
    'admin' => array(
        // The default is to use core:AdminPassword, but it can be replaced with
        // any authentication source.

        'core:AdminPassword',
    ),
    
    // An authentication source which can authenticate against both SAML 2.0
    // and Shibboleth 1.3 IdPs.
    'default-sp' => array(
        'saml:SP',

        // The entity ID of this SP.
        // Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
        'entityID' => ENTITY_ID,

        // The entity ID of the IdP this should SP should contact.
        // Can be NULL/unset, in which case the user will be shown a list of available IdPs.
        'idp' => null,

        // The URL to the discovery service.
        // Can be NULL/unset, in which case a builtin discovery service will be used.
        'discoURL' => '/proxy/module.php/discopower/disco.php',

	'privatekey' => 'saml.key',
    	'certificate' => 'saml.pem',

	'AssertionConsumerService' => array(
		ASSERTION_CONSUMER_SERVICE,
	),

	'name' => array(
    		'en' => NAME,
	),

	'description' => array(
    		'en' => DESCRIPTION,
	),

	'OrganizationName' => array(
    		'en' => 'ELIXIR CZ',
	),
	'OrganizationDisplayName' => array(
    		'en' => 'ELIXIR CZ',
	),
	'OrganizationURL' => array(
    		'en' => 'http://www.elixir-czech.cz',
	),

	'UIInfo' => array(
            'DisplayName' => array(
                'en' => NAME,
            ),
            'Description' => array(
                'en' => DESCRIPTION,
	    ),
	    'InformationURL'  => array(
                'en' => 'http://www.elixir-europe.org/services/compute/aai',
            ),
	   'PrivacyStatementURL'  => array(
                'en' => 'http://www.elixir-europe.org/services/compute/aai',
            ),
	   'Keywords'  => array(
                'en' => array('ELIXIR','proxy','biology','life','sciences'),
            ),
	   'Logo'  => array(
		array(
		    'url'    => 'https://login.elixir-czech.org/media/elixir-ds.jpg',
		    'height' => 96,
		    'width'  => 96,
		),
	    ),

        ),

	'EntityAttributes' => array(
		'http://macedir.org/entity-category' => array (
			'http://refeds.org/category/research-and-scholarship',
		),
	),

	'authproc' => array(
		
		10 => array(
			'class' => 'core:AttributeMap', 'oid2name'
		),
		# If eduPersonScopedAffiliation missing then compute it from eduPersonPrincipalName and eduPersonAffiliation
		11 => array(
		    'class' => 'core:ScopeAttribute',
		    'scopeAttribute' => 'eduPersonPrincipalName',
		    'sourceAttribute' => 'eduPersonAffiliation',
		    'targetAttribute' => 'eduPersonScopedAffiliation',
		),
		# Generate schacHomeOrganization from eduPersonPrincipal name when it is not delivered by the IdP
		12 => array(
		    'class' => 'core:ScopeFromAttribute',
		    'sourceAttribute' => 'eduPersonPrincipalName',
		    'targetAttribute' => 'schacHomeOrganization',
		),
		# Generate schacHomeOrganization from eduPersonScopedAffiliation name when it is not delivered by the IdP
		13 => array(
		    'class' => 'core:ScopeFromAttribute',
		    'sourceAttribute' => 'eduPersonScopedAffiliation',
		    'targetAttribute' => 'schacHomeOrganization',
		),	
		# Extract NameID attribute
                20 => array(
                    	'class' => 'saml:NameIDAttribute',
                ),
                30 => array(
                        'class' => 'smartattributes:SmartID',
                        // FIXME: ePUID is never used because oid name of attr is not mapped
			'candidates' => array('eduPersonUniqueId', 'eduPersonPrincipalName', 'eduPersonTargetedID', 'nameid'),
                        'id_attribute' => 'uid',
                        'add_authority' => FALSE,
                        'add_candidate' => FALSE,
                ),
		40 => array(
			'class' => 'elixir:RetainIdPEntityID',
		),
	),

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
         * If you are uncertain about identity providers supporting SHA-256 or other
         * algorithms of the SHA-2 family, you can configure it individually in the
         * IdP-remote metadata set for those that support it. Once you are certain that
         * all your configured IdPs support SHA-2, you can safely remove the configuration
         * options in the IdP-remote metadata set and uncomment the following option.
         *
         * Please refer to the hosted SP configuration reference for more information.
          */
        'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

	'contacts' => array(
	    array(
		'contactType'       => 'support',
		'emailAddress'      => 'mailto:aai-contact@elixir-europe.org',
		'givenName'         => 'ELIXIR',
		'surName'           => 'AAI',
	    ),
	    array(
		'contactType'       => 'administrative',
		'emailAddress'      => 'mailto:aai-contact@elixir-europe.org',
		'givenName'         => 'ELIXIR',
		'surName'           => 'AAI',
	    )
	),

        /*
         * The attributes parameter must contain an array of desired attributes by the SP.
         * The attributes can be expressed as an array of names or as an associative array
         * in the form of 'friendlyName' => 'name'.
         * The metadata will then be created as follows:
         * <md:RequestedAttribute FriendlyName="friendlyName" Name="name" />
         */
	'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri',
        'attributes' => array(
            'eduPersonPrincipalName' 	 => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
            'eduPersonTargetedID' 	 => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.10',
            'eduPersonScopedAffiliation' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.9',
            'eduPersonAffiliation' 	 => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.1',
            'schacHomeOrganization' 	 => 'urn:oid:1.3.6.1.4.1.25178.1.2.9',
        ),
        'attributes.required' => array (
            'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
            'urn:oid:1.3.6.1.4.1.5923.1.1.1.10',
            'urn:oid:1.3.6.1.4.1.5923.1.1.1.9',
            'urn:oid:1.3.6.1.4.1.5923.1.1.1.1',
            'urn:oid:1.3.6.1.4.1.25178.1.2.9',
        ),
    ),


    /*
    'example-sql' => array(
        'sqlauth:SQL',
        'dsn' => 'pgsql:host=sql.example.org;port=5432;dbname=simplesaml',
        'username' => 'simplesaml',
        'password' => 'secretpassword',
        'query' => 'SELECT uid, givenName, email, eduPersonPrincipalName FROM users WHERE uid = :username AND password = SHA2(CONCAT((SELECT salt FROM users WHERE uid = :username), :password),256);',
    ),
    */

    /*
    'example-static' => array(
        'exampleauth:Static',
        'uid' => array('testuser'),
        'eduPersonAffiliation' => array('member', 'employee'),
        'cn' => array('Test User'),
    ),
    */

    /*
    'example-userpass' => array(
        'exampleauth:UserPass',

        // Give the user an option to save their username for future login attempts
        // And when enabled, what should the default be, to save the username or not
        //'remember.username.enabled' => FALSE,
        //'remember.username.checked' => FALSE,

        'student:studentpass' => array(
            'uid' => array('test'),
            'eduPersonAffiliation' => array('member', 'student'),
        ),
        'employee:employeepass' => array(
            'uid' => array('employee'),
            'eduPersonAffiliation' => array('member', 'employee'),
        ),
    ),
    */

    /*
    'crypto-hash' => array(
        'authcrypt:Hash',
        // hashed version of 'verysecret', made with bin/pwgen.php
        'professor:{SSHA256}P6FDTEEIY2EnER9a6P2GwHhI5JDrwBgjQ913oVQjBngmCtrNBUMowA==' => array(
            'uid' => array('prof_a'),
            'eduPersonAffiliation' => array('member', 'employee', 'board'),
        ),
    ),
    */

    /*
    'htpasswd' => array(
        'authcrypt:Htpasswd',
        'htpasswd_file' => '/var/www/foo.edu/legacy_app/.htpasswd',
        'static_attributes' => array(
            'eduPersonAffiliation' => array('member', 'employee'),
            'Organization' => array('University of Foo'),
        ),
    ),
    */

    /*
    // This authentication source serves as an example of integration with an
    // external authentication engine. Take a look at the comment in the beginning
    // of modules/exampleauth/lib/Auth/Source/External.php for a description of
    // how to adjust it to your own site.
    'example-external' => array(
        'exampleauth:External',
    ),
    */

    /*
    'yubikey' => array(
        'authYubiKey:YubiKey',
         'id' => '000',
        // 'key' => '012345678',
    ),
    */

    /*
    'openid' => array(
        'openid:OpenIDConsumer',
        'attributes.required' => array('nickname'),
        'attributes.optional' => array('fullname', 'email',),
        // 'sreg.validate' => FALSE,
        'attributes.ax_required' => array('http://axschema.org/namePerson/friendly'),
        'attributes.ax_optional' => array('http://axschema.org/namePerson','http://axschema.org/contact/email'),
        // Prefer HTTP redirect over POST
        // 'prefer_http_redirect' => FALSE,
    ),
    */

    /*
    // Example of an authsource that authenticates against Google.
    // See: http://code.google.com/apis/accounts/docs/OpenID.html
    'google' => array(
        'openid:OpenIDConsumer',
        // Googles OpenID endpoint.
        'target' => 'https://www.google.com/accounts/o8/id',
        // Custom realm
        // 'realm' => 'http://*.example.org',
        // Attributes that google can supply.
        'attributes.ax_required' => array(
            //'http://axschema.org/namePerson/first',
            //'http://axschema.org/namePerson/last',
            //'http://axschema.org/contact/email',
            //'http://axschema.org/contact/country/home',
            //'http://axschema.org/pref/language',
        ),
        // custom extension arguments
        'extension.args' => array(
            //'http://specs.openid.net/extensions/ui/1.0' => array(
            //	'mode' => 'popup',
            //	'icon' => 'true',
            //),
        ),
    ),
    */

    /*
    'papi' => array(
        'authpapi:PAPI',
    ),
    */


    /*
    'facebook' => array(
        'authfacebook:Facebook',
        // Register your Facebook application on http://www.facebook.com/developers
        // App ID or API key (requests with App ID should be faster; https://github.com/facebook/php-sdk/issues/214)
        'api_key' => 'xxxxxxxxxxxxxxxx',
        // App Secret
        'secret' => 'xxxxxxxxxxxxxxxx',
        // which additional data permissions to request from user
        // see http://developers.facebook.com/docs/authentication/permissions/ for the full list
        // 'req_perms' => 'email,user_birthday',
    ),
    */

/*    
    // LinkedIn OAuth Authentication API.
    // Register your application to get an API key here:
    //  https://www.linkedin.com/secure/developer
    'linkedin' => array(
        'authlinkedin:LinkedIn',
        'key' => '7704ap765tq6ta',
        'secret' => '5kQq2l66i3bE3fZz',
    ),

    // ORCID OAuth Authentication API
    'orcid' => array(
        'authorcid:ORCID',
        'clientId' => 'APP-54NFNJM6EHYW3WKB',
        'clientSecret' => '75e57b48-aed4-4e95-a762-edc979ceafd7',
    ),
    
    // Google OAuth2 Authentication API
    'authgoogleoauth2' => array(
        'authgoogleoauth2:authgoogleoauth2',
        'client_id' => '989734064081-lllvft1eb3178l77ha0rft4d0d7jnri9.apps.googleusercontent.com',
        'client_secret' => 'eCeFzcJRE97IdVXAdFfPcLK3',
        'developer_key' => 'AIzaSyBPrlvinxsOcuk59f7yI5ooNbxqqPSQoi4',
    ),
*/
    /*
    // Twitter OAuth Authentication API.
    // Register your application to get an API key here:
    //  http://twitter.com/oauth_clients
    'twitter' => array(
        'authtwitter:Twitter',
        'key' => 'xxxxxxxxxxxxxxxx',
        'secret' => 'xxxxxxxxxxxxxxxx',

        // Forces the user to enter their credentials to ensure the correct users account is authorized.
        // Details: https://dev.twitter.com/docs/api/1/get/oauth/authenticate
        'force_login' => FALSE,
    ),
    */

    /*
    // MySpace OAuth Authentication API.
    // Register your application to get an API key here:
    //  http://developer.myspace.com/
    'myspace' => array(
        'authmyspace:MySpace',
        'key' => 'xxxxxxxxxxxxxxxx',
        'secret' => 'xxxxxxxxxxxxxxxx',
    ),
    */

    /*
    // Microsoft Account (Windows Live ID) Authentication API.
    // Register your application to get an API key here:
    //  https://apps.dev.microsoft.com/
    'windowslive' => array(
        'authwindowslive:LiveID',
        'key' => 'xxxxxxxxxxxxxxxx',
        'secret' => 'xxxxxxxxxxxxxxxx',
    ),
    */

    /*
    // Example of a LDAP authentication source.
    'example-ldap' => array(
        'ldap:LDAP',

        // Give the user an option to save their username for future login attempts
        // And when enabled, what should the default be, to save the username or not
        //'remember.username.enabled' => FALSE,
        //'remember.username.checked' => FALSE,

        // The hostname of the LDAP server.
        'hostname' => 'ldap.example.org',

        // Whether SSL/TLS should be used when contacting the LDAP server.
        'enable_tls' => TRUE,

        // Whether debug output from the LDAP library should be enabled.
        // Default is FALSE.
        'debug' => FALSE,

        // The timeout for accessing the LDAP server, in seconds.
        // The default is 0, which means no timeout.
        'timeout' => 0,

        // The port used when accessing the LDAP server.
        // The default is 389.
        'port' => 389,

        // Set whether to follow referrals. AD Controllers may require FALSE to function.
        'referrals' => TRUE,

        // Which attributes should be retrieved from the LDAP server.
        // This can be an array of attribute names, or NULL, in which case
        // all attributes are fetched.
        'attributes' => NULL,

        // The pattern which should be used to create the users DN given the username.
        // %username% in this pattern will be replaced with the users username.
        //
        // This option is not used if the search.enable option is set to TRUE.
        'dnpattern' => 'uid=%username%,ou=people,dc=example,dc=org',

        // As an alternative to specifying a pattern for the users DN, it is possible to
        // search for the username in a set of attributes. This is enabled by this option.
        'search.enable' => FALSE,

        // The DN which will be used as a base for the search.
        // This can be a single string, in which case only that DN is searched, or an
        // array of strings, in which case they will be searched in the order given.
        'search.base' => 'ou=people,dc=example,dc=org',

        // The attribute(s) the username should match against.
        //
        // This is an array with one or more attribute names. Any of the attributes in
        // the array may match the value the username.
        'search.attributes' => array('uid', 'mail'),

        // The username & password the SimpleSAMLphp should bind to before searching. If
        // this is left as NULL, no bind will be performed before searching.
        'search.username' => NULL,
        'search.password' => NULL,

        // If the directory uses privilege separation,
        // the authenticated user may not be able to retrieve
        // all required attribures, a privileged entity is required
        // to get them. This is enabled with this option.
        'priv.read' => FALSE,

        // The DN & password the SimpleSAMLphp should bind to before
        // retrieving attributes. These options are required if
        // 'priv.read' is set to TRUE.
        'priv.username' => NULL,
        'priv.password' => NULL,

    ),
    */

    /*
    // Example of an LDAPMulti authentication source.
    'example-ldapmulti' => array(
        'ldap:LDAPMulti',

        // Give the user an option to save their username for future login attempts
        // And when enabled, what should the default be, to save the username or not
        //'remember.username.enabled' => FALSE,
        //'remember.username.checked' => FALSE,

        // The way the organization as part of the username should be handled.
        // Three possible values:
        // - 'none':   No handling of the organization. Allows '@' to be part
        //             of the username.
        // - 'allow':  Will allow users to type 'username@organization'.
        // - 'force':  Force users to type 'username@organization'. The dropdown
        //             list will be hidden.
        //
        // The default is 'none'.
        'username_organization_method' => 'none',

        // Whether the organization should be included as part of the username
        // when authenticating. If this is set to TRUE, the username will be on
        // the form <username>@<organization identifier>. If this is FALSE, the
        // username will be used as the user enters it.
        //
        // The default is FALSE.
        'include_organization_in_username' => FALSE,

        // A list of available LDAP servers.
        //
        // The index is an identifier for the organization/group. When
        // 'username_organization_method' is set to something other than 'none',
        // the organization-part of the username is matched against the index.
        //
        // The value of each element is an array in the same format as an LDAP
        // authentication source.
        'employees' => array(
            // A short name/description for this group. Will be shown in a dropdown list
            // when the user logs on.
            //
            // This option can be a string or an array with language => text mappings.
            'description' => 'Employees',

            // The rest of the options are the same as those available for
            // the LDAP authentication source.
            'hostname' => 'ldap.employees.example.org',
            'dnpattern' => 'uid=%username%,ou=employees,dc=example,dc=org',
        ),

        'students' => array(
            'description' => 'Students',

            'hostname' => 'ldap.students.example.org',
            'dnpattern' => 'uid=%username%,ou=students,dc=example,dc=org',
        ),

    ),
    */

);
