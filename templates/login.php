<?php require_once('templates/header.php'); ?>
<?php if(isset($_SESSION['login_error']) && $_SESSION['login_error']){ $erro = true; unset($_SESSION['login_error']); } ?>
<div class="row-fluid">
	<div class="span3 offset3">
		<div style="text-align:center;"><img src="<?= SITE_BASE . ORG_LOGO ?>"></div>
		<h1>Bem vindo!</h1>
		<p>Utilize o seu usuário da rede para acessar.</p>
	</div>
	<div class="span3 col2-bordered">
		<form method="POST">
			<div class="control-group <?= $erro ? 'error' : '' ?>">
				<label class="control-label" for="user">Usuário</label>
				<div class="controls"><input type="text" name="user" id="user"></div>
			</div>
			<div class="control-group <?= $erro ? 'error' : '' ?>">
				<label class="control-label" for="pass">Senha</label>
				<div class="controls"><input type="password" name="pass" id="pass"></div>
			</div>
			<div class="clear" style="margin-top:10px;"></div>
			<button class="btn btn-primary" type="submit">Acessar</button>
		</form>
	</div>
</div>
<?php require_once('templates/footer.php'); ?>