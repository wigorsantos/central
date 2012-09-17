<?php
require_once(dirname(__FILE__) . '/../setup.php'); 
$_TITLE = "Não encontrado";
$_SESSION['IS_ERROR'] = true;
require_once('header.php'); 
?>

<h1>Opss!</h1>

<p>O endereço <code><?= $_SERVER['REDIRECT_URL'] ?></code> não existe. Tem certeza que digitou corretamente?</p>

<?php require_once('footer.php'); ?>