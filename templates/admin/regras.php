<?php require_once('base-top.php'); ?>
<form method="post" class="form-horizontal">

<p>
	Informe abaixo os usuários que possuem permissões especiais na Central de Solicitações
	<span class="label label-info"><i class="icon-info-sign icon-white"></i> Utilize o nome de usuário das pessoas, separando por ; (ponto-e-vírgula).</span>
</p>

<div class"control-group">
	<label class="control-label" for="gerente">Gerentes</label>
	<div class="controls">
		<textarea class="input-xxlarge" name="gerente" id="gerente"><?php
		foreach ($D['gerente'] as $pessoa) {
			echo $pessoa . ";";
		}
		?></textarea>
		<span class="help-block">Pessoas que podem acessar estatísticas e relatórios</span>
	</div>
</div>

<div class"control-group">
	<label class="control-label" for="desenvolvedor">Desenvolvedores</label>
	<div class="controls">
		<textarea class="input-xxlarge" name="desenvolvedor" id="desenvolvedor"><?php
		foreach ($D['desenvolvedor'] as $pessoa) {
			echo $pessoa . ";";
		}
		?></textarea>
		<span class="help-block">Pessoas que podem acessar os logs, feedbacks e configurações de SMTP e Active Directory</span>
	</div>
</div>

<div class"control-group">
	<label class="control-label" for="administrador">Administradores</label>
	<div class="controls">
		<textarea class="input-xxlarge" name="administrador" id="administrador"><?php
		foreach ($D['administrador'] as $pessoa) {
			echo $pessoa . ";";
		}
		?></textarea>
		<span class="help-block">Pessoas que podem acessar todas as opções de administração, exceto a edição manual de solicitações</span>
	</div>
</div>

<div class"control-group">
	<label class="control-label" for="super">Super usuários</label>
	<div class="controls">
		<textarea class="input-xxlarge" name="super" id="super"><?php
		foreach ($D['super'] as $pessoa) {
			echo $pessoa . ";";
		}
		?></textarea>
		<span class="help-block">Pessoas que possuem acesso total a todas as configurações e solicitações</span>
	</div>
</div>

<div class="form-actions">
		<button type="submit" class="btn btn-primary">Salvar alterações</button>
	</div>

</form>

<?php require_once('base-bottom.php'); ?>