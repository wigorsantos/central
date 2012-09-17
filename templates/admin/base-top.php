<?php require_once('templates/header.php'); ?>
<div class="container-fluid">
	<h1><?= $_TITLE ?></h1>
	<hr>
	<div class="row-fluid">
		<div class="span3">
			<ul class="nav nav-list">
				<li<?= $action == "dashboard" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin"><i class="icon-home"></i> <?= $titles_map['dashboard'] ?></a></li>
				
				<li class="nav-header">Básico</li>
				<?php if($role['role'] > 0) { ?> <li<?= $action == "tipos" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/tipos"><i class="icon-wrench"></i> <?= $titles_map['tipos'] ?></a></li> <?php } ?>
				<?php if($role['role'] > 0) { ?> <li<?= $action == "especialistas" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/especialistas"><i class="icon-wrench"></i> <?= $titles_map['especialistas'] ?></a></li> <?php } ?>
				<?php if($role['role'] > 0) { ?> <li<?= $action == "centros" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/centros"><i class="icon-wrench"></i> <?= $titles_map['centros'] ?></a></li> <?php } ?>
				<li<?= $action == "relatorios" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/relatorios"><i class="icon-wrench"></i> <?= $titles_map['relatorios'] ?></a></li>
				<?php if($role['role'] > 0) { ?> <li<?= $action == "feedbacks" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/feedbacks"><i class="icon-wrench"></i> <?= $titles_map['feedbacks'] ?></a></li> <?php } ?>
				<li<?= $action == "logs" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/logs"><i class="icon-wrench"></i> <?= $titles_map['logs'] ?></a></li>
				<?php if($role['role'] == 3) { ?> <li<?= $action == "all" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/all"><i class="icon-wrench"></i> <?= $titles_map['all'] ?></a></li> <?php } ?>
				
				<?php if($role['role'] > 0) { ?>
				<li class="nav-header">Avançado</li>
				<li<?= $action == "org" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/org"><i class="icon-wrench"></i> <?= $titles_map['org'] ?></a></li>
				<li<?= $action == "env" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/env"><i class="icon-wrench"></i> <?= $titles_map['env'] ?></a></li>
				<li<?= $action == "smtp" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/smtp"><i class="icon-wrench"></i> <?= $titles_map['smtp'] ?></a></li>
				<li<?= $action == "ldap" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/ldap"><i class="icon-wrench"></i> <?= $titles_map['ldap'] ?></a></li>
				<li<?= $action == "mongo" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/mongo"><i class="icon-wrench"></i> <?= $titles_map['mongo'] ?></a></li>
				<li<?= $action == "regras" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/regras"><i class="icon-wrench"></i> <?= $titles_map['regras'] ?></a></li>
				<li<?= $action == "backup" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/backup"><i class="icon-wrench"></i> <?= $titles_map['backup'] ?></a></li>
				<li<?= $action == "status" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/status"><i class="icon-wrench"></i> <?= $titles_map['status'] ?></a></li>
				<?php if($role['role'] == 3) { ?> <li<?= $action == "editar" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/editar"><i class="icon-wrench"></i> <?= $titles_map['editar'] ?></a></li><?php } ?>
				<li<?= $action == "apps" ? ' class="active"' : '' ?>><a href="<?= SITE_BASE ?>admin/apps"><i class="icon-wrench"></i> <?= $titles_map['apps'] ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="span9">