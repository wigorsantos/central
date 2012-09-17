<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<form method="post">
	<p>Clicando no botão <i>concluir</i> você estará confirmando que atendeu e executou essa solicitação.</p>
	<div class="control-group">
		<label class="control-label" for="text">Se desejar, adicione uma observação:</label>
		<div class="controls">
			<textarea name="text" id="text" class="input-xxlarge" rows="5"></textarea>
		</div>
	</div>
	<p>
		<button class="btn btn-primary" type="submit"><i class="icon-ok icon-white"></i> Concluir</button>
		<a class="btn" href="<?= SITE_BASE . $s->id ?>">Cancelar</a>
	</p>
</form>
<p>Você também pode...</p>
<p><a class="btn" href="<?= SITE_BASE . $s->id ?>/encaminhar"><b>Encaminhar</b></a> - Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.</p>
<?php require_once('templates/footer.php'); ?>
