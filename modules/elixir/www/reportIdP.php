<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo 'ERROR request has to be POST';
        die;
}
if (!isset($_POST['idpEntityId'])) {
        echo 'ERROR parametr "idpEntityId" is missing';    
        die;
}
if (!isset($_POST['isOk'])) {
        echo 'ERROR parametr "isOk" is missing';
        die;
}
if (!isset($_POST['redirectUri'])) {
        echo 'ERROR parametr "redirectUri" is missing';
        die;
}


$config = SimpleSAML_Configuration::getInstance();

$message = <<<EOD

User message: {$_POST['body']}

IdP name displayed to user: {$_POST['idpDisplayName']}
IdP entityId: {$_POST['idpEntityId']}

Released all attributes: {$_POST['isOk']}
 - user's identifier: {$_POST['hasUid']}
 - user's affiliation: {$_POST['hasAffiliation']}
 - user's organization: {$_POST['hasOrganization']}

Time of the check: {$_POST['time']}

Result were saved on machine: {$_POST['resultInFile']}
IdP were whitelisted automatically: {$_POST['resultOnProxy']}

EOD;

$toAddress = $config->getString('technicalcontact_email', 'N/A');
if ($toAddress !== 'N/A') {
    $email = new SimpleSAML_XHTML_EMail($toAddress, 'Report: '.$_POST['title'], $_POST['from']);
    $email->setBody($message);
    $email->send();
}

echo '<h1>Unssuported yet</h1>';

echo "back to <a href='{$_POST['redirectUri']}'>{$_POST['redirectUri']}</a>";

$url = parse_url($_POST['redirectUri']);

if (empty($parseUrl['query'])) {
	$parseQuery = array();
} else {
	parse_str($parseUrl['query'], $parseQuery);
}
$parseQuery['mailSended'] = true;
$url['query'] = http_build_query($parseQuery);

$urlString = build_url($url);

// redirect the user back 
\SimpleSAML\Utils\HTTP::redirectTrustedURL($urlString);





/////////// functions

/*
 Opposite of standard parse_url. 
*/
function build_url($parsUrl) {

	$url = $parsUrl['scheme'] . "://";
	if (!empty($parsUrl['user'])) {
		$url .= $parsUrl['user'];
		if (!empty($parsUrl['pass'])) {
			$url .= ":" . $parsUrl['pass'];
		}
		$url .= "@";
	}
	$url .= $parsUrl['host'];
	if (!empty($parsUrl['port'])) {
		$url .= ":" . $parsUrl['port'];
	}
	if (!empty($parsUrl['path'])) {
	        $url .= $parsUrl['path'];
	}
	if (!empty($parsUrl['query'])) {
	        $url .= "?" . $parsUrl['query'];
	}
	if (!empty($parsUrl['fragment'])) {
	        $url .= "#" . $parsUrl['fragment'];
	}
	
	return $url;
}


?>
