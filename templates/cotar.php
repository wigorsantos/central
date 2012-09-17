<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<form enctype="multipart/form-data" method="post">
	<fieldset>
		<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
		<p>Anexe o arquivo com as três cotações para essa solicitação:</p>
		<div class="control-group">
			<label class="control-label" for="file">Escolha o arquivo:</label>
			<input class="input-file" name="file" id="file" type="file" />
			<span class="help-block">Ele deve conter no máximo 10MB</span>
		</div>
		<div class="control-group">
			<label class="control-label" for="text">Se desejar, adicione uma observação:</label>
			<textarea name="text" id="text" class='input-xxlarge' rows="5"></textarea>
		</div>
	</fieldset>
	<p>
		<button class="btn btn-primary" type="submit"><i class="icon-upload icon-white"></i> Enviar</button>
		<a class="btn" href="<?= SITE_BASE . $s->id ?>">Voltar</a>
	</p>
</form>
<p>Você também pode...</p>
<p><a class="btn" href="<?= SITE_BASE . $s->id ?>/encaminhar"><b>Encaminhar</b></a> - Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.</p>
<?php require_once('templates/footer.php'); ?>
