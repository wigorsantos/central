<?php require_once(dirname(__FILE__) . '/header.php'); ?>

<div class="row-fluid">
	<div class="span3 hidden-phone hidden-tablet">
		<div class="sidebar-nav">
			<ul class="nav nav-list">
				<li class='nav-header'>Ir para</li>
				<li><a href="#aprovar">Solicitações que aprovei</a></li>
				<li><a href="#executar">Solicitações que executei</a></li>
				<li><a href="#cotar">Solicitações que cotei</a></li>
				<li><a href="#analisar">Solicitações que analisei</a></li>
				<li><a href="#informado">Solicitações que sou informado</a></li>
			</ul>
		</div>
	</div>
	<div class="row-fluid visible-phone visible-tablet">
		<div class="btn-group pull-right">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="icon-filter"></i>
				Ir para
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="#aprovar">Solicitações que aprovei</a></li>
				<li><a href="#executar">Solicitações que executei</a></li>
				<li><a href="#cotar">Solicitações que cotei</a></li>
				<li><a href="#analisar">Solicitações que analisei</a></li>
				<li><a href="#informado">Solicitações que sou informado</a></li>
			</ul>
		</div>
	</div>
	<div class="span9">
		<h1><?= $_TITLE ?></h1>
		<!-- Pré-aprovar ou aprovar -->
		<a name="aprovar"></a><h3>Solicitações que aprovei</h3>
		<?php if($aprovar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th width="80"><span class="hidden-phone">Data</span><span class="visible-phone">Info</span></th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="200">Solicitante</th>
				<th class="hidden-phone" width="80">Status</th>
			</tr></thead>
			<tbody>
			<?php foreach ($aprovar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td><?= date('d/m/Y',$s->data->sec) ?><div class="visible-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></div></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td class="hidden-phone"><?= $s->solicitante['displayname'] ?></td>
				<td class="hidden-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($aprovar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $aprovar_is_first ? ' class="disabled"' : '' ?>><a href="<?= $aprovar_first_url ?>">«</a></li>
				<?php
				for($i=1; $i<=$aprovar_pages;$i++){
					if($i == 1 || $i == $aprovar_pages || ($i >= $aprovar_page -2 && $i <= $aprovar_page +2)){
					$aprovar_is_current = $aprovar_page == $i;
				?>
				<li<?= $aprovar_is_current ? ' class="active"' : '' ?>><a href="?<?= makePageUrl('aprovar',$i) ?>"><?= $i ?></a></li>
				<?php 
					} else {
						echo '<li class="disabled"><a href="#">...</a></li>';
						if($i < $aprovar_page){ 
							$i = $aprovar_page -3; 
						}elseif($i > $aprovar_page){
							$i = $aprovar_pages - 1;
						}
					}
				}
				?>
				<li<?= $aprovar_is_last ? ' class="disabled"' : '' ?>><a href="<?= $aprovar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('aprovar',1,$total_aprovar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

		<!-- Executar -->
		<a name="executar"></a><h3>Solicitações que executei</h3>
		<?php if($executar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th class="hidden-phone" width="80">Data</th>
				<th class="hidden-phone">Descrição</th>
				<th width="80">Prazo</th>
				<th class="hidden-phone" width="80">Status</th>
			</tr></thead>
			<tbody>
			<?php foreach ($executar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td class="hidden-phone"><?= date('d/m/Y',$s->data->sec) ?></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td><?= $s->prazo_textual ?></td>
				<td class="hidden-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($executar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $executar_is_first ? ' class="disabled"' : '' ?>><a href="<?= $executar_first_url ?>">«</a></li>
				<?php
				for($i=1; $i<=$executar_pages;$i++){
					if($i == 1 || $i == $executar_pages || ($i >= $executar_page -2 && $i <= $executar_page +2)){
					$executar_is_current = $executar_page == $i;
				?>
				<li<?= $executar_is_current ? ' class="active"' : '' ?>><a href="?<?= makePageUrl('executar',$i) ?>"><?= $i ?></a></li>
				<?php 
					} else {
						echo '<li class="disabled"><a href="#">...</a></li>';
						if($i < $executar_page){ 
							$i = $executar_page -3; 
						}elseif($i > $executar_page){
							$i = $executar_pages - 1;
						}
					}
				}
				?>
				<li<?= $executar_is_last ? ' class="disabled"' : '' ?>><a href="<?= $executar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('executar',1,$total_executar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

		<!-- Cotar -->
		<a name="cotar"></a><h3>Solicitações que cotei</h3>
		<?php if($cotar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th width="80"><span class="hidden-phone">Data</span><span class="visible-phone">Info</span></th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="200">Solicitante</th>
				<th class="hidden-phone" width="80">Status</th>
			</tr></thead>
			<tbody>
			<?php foreach ($cotar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td><?= date('d/m/Y',$s->data->sec) ?><div class="visible-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></div></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td class="hidden-phone"><?= $s->solicitante['displayname'] ?></td>
				<td class="hidden-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($cotar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $cotar_is_first ? ' class="disabled"' : '' ?>><a href="<?= $cotar_first_url ?>">«</a></li>
				<?php
				for($i=1; $i<=$cotar_pages;$i++){
					if($i == 1 || $i == $cotar_pages || ($i >= $cotar_page -2 && $i <= $cotar_page +2)){
					$cotar_is_current = $cotar_page == $i;
				?>
				<li<?= $cotar_is_current ? ' class="active"' : '' ?>><a href="?<?= makePageUrl('cotar',$i) ?>"><?= $i ?></a></li>
				<?php 
					} else {
						echo '<li class="disabled"><a href="#">...</a></li>';
						if($i < $cotar_page){ 
							$i = $cotar_page -3; 
						}elseif($i > $cotar_page){
							$i = $cotar_pages - 1;
						}
					}
				}
				?>
				<li<?= $cotar_is_last ? ' class="disabled"' : '' ?>><a href="<?= $cotar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('cotar',1,$total_cotar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

		<!-- Análisar -->
		<a name="analisar"></a><h3>Solicitações que analisei</h3>
		<?php if($analisar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th width="80"><span class="hidden-phone">Data</span><span class="visible-phone">Info</span></th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="200">Solicitante</th>
				<th class="hidden-phone" width="80">Status</th>
			</tr></thead>
			<tbody>
			<?php foreach ($analisar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td><?= date('d/m/Y',$s->data->sec) ?><div class="visible-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></div></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td class="hidden-phone"><?= $s->solicitante['displayname'] ?></td>
				<td class="hidden-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($analisar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $analisar_is_first ? ' class="disabled"' : '' ?>><a href="<?= $analisar_first_url ?>">«</a></li>
				<?php
				for($i=1; $i<=$analisar_pages;$i++){
					if($i == 1 || $i == $analisar_pages || ($i >= $analisar_page -2 && $i <= $analisar_page +2)){
					$analisar_is_current = $analisar_page == $i;
				?>
				<li<?= $analisar_is_current ? ' class="active"' : '' ?>><a href="?<?= makePageUrl('analisar',$i) ?>"><?= $i ?></a></li>
				<?php 
					} else {
						echo '<li class="disabled"><a href="#">...</a></li>';
						if($i < $analisar_page){ 
							$i = $analisar_page -3; 
						}elseif($i > $analisar_page){
							$i = $analisar_pages - 1;
						}
					}
				}
				?>
				<li<?= $analisar_is_last ? ' class="disabled"' : '' ?>><a href="<?= $analisar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('analisar',1,$total_analisar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

		<!-- Informado -->
		<a name="informado"></a><h3>Solicitações que sou informado</h3>
		<?php if($informado->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th width="80"><span class="hidden-phone">Data</span><span class="visible-phone">Info</span></th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="200">Solicitante</th>
				<th class="hidden-phone" width="80">Status</th>
			</tr></thead>
			<tbody>
			<?php foreach ($informado as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td><?= date('d/m/Y',$s->data->sec) ?><div class="visible-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></div></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td class="hidden-phone"><?= $s->solicitante['displayname'] ?></td>
				<td class="hidden-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($informado_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $informado_is_first ? ' class="disabled"' : '' ?>><a href="<?= $informado_first_url ?>">«</a></li>
				<?php
				for($i=1; $i<=$informado_pages;$i++){
					if($i == 1 || $i == $informado_pages || ($i >= $informado_page -2 && $i <= $informado_page +2)){
					$informado_is_current = $informado_page == $i;
				?>
				<li<?= $informado_is_current ? ' class="active"' : '' ?>><a href="?<?= makePageUrl('informado',$i) ?>"><?= $i ?></a></li>
				<?php 
					} else {
						echo '<li class="disabled"><a href="#">...</a></li>';
						if($i < $informado_page){ 
							$i = $informado_page -3; 
						}elseif($i > $informado_page){
							$i = $informado_pages - 1;
						}
					}
				}
				?>
				<li<?= $informado_is_last ? ' class="disabled"' : '' ?>><a href="<?= $informado_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('informado',1,$total_informado) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

	</div>
</div>
<?php require_once(dirname(__FILE__) . '/footer.php'); ?>