<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<form method="post">
	<fieldset>
		<p>Ao encaminhar uma solicitação você está transeferindo a responsabilidade pela <?= $acao ?> dela.</p>
		<div class="control-group">
			<label class="control-label" for="to">Encaminhar para:</label>
			<div class="controls">
				<div class="input-append">
					<input class="input-xlarge" type="text" name="to" id="to"></input><span class="add-on"><?= ADLDAP_ACCOUNT_SUFFIX ?></span>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="text">Se desejar, adicione uma mensagem para essa pessoa:</label>
			<div class="controls">
				<textarea class="input-xxlarge" name="text" id="text" rows="5"></textarea>
			</div>
		</div>
	</fieldset>
	<p>
		<button class="btn btn-primary" type="submit">Encaminhar</button>
		<a class="btn" href="<?= SITE_BASE . $s->id ?>">Voltar</a>
	</p>
</form>
<?php require_once('templates/footer.php'); ?>