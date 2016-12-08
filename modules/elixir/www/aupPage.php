<!DOCTYPE html>
<html>
<head>
	<title>Access usage policy</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo SimpleSAML_Module::getModuleUrl('elixir/aup.css') ?>">
</head>

<body>

<div class="container">
<div class="row">
<div class="col-sm-8 col-sm-offset-2">

<?php
SimpleSAML_Logger::info('elixir.ForceAup - Page shown');

$id = $_REQUEST['StateId'];
$state = SimpleSAML_Auth_State::loadState($id, 'elixir:forceAup');

echo '<img id="logo" src="' . SimpleSAML_Module::getModuleUrl('elixir/res/img/logo_256.png') . '" alt="ELIXIR logo" >';
?>


<form method="post" action="<?php echo SimpleSAML_Module::getModuleURL('elixir/aupContinue.php'); ?>" >

	<input type="hidden" name="StateId" value="<?php echo $id ?>" >

	<div class="form-group">
		<input type="submit" value="I agree with the Acceptable Usage Policy" class="btn btn-lg btn-primary btn-block">
	</div>	
	<p>
		See the <a href="<?php echo $state['aupUrl']; ?>" target="_blank">ELIXIR Acceptable Usage Policy <i class="glyphicon glyphicon-new-window"></i></a>.
	</p>
</form>

</div>
</div>
</div>

</body>

</html>





