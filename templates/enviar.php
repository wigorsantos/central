<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<form method="post">
<p>Você está prestes a enviar esta solicitação para aprovação. Você não poderá mais editar a solicitação exceto se um dos aprovadores devolver para revisão.</p>
<button class="btn btn-primary" type="submit">Enviar</button> 
<button class="btn" type="button" onclick="window.location = '<?= SITE_BASE . $s->id ?>';">Cancelar</button>
</form>
<?php require_once('templates/footer.php'); ?>