#!/usr/bin/php

<?php

include("./saml20-idp-remote.php");

foreach ($metadata as $idp) {
	if (isset($idp['scope'])) {
		foreach ($idp['scope'] as $scope) {
			print '@' . $scope . '|' . $idp['name']['en'] . '#';
		}
	}
}
?>
