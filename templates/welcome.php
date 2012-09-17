<?php require_once('templates/header.php'); ?>
<div class="row-fluid">
	<div class="span12">
	<h1>Bem vindo!</h1>
		<div class="row-fluid">
			<div class="span6">
				<div style="margin:10px;">
				<p>Essa é a sua central de solicitações! Com essa ferramenta você não 
					precisará mais ficar preenchendo fichas em papel e se deslocando 
					para conseguir assinaturas e aprovações. O processo está automatizado
					e tudo acontece eletrônicamente com notificações por e-mail.</p>
				<p>Antes de continuar precisamos que você confirme algumas informações importantes.</p>
				</div>
			</div>
			<div class="span6 col2-bordered">
				<div style="margin:10px;">
				<form method="post">
					<h3><?= $uinfo['displayname']; ?></h3>
					<fieldset>
						<div class="control-group">
							<label class="control-label" for="departamento">Seu departamento</label>
							<div class="controls">
								<input type="text" class="input-xlarge" name="departamento" id="departamento" value="<?= $uinfo['department'] ?>">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="cargo">Seu cargo</label>
							<div class="controls">
								<input type="text" class="input-xlarge" name="cargo" id="cargo" value="<?= $uinfo['title'] ?>">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="gerente">Email do gerente do seu departamento</label>
							<div class="controls">
								<div class="input-append">
									<input type="text" class="input-small" name="gerente" id="gerente" value="<?= $uinfo['manager'] ?>"><span class="add-on"><?= ADLDAP_ACCOUNT_SUFFIX ?></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="telefone">Seu ramal (ou telefone)</label>
							<div class="controls">
								<input type="text" class="input-xlarge" name="telefone" id="telefone" value="<?= $uinfo['telephone'] ?>">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="celular">Seu celular</label>
							<div class="controls">
								<input type="text" class="input-xlarge" name="celular" id="celular" value="<?= $uinfo['mobile'] ?>">
							</div>
						</div>
					</fieldset>
					<div style="margin:5px;">
						<button class="btn btn-large btn-primary" type="submit">Continuar</button>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require_once('templates/footer.php'); ?>