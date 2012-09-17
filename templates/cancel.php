<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<form method="post">
	<fieldset>
		<p>Você está prestes a parar o progresso dessa solicitação. Ela será arquivada.</p>
		<div class="control-group">
			<label class="control-label" for="text">Explique porque essa solicitação está sendo cancelada:</label>
			<textarea name="text" id="text" class='input-xxlarge' rows="5"></textarea>
		</div>
	</fieldset>
	<p>
		<button class="btn btn-warning" type="submit"><i class="icon-remove icon-white"></i> Cancelar</button>
		<a class="btn" href="<?= SITE_BASE . $s->id ?>">Voltar</a>
	</p>
</form>
<?php require_once('templates/footer.php'); ?>
