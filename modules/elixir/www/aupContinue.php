<?php

$id = $_REQUEST['StateId'];
$state = SimpleSAML_Auth_State::loadState($id, 'elixir:forceAup');

$user = sspmod_elixir_Perun::get('usersManager', 'getUserByExtSourceNameAndExtLogin', array(
	'extSourceName' => $state['IdPMetadata']['entityid'],
	'extLogin' => $state['Attributes'][$state['eppnAttr']][0],
));



$forceAup = sspmod_elixir_Perun::get('attributesManager', 'getAttribute', array(
	'user' => $user['id'],
	'attributeName' => $state['perunForceAttr'],
));


$aup = sspmod_elixir_Perun::get('attributesManager', 'getAttribute', array(
        'user' => $user['id'],
        'attributeName' => $state['perunAupAttr'],
));

if (empty($aup['value'])) {
	$aup['value'] = array($forceAup['value']);
} else {
	array_push($aup['value'], $forceAup['value']);
}

sspmod_elixir_Perun::post('attributesManager', 'setAttribute', array(
        'user' => $user['id'],
        'attribute' => $aup,
));


$forceAup['value'] = null;

sspmod_elixir_Perun::post('attributesManager', 'setAttribute', array(
	'user' => $user['id'],
	'attribute' => $forceAup,
));



SimpleSAML_Logger::info('Elixir.ForceAup - User accepted');


SimpleSAML_Auth_ProcessingChain::resumeProcessing($state);




