<?php 
	require_once(dirname(__FILE__) . '/../setup.php');
	require_once(dirname(__FILE__) . '/AuthAPI.class.php');
?>
<?php
	if(isset($_REQUEST['a']) && $_REQUEST['a'] == 'showToken'){
		header('Content-Type: text/plain; charset=utf8');
		if($_REQUEST['error']){
			echo 'ERROR: ' . $_REQUEST['message'];
		}else{
			echo $_REQUEST['token'];
		}
		exit();
	}

	$callback = isset($_REQUEST['callback']) && !empty($_REQUEST['callback']) ? $_REQUEST['callback'] : '?a=showToken';
	$api = new AuthAPI();
	if(!$api->loadApp($_REQUEST['id'],$_REQUEST['secret'])){
		header('Location: ' . $api->getCallbackURL($callback));
		exit();
	}
	if($api->isAuthorized($_SESSION['user'])){
		header('Location: ' . $api->getCallbackURL($callback));
		exit();
	}
	
	if(IS_POST){
		$a = $_REQUEST['a'];
		$api->action($a,$_SESSION['user']);
		header('Location: ' . $api->getCallbackURL($callback));
		exit();
	}

	$_TITLE = "Autorizar o acesso do aplicativo";
?>
<?php require_once(dirname(__FILE__) . '/../templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<p>O aplicativo abaixo está solicitando a sua autorização para acessar a Central de Solicitações em seu nome.</p>
<div class="row-fluid">
<div class="span4 well">
		<h2><?= $api->app->name ?></h2>
		<?= $api->app->description ?>	
</div>
<div class="span4">
		<strong>Este aplicativo poderá:</strong>
		<ul>
			<li>Listar as suas solicitações.</li>
			<li>Listar as suas pendências.</li>
			<li>Listar as suas participações.</li>
			<li>Criar, editar e excluir as suas solicitações.</li>
			<li>Aprovar, recusar, devolver e encaminhar as suas pendências.</li>
			<li>Cotar, analisar e executar as suas pendências.</li>
		</ul>	
</div>
</div>
<p>
	<form action="<?= SITE_BASE ?>API/auth" method="post" style="display:inline;">
		<input type='hidden' name='a' value='refuse'>
		<input type='hidden' name='id' value='<?= $_REQUEST['id'] ?>'>
		<input type='hidden' name='secret' value='<?= $_REQUEST['secret'] ?>'>
		<input type='hidden' name='callback' value='<?= $_REQUEST['callback'] ?>'>
		<button class="btn btn-large">Recusar</button>
	</form>
	<form action="<?= SITE_BASE ?>API/auth" method="post" style="display:inline;">
		<input type='hidden' name='a' value='accept'>
		<input type='hidden' name='id' value='<?= $_REQUEST['id'] ?>'>
		<input type='hidden' name='secret' value='<?= $_REQUEST['secret'] ?>'>
		<input type='hidden' name='callback' value='<?= $_REQUEST['callback'] ?>'>
		<button class="btn btn-large btn-primary" type="submit">Aceitar</button>
	</form>
	
</p>

<?php require_once(dirname(__FILE__) . '/../templates/footer.php'); ?>