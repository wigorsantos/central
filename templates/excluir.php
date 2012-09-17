<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<form method="post" id='f'>
	<p>Ao excluir a solicitação todas as informações relacionadas a ela também serão automaticamente excluídas e não há como desfazer essa operação.</p>
	<?php if($s->status[0] == 2 || $s->status[0] == 3){ echo '<p>Os aprovadores dessa solicitação serão notificados sobre a sua exclusão.</p>';} ?>
	<p><strong>Você realmente deseja excluir essa solicitação?</strong></p>
	<input type='hidden' name='ok' id='ok' value='1'>
	<button class="btn btn-danger" type="button" onclick='$("#ok").val(0);$("#f").submit();'>Sim</button>
	<button class="btn" type="submit">Não</button>
</form>
<?php require_once('templates/footer.php'); ?>