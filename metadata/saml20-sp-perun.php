<?php
/**
 * SAML 2.0 remote SP metadata for SimpleSAMLphp.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-sp-remote
 */


# ELIXIR Perun
# Contact: Michal Prochazka michalp@ics.muni.cz
$metadata['https://perun.elixir-czech.cz/shibboleth'] = array(
	'AssertionConsumerService' => 'https://perun.elixir-czech.cz/Shibboleth.sso/SAML2/POST',
	#'SingleLogoutService' => '',
	'OrganizationDisplayName' => array(
		'en' => 'ELIXIR AAI Directory',
	),
	'name' => array(
		'en' => 'ELIXIR AAI Directory',
	),
	'CoCo' => true,
	'RaS' => true,
	// Force auth Can be removed in future when consolidator is moved to raw-attributes SP
	'ForceAuthn' => true,
	'consent.disable' => true,
	'attributes' => array(
			'eduPersonPrincipalName',
			//'eduPersonTargetedID',
                        'eduPersonUniqueId',
                        'displayName',
                        'mail',
                        'schacHomeOrganization',
                        'eduPersonScopedAffiliation',
                        'eduPersonEntitlement',
			'https://login.elixir-czech.org/attr-name/sourceIdPEntityID',
	),
);


# ELIXIR AAI SP with raw attribute set
# Contact: Michal Prochazka michalp@ics.muni.cz
$metadata['https://perun.elixir-czech.cz/shibboleth/raw-attributes'] = array(
        'AssertionConsumerService' => 'https://perun.elixir-czech.cz/raw-attributes/Shibboleth.sso/SAML2/POST',
        #'SingleLogoutService' => '',
        'OrganizationDisplayName' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'name' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'CoCo' => true,
        'RaS' => true,
	'ForceAuthn' => true,
        'consent.disable' => true,
        'attributes' => array(
                        'eduPersonPrincipalName',
                        //'eduPersonTargetedID',
                        'eduPersonUniqueId',
                        'displayName',
                        'mail',
                        'schacHomeOrganization',
                        'eduPersonScopedAffiliation',
                        'eduPersonEntitlement',
                        'https://login.elixir-czech.org/attr-name/sourceIdPEntityID',
        ),
);

# ELIXIR AAI SP with raw attribute set require all IdPs
# Contact: Michal Prochazka michalp@ics.muni.cz
$metadata['https://perun.elixir-czech.cz/shibboleth/raw-attributes-all-idps'] = array(
        'AssertionConsumerService' => 'https://perun.elixir-czech.cz/raw-attributes-all-idps/Shibboleth.sso/SAML2/POST',
        #'SingleLogoutService' => '',
        'OrganizationDisplayName' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'name' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'CoCo' => true,
        'RaS' => true,
        'disco.showAllIdps' => true,
	'ForceAuthn' => true,
        'consent.disable' => true,
        'attributes' => array(
                        'eduPersonPrincipalName',
                        //'eduPersonTargetedID',
                        'eduPersonUniqueId',
                        'displayName',
                        'mail',
			'schacHomeOrganization',
                        'eduPersonScopedAffiliation',
                        'eduPersonEntitlement',
                        'https://login.elixir-czech.org/attr-name/sourceIdPEntityID',
        ),
);

# ELIXIR AAI SP with Google authN
# Contact: Michal Prochazka michalp@ics.muni.cz
$metadata['https://perun.elixir-czech.cz/shibboleth/google'] = array(
        'AssertionConsumerService' => 'https://perun.elixir-czech.cz/google/Shibboleth.sso/SAML2/POST',
        #'SingleLogoutService' => '',
        'OrganizationDisplayName' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'name' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'CoCo' => true,
        'RaS' => true,
	'ForceAuthn' => true,
        'consent.disable' => true,
        'attributes' => array(
                        'eduPersonPrincipalName',
                        //'eduPersonTargetedID',
                        'eduPersonUniqueId',
                        'displayName',
                        'mail',
                        'schacHomeOrganization',
                        'eduPersonScopedAffiliation',
                        'eduPersonEntitlement',
                        'https://login.elixir-czech.org/attr-name/sourceIdPEntityID',
        ),
);


# ELIXIR AAI registrar SP 
# Contact: Michal Prochazka michalp@ics.muni.cz
#	   Ondrej Velisek ondrejvelisek@mail.muni.cz
$metadata['https://perun.elixir-czech.cz/shibboleth/sp-registrar'] = array(
        'AssertionConsumerService' => 'https://perun.elixir-czech.cz/sp-registrar/Shibboleth.sso/SAML2/POST',
        'OrganizationDisplayName' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'name' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'CoCo' => true,
        'RaS' => true,
        'ForceAuthn' => false,
        'consent.disable' => true,
        'attributes' => array(
                        'eduPersonPrincipalName',
                        'eduPersonTargetedID',
                        'eduPersonUniqueId',
                        'displayName',
                        'mail',
                        'schacHomeOrganization',
                        'eduPersonScopedAffiliation',
                        'eduPersonEntitlement',
                        'https://login.elixir-czech.org/attr-name/sourceIdPEntityID',
        ),
);

# ELIXIR AAI consolidator SP 
# Contact: Michal Prochazka michalp@ics.muni.cz
#          Ondrej Velisek ondrejvelisek@mail.muni.cz
$metadata['https://perun.elixir-czech.cz/shibboleth/sp-consolidator'] = array(
        'AssertionConsumerService' => 'https://perun.elixir-czech.cz/sp-consolidator/Shibboleth.sso/SAML2/POST',
        'OrganizationDisplayName' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'name' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'CoCo' => true,
        'RaS' => true,
        'ForceAuthn' => false,
        'consent.disable' => true,
        'attributes' => array(
                        'eduPersonPrincipalName',
                        'eduPersonTargetedID',
                        'eduPersonUniqueId',
                        'displayName',
                        'mail',
                        'schacHomeOrganization',
                        'eduPersonScopedAffiliation',
                        'eduPersonEntitlement',
                        'https://login.elixir-czech.org/attr-name/sourceIdPEntityID',
        ),
);

# ELIXIR AAI conformance SP 
# Contact: Michal Prochazka michalp@ics.muni.cz
#          Ondrej Velisek ondrejvelisek@mail.muni.cz
$metadata['https://perun.elixir-czech.cz/shibboleth/sp-conformance'] = array(
        'AssertionConsumerService' => 'https://perun.elixir-czech.cz/sp-conformance/Shibboleth.sso/SAML2/POST',
        'OrganizationDisplayName' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'name' => array(
                'en' => 'ELIXIR AAI Directory',
        ),
        'CoCo' => true,
        'RaS' => true,
        'ForceAuthn' => false,
        'consent.disable' => true,
        'attributes' => array(
                        'eduPersonPrincipalName',
                        'eduPersonTargetedID',
                        'eduPersonUniqueId',
                        'displayName',
                        'mail',
                        'schacHomeOrganization',
                        'eduPersonScopedAffiliation',
                        'eduPersonEntitlement',
                        'https://login.elixir-czech.org/attr-name/sourceIdPEntityID',
        ),
);

