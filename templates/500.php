<?php 
require_once(dirname(__FILE__) . '/../setup.php'); 
$_TITLE = "Erro interno";
$_SESSION['IS_ERROR'] = true;
require_once('header.php'); 
?>

<h1>Opss! <small>Algo deu errado.</small></h1>

<p>Ocorreu um erro no servidor. Por favor, envie um e-mail para <a href="mailto:<?= ORG_TIMAIL ?>"><?= ORG_TIMAIL ?></a> informando como o erro apareceu. Ex.: Quando cliquei em imprimir.</p>
<? if(isset($E)){ ?>

<pre><?= $E['message'] ?></pre>
<a class="btn btn-mini pull-right" data-toggle="collapse" data-target="#trace">Mais detalhes</a>
<div id="trace" class="collapse" style="margin:10px;">
	<dl class="dl-horizontal">
		<dt>Error message: </dt><dd><?= $E['message'] ?></dd>
		<dt>Error code: </dt><dd><?= $E['code'] ?></dd>
		<dt>File: </dt><dd><?= $E['file'] ?> (at line <?= $E['line'] ?>)</dd>
	</dl>
	<strong>Trace: </strong>
	<pre><?= $E['trace_string'] ?></pre>
</div>

<?php } require_once('footer.php'); ?>