<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<?php if(IS_POST){ ?>
<p>O seu feedback foi registrado e enviado!</p>
<button class="btn btn-primary" type="submit" onclick="window.location = '<?= SITE_BASE ?>todas';">Voltar para minhas solicitações</button>
<? } else { ?>
<p>Essa ferramenta foi desenvolvida para você, se alguma coisa não te agrada, se encontrou um problema ou se tem uma idéia para facilitar o seu trabalho, envie a sua mensagem.</p>
<form enctype="multipart/form-data" method="post">
	<fieldset>
		<div class="span6">
			<div class="control-group">
				<label class="control-label" for="message-type">Estou escrevendo sobre:</label>
				<div class="controls">
					<select name="message-type" id="message-type" style="width:100%;">
						<option value="error">um erro</option>
						<option value="suggestion">uma sugestão</option>
						<option value="complaint">uma reclamação</option>
						<option value="compliment">um elogio</option>
						<option value="other">outra coisa</option>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="message">Escreva o seu recado:</label>
				<div class="controls">
					<textarea name="message" id="message" style="width:100%;" rows="7"></textarea>
				</div>
			</div>
		</div>
		<div class="control-group span6 col2-bordered">
			<label class="control-label">Gostaria de dizer como se sente com essa ferramenta?</label>
			<div class="controls">
				<p><label class="radio"><input type="radio" name="rating" id="" value="2"><strong><img src="<?= SITE_BASE ?>res/img/emot+2.png" class="smiley" />Muito feliz.</strong>
				<span class="help-block">Está facilitando muito o meu trabalho.</span></label></p>
				<p><label class="radio"><input type="radio" name="rating" id="" value="1"><strong><img src="<?= SITE_BASE ?>res/img/emot+1.png" class="smiley" />Feliz.</strong>
				<span class="help-block">Está ajudando um pouco no meu trabalho.</span></label></p>
				<p><label class="radio"><input type="radio" name="rating" id="" value="0"><strong><img src="<?= SITE_BASE ?>res/img/emot0.png" class="smiley" />Indiferente.</strong>
				<span class="help-block">Não mudou nada para mim.</span></label></p>
				<p><label class="radio"><input type="radio" name="rating" id="" value="-1"><strong><img src="<?= SITE_BASE ?>res/img/emot-1.png" class="smiley" />Triste.</strong>
				<span class="help-block">Está me atrapalhando um pouco.</span></label></p>
				<p><label class="radio"><input type="radio" name="rating" id="" value="-2"><strong><img src="<?= SITE_BASE ?>res/img/emot-2.png" class="smiley" />Muito triste.</strong>
				<span class="help-block">Está me atrapalhando muito, ou até me impedindo de trabalhar.</span></label></p>
			</div>
		</div>
	</fieldset>
	<p>
		<button class="btn btn-primary" type="submit">Enviar</button> 
		<button class="btn" type="button" onclick="window.location = '<?= SITE_BASE ?>todas';">Voltar<span class="hidden-phone"> para minhas solicitações</span></button>
	</p>
</form>
<? } require_once('templates/footer.php'); ?>