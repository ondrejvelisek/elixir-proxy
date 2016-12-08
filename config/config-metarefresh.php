<?php

$config = array(

	/*
	 * Global blacklist: entityIDs that should be excluded from ALL sets.
	 */
	#'blacklist' = array(
	#	'http://my.own.uni/idp'
	#),
	
	/*
	 * Conditional GET requests
	 * Efficient downloading so polling can be done more frequently.
	 * Works for sources that send 'Last-Modified' or 'Etag' headers.
	 * Note that the 'data' directory needs to be writable for this to work.
	 */
	'conditionalGET'	=> TRUE,

	'sets' => array(

		'edugain' => array(
			'cron'		=> array('hourly'),
			'sources'	=> array(
				array(
					/*
					 * entityIDs that should be excluded from this src.
					 */
					#'blacklist' => array(
					#	'http://some.other.uni/idp',
					#),

					/*
					 * Whitelist: only keep these EntityIDs.
					 */
					#'whitelist' => array(
					#	'http://some.uni/idp',
					#	'http://some.other.uni/idp',
					#),

					#'conditionalGET' => TRUE,
					'src' => 'https://mds.edugain.org',
					'certificates' => array(
						'edugain-metadata-cert.pem',
						//'rollover.crt',
					),
					//'validateFingerprint' => '12:8F:40:34:6A:D0:BE:D0:D2:92:8E:07:11:89:90:A7:46:04:30:22:D0:3D:55:22:2E:62:60:7C:C3:D5:40:C0',
					/*	
					'template' => array(
						'tags'	=> array('edugain'),
						'authproc' => array(
							51 => array('class' => 'core:AttributeMap', 'oid2name'),
						),
					),
					*/

					/*
					 * The sets of entities to load, any combination of:
					 *  - 'saml20-idp-remote'
					 *  - 'saml20-sp-remote'
					 *  - 'shib13-idp-remote'
					 *  - 'shib13-sp-remote'
					 *  - 'attributeauthority-remote'
					 *
					 * All of them will be used by default.
					 *
					 * This option takes precedence over the same option per metadata set.
					 */
					//'types' => array(),

					/*
					 * Filter entities based on entity-attributes. If they have at least one of the listed below, they will be allowed.
					 */
/*
					'entity-attributes' => array(
						'http://macedir.org/entity-category-support' => array (
							'http://refeds.org/category/research-and-scholarship',
							'http://www.geant.net/uri/dataprotection-code-of-conduct/v1',
						),
					),
*/
					'entity-attributes' => array(
						'*',
						'http://macedir.org/entity-category' => array (
							'!http://refeds.org/category/hide-from-discovery',
						),
					),

				),
			),
			'expireAfter' 		=> 60*60*24*4, // Maximum 4 days cache time
			'outputDir' 	=> 'metadata/metadata-edugain/',

			/*
			 * Which output format the metadata should be saved as.
			 * Can be 'flatfile' or 'serialize'. 'flatfile' is the default.
			 */
			'outputFormat' => 'flatfile',


			/*
			 * The sets of entities to load, any combination of:
			 *  - 'saml20-idp-remote'
			 *  - 'saml20-sp-remote'
			 *  - 'shib13-idp-remote'
			 *  - 'shib13-sp-remote'
			 *  - 'attributeauthority-remote'
			 *
			 * All of them will be used by default.
			 */
			'types' => array('saml20-idp-remote'),
		),
	),
);



