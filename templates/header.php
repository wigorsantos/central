<!DOCTYPE html>
<html lang="pt">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="ie=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
		<meta name="author" content="Luiz Fernando da Silva <lfsilva@sccorinthians.com.br>" />
		<meta name="copyright" content="Sport Club Corinthians Paulista" />
		<meta name="description" content="Site para fluxo de solicitações diversas" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <link rel="apple-touch-icon" href="<?= SITE_BASE ?>res/icons/iphone.png"/>
        <link rel="shortcut icon" href="<?= SITE_BASE ?>res/icons/icon.ico" type="image/x-icon" />
        <link rel="icon" href="<?= SITE_BASE ?>res/icons/icon.png" sizes="96x96" type="image/png">
        <link rel="icon" href="<?= SITE_BASE ?>res/icons/icon.svg" sizes="any" type="image/svg+xml">
		<title><?= $_TITLE ? $_TITLE . " - " : "" ?>Central de Solicitações</title>
		<!--<link rel="stylesheet" href="<?= SITE_BASE ?>res/css/styles.css" />-->
		<link rel="stylesheet" href="<?= SITE_BASE ?>res/css/bootstrap.min.css" />
		<link rel="stylesheet" href="<?= SITE_BASE ?>res/css/custom.css" />
		<?php if(IS_ADMIN) { ?><link rel="stylesheet" href="<?= SITE_BASE ?>res/css/admin.css" /><?php } ?>
		<link rel="stylesheet" href="<?= SITE_BASE ?>res/css/bootstrap-responsive.min.css" />
		<script src='https://www.google.com/jsapi'></script>
		<script src="<?= SITE_BASE ?>res/js/jquery.js"></script>
		<script src="<?= SITE_BASE ?>res/js/jquery.maskedinput.js"></script>
		<script src="<?= SITE_BASE ?>res/js/jquery.price_format.js"></script>
	</head>
	<body <?php  if($_SESSION['role'] == 3){ echo 'class="superuser"'; } ?>>			
			
		<div class="navbar navbar-fixed-top <?= $_SESSION['IS_ERROR'] || IS_ADMIN || $_SESSION['SUDO_MODE'] ? 'navbar-inverse' : '' ?>">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a href="/" class="brand" style="background-image:url('<?= SITE_BASE . ORG_ICON ?>')">
						<span class="visible-desktop">Central de Solicitações</span>
					</a>

					<?php if(HAS_USER){
							$user_info = AD::info($_SESSION['user'],array('displayname','department','manager','title'));
					 ?>
					<ul class="nav pull-right">
						<li<?= $section == 'profile' ? ' class="active"' : '' ?> class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="icon-user<?= $_SESSION['IS_ERROR'] || IS_ADMIN || $_SESSION['SUDO_MODE'] ? ' icon-white' : '' ?>"></i>
								<?= $_SESSION['role'] == 3 ? '<i class="icon-exclamation-sign' . ($_SESSION['IS_ERROR'] || IS_ADMIN || $_SESSION['SUDO_MODE'] ? ' icon-white' : '') . '" title="Acesso de super usuário"></i>' : '' ?>
								<?= $_SESSION['SUDO_MODE'] ? '<i class="icon-exclamation-sign' . ($_SESSION['IS_ERROR'] || IS_ADMIN || $_SESSION['SUDO_MODE'] ? ' icon-white' : '') . '" title="Acessando como outro usuário"></i>' : '' ?>
								<b class='caret'></b>
							</a>
							<ul class="dropdown-menu">
								<li><span class="username">
									<?= $user_info[0]['displayname'][0] ?>
									<?= $_SESSION['role'] == 3 ? '<i class="icon-exclamation-sign" title="Acesso de super usuário"></i> <small>Acesso de super usuário</small>' : '' ?>
									<?= $_SESSION['SUDO_MODE'] ? '<i class="icon-exclamation-sign" title="Acessando como outro usuário"></i> <small>Acessando como outro usuário</small>' : '' ?>
								</span></li>
								<?php if($_SESSION['role'] == 3) { ?>
								<li><a data-placement="left" title="Acessar como outro usuário" href="javascript:window.location = '?sudo='+prompt('Informe o usuário:')">Ver como...</a></li>
								<?php } ?>
								<li><a data-placement="left" title="Veja e atualize as suas informações" href="<?= SITE_BASE ?>perfil">Meu perfil</a></li>
								<li><a data-placement="left" title="Ajude a melhorar a Central de solicitações" href="<?= SITE_BASE ?>feedback">Enviar feedback</a></li>
								<li><a data-placement="left" title="Conheça o que há de novo" href="<?= SITE_BASE ?>novidades">Novidades da versão</a></li>
								<li class="divider"></li>
								<li><a data-placement="left" title="Encerre a sua sessão na Central de solicitações" href="<?= SITE_BASE ?>logout">Sair do sistema</a></li>
							</ul>
						</li>
					</ul>
					<?php } ?>

					<div class="nav-collapse">
					<?php if(HAS_USER && (!isset($_SESSION['IS_FIRST_ACCESS']) || !$_SESSION['IS_FIRST_ACCESS'])){ ?>
						<ul class="nav">
							<li<?= $section == 'home' ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>todas" data-placement="bottom" title="Mostra todas as solicitações criadas por você">Minhas solicitações</a></li>
							<li<?= $section == 'pendencias' ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>pendencias" data-placement="bottom" title="Mostra todas as solicitações que precisam de uma ação sua">Minhas pendências <?php 
								$count_pendencias = Views::countPendencias($_SESSION['user']); 
								if(!empty($count_pendencias)){
									echo '<span class="badge badge-important">' . $count_pendencias . '</span>';
								}
							?></a></li>
							<li<?= $section == 'participacoes' ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>participacoes" data-placement="bottom" title="Mostra todas as solicitações que envolveram você">Minhas participações</a></li>
							<li<?= $section == 'nova' ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>nova" data-placement="bottom" title="Crie uma nova solicitação">Nova solicitação</a></li>
						<?php if(DB::$roles->count(array("user" => $_SESSION['user'])) > 0){ ?>
							<li<?= $section == 'admin' ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin" data-placement="bottom" title="Gerencie o sistema.">Administração</a></li>
						<?php } ?>
						</ul>
					<?php } ?>
					</div>

				</div>
			</div>
		</div>
		<header>
		<?php if(isset($_SESSION['flash'])){ ?>
			<div id="flash" class="alert fade in hide">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<strong>Notificação:</strong><div class="hidden-phone"> </div> <?= $_SESSION['flash'] ?>
			</div>	
		<?php } ?>
		</header>	
		<div class="container-fluid" id="application-body">
			<div class="row-fluid">
