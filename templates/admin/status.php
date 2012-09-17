<?php require_once('base-top.php'); ?>
<form method="post">
	<div class="control-group">
		<label class="control-label" for="id">Informe o ID da solicitação:</label>
		<div class="controls">
			<input class="input-xlarge" type="text" name="id" id="id" />
			<span class="help-block">O texto com 24 caracteres presente no rodapé da solicitação</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="status">Selecione o status da solictação:</label>
		<div class="controls">
			<select name="status" id="status">
				<option value="0"><?= $verbal_status[0] ?></option>
				<option value="1"><?= $verbal_status[1] ?></option>
				<option value="2"><?= $verbal_status[2] ?></option>
				<option value="3"><?= $verbal_status[3] ?></option>
				<option value="4"><?= $verbal_status[4] ?></option>
				<option value="5"><?= $verbal_status[5] ?></option>
				<option value="6"><?= $verbal_status[6] ?></option>
				<option value="7"><?= $verbal_status[7] ?></option>
				<option value="8"><?= $verbal_status[8] ?></option>
			</select>
		</div>
	</div>
	<button class="btn btn-primary" type="submit">Alterar</button>
</form>
<?php require_once('base-bottom.php'); ?>