<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<form method="post" id="form">
	<input name="resposta" id="resposta" type="hidden">
	<p>Você precisa analisar essa solicitação e indicar se possui ou não algum problema.</p>
	<fieldset>
		<div class="control-group">
			<label for="text">Descreva a sua resposta:</label>
			<textarea name="text" id="text" style='width: 400px;max-width:90%;' rows="5"></textarea>
			<span class="help-block">Explique o motivo da sua decisão para ajudar os aprovadores a enteder essa solicitação.</span>
		</div>
	</fieldset>
	<p>Escolha a sua resposta:</p>
	<p>
		<a class="btn btn-info" href="javascript:$('#resposta').val('no');$('#form').submit();"><i class="icon-thumbs-up icon-white"></i> <b> Sem problemas</b></a>
		- Essa solicitação não possui nenhum problema.
	</p>
	<p>
		<a class="btn btn-warning" href="javascript:$('#resposta').val('yes');$('#form').submit();"><i class="icon-thumbs-down icon-white"></i> <b> Com problemas</b></a>
		- Essa solicitação possui algum problema.
	</p>
	<p>
		<a class="btn" href="<?= SITE_BASE . $s->id ?>">Voltar</a>
		- Voltar e responder mais tarde.
	</p>
</form>
<p>Você também pode...</p>
<p><a class="btn" href="<?= SITE_BASE . $s->id ?>/encaminhar"><b>Encaminhar</b></a> - Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.</p>
<?php require_once('templates/footer.php'); ?>