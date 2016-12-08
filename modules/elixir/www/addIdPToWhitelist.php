<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	sendError('request has to be POST');
}
if (!isset($_POST['entityId'])) {
        sendError('parametr \"entityId\" is missing');    
}


// TODO: decompose into functions


// Has to be unique systemwide. How to check???
$greylistSemKey = 745;
$greylistSem = sem_get($greylistSemKey);
if ($greylistSem === false) {
        sendError('Fail to get greylist semaphore');
}




// greylist SYNC section start


if (!sem_acquire($greylistSem)) {
        sem_remove($greylistSem);
        sendError('Fail to aquire semaphore '.$greylistSem);
}



$greylistPath = SimpleSAML_Module::getModuleDir('elixir/greylist');

$greyLines = file($greylistPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($greyLines === false) {
        sem_remove($greylistSem);
        sendError('while reading greylist file');
}

$greyStatus = 'NOT_IN';
if (in_array($_POST['entityId'], $greyLines)) {
	$greyStatus = 'IN';

        $greyLines = array_diff($greyLines, array($_POST['entityId']));

        $greyRes = file_put_contents($greylistPath, implode(PHP_EOL, $greyLines));
        if ($greyRes === false) {
                sem_remove($greylistSem);
                sendError('while writing greylist file');
	}
}




// greylist SYNC section end

if (!sem_release($greylistSem)) {
        sem_remove($greylistSem);
        sendError('Fail to release '.$greylistSem.' semaphore.');
}







// Has to be unique systemwide. How to check???
$whitelistSemKey = 744;
$whitelistSem = sem_get($whitelistSemKey);
if ($whitelistSem === false) {
        sendError('Fail to get whitelist semaphore');
}





// whitelist SYNC section start


if (!sem_acquire($whitelistSem)) {
        sem_remove($whitelistSem);
	sendError('Fail to aquire semaphore '.$whitelistSem);
}



$whitelistPath = SimpleSAML_Module::getModuleDir('elixir/whitelist');

$lines = file($whitelistPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($lines === false) {
	sem_remove($whitelistSem);
	sendError('while reading whitelist file');	
}


if (in_array($_POST['entityId'], $lines)) {
	sem_remove($whitelistSem); 
        header('Content-Type: application/json');
	if ($greyStatus === 'NOT_IN') {
	    echo json_encode(array(
                'result' => 'ALREADY_THERE',
                'msg' => 'IdP "'.$_POST['entityId'].'" is already whitelisted'
            ));
	} else {
            echo json_encode(array(
                'result' => 'ADDED',
                'whitelist' => $lines,
		'greylist' => $greyLines
            ));
	}
        die;
}


array_push($lines, $_POST['entityId']);

$res = file_put_contents($whitelistPath, implode(PHP_EOL, $lines));
if ($res === false) {
	sem_remove($whitelistSem);
        sendError('while writing file');
}



// whitelist SYNC section end

if (!sem_release($whitelistSem)) {
	sem_remove($whitelistSem);
        sendError('Fail to release '.$whitelistSem.' semaphore.');
}






header('Content-Type: application/json');
echo json_encode(array(
	'result' => 'ADDED',
	'whitelist' => $lines,
	'greylist' => $greyLines
));






function sendError($msg) {
	header('Content-Type: application/json');
	echo json_encode(array(
		'result' => 'ERROR',
		'msg' => $msg
	));
	die;
}

?>
