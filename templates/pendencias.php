<?php require_once('templates/header.php'); ?>
<div class="row-fluid">
	<div class="span3 hidden-phone hidden-tablet">
		<div class="sidebar-nav">
			<ul class="nav nav-list">
				<li class='nav-header'>Ir para</li>
				<li><a href="#aprovar">Solicitações que preciso aprovar</a></li>
				<li><a href="#executar">Solicitações que preciso executar</a></li>
				<li><a href="#cotar">Solicitações que preciso cotar</a></li>
				<li><a href="#analisar">Solicitações que preciso analisar</a></li>
				<li><a href="#revisar">Solicitações que preciso revisar</a></li>
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
				<li><a href="#aprovar">Solicitações que preciso aprovar</a></li>
				<li><a href="#executar">Solicitações que preciso executar</a></li>
				<li><a href="#cotar">Solicitações que preciso cotar</a></li>
				<li><a href="#analisar">Solicitações que preciso analisar</a></li>
				<li><a href="#revisar">Solicitações que preciso revisar</a></li>
			</ul>
		</div>
	</div>
	<div class="span9">
		<h1>Minhas pendências</h1>

		<!-- Pré-aprovar ou aprovar -->
		<a name="aprovar"></a><h3>Solicitações que preciso aprovar</h3>
		<?php if($aprovar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th width="80">Data</th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="200">Solicitante</th>
			</tr></thead>
			<tbody>
			<?php foreach ($aprovar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td class="hidden-phone"><?= date('d/m/Y',$s->data->sec) ?></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td><?= $s->solicitante['displayname'] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($aprovar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $aprovar_is_first ? ' class="disabled"' : '' ?>><a href="?<?= $aprovar_first_url ?>">«</a></li>
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
				<li<?= $aprovar_is_last ? ' class="disabled"' : '' ?>><a href="?<?= $aprovar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('aprovar',1,$total_aprovar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

		<!-- Executar -->
		<a name="executar"></a><h3>Solicitações que preciso executar</h3>
		<?php if($executar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th class="hidden-phone" width="80">Data</th>
				<th class="hidden-phone">Descrição</th>
				<th width="80">Prazo</th>
			</tr></thead>
			<tbody>
			<?php foreach ($executar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td class="hidden-phone"><?= date('d/m/Y',$s->data->sec) ?></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td><?= $s->prazo_textual ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($executar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $executar_is_first ? ' class="disabled"' : '' ?>><a href="?<?= $executar_first_url ?>">«</a></li>
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
				<li<?= $executar_is_last ? ' class="disabled"' : '' ?>><a href="?<?= $executar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('executar',1,$total_executar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

		<!-- Cotar -->
		<a name="cotar"></a><h3>Solicitações que preciso cotar</h3>
		<?php if($cotar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th width="80">Data</th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="200">Solicitante</th>
			</tr></thead>
			<tbody>
			<?php foreach ($cotar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td><?= date('d/m/Y',$s->data->sec) ?></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td class="hidden-phone"><?= $s->solicitante['displayname'] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($cotar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $cotar_is_first ? ' class="disabled"' : '' ?>><a href="?<?= $cotar_first_url ?>">«</a></li>
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
				<li<?= $cotar_is_last ? ' class="disabled"' : '' ?>><a href="?<?= $cotar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('cotar',1,$total_cotar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

		<!-- Análisar -->
		<a name="analisar"></a><h3>Solicitações que preciso analisar</h3>
		<?php if($analisar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th width="80">Data</th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="200">Solicitante</th>
			</tr></thead>
			<tbody>
			<?php foreach ($analisar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td><?= date('d/m/Y',$s->data->sec) ?></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td class="hidden-phone"><?= $s->solicitante['displayname'] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($analisar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $analisar_is_first ? ' class="disabled"' : '' ?>><a href="?<?= $analisar_first_url ?>">«</a></li>
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
				<li<?= $analisar_is_last ? ' class="disabled"' : '' ?>><a href="?<?= $analisar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('analisar',1,$total_analisar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

		<!-- Revisar -->
		<a name="revisar"></a><h3>Solicitações que preciso revisar</h3>
		<?php if($revisar->count(true) > 0){ ?>
		<table class="table table-striped" width='100%'>
			<thead><tr>
				<th width="220">Solicitação</th>
				<th width="80">Data</th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="200">Aprovador</th>
			</tr></thead>
			<tbody>
			<?php foreach ($revisar as $item) { $s = new Solicitacao($item['_id']); ?>
			<tr>
				<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
				<td><?= date('d/m/Y',$s->data->sec) ?></td>
				<td class="hidden-phone"><?= $s->descricao ?></td>
				<td class="hidden-phone"><?= $s->pre_aprovado ? $s->aprovador['displayname'] : $s->pre_aprovador['displayname'] ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
		<?php if($revisar_pages > 1) { ?>
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $revisar_is_first ? ' class="disabled"' : '' ?>><a href="?<?= $revisar_first_url ?>">«</a></li>
				<?php
				for($i=1; $i<=$revisar_pages;$i++){
					if($i == 1 || $i == $revisar_pages || ($i >= $revisar_page -2 && $i <= $revisar_page +2)){
					$revisar_is_current = $revisar_page == $i;
				?>
				<li<?= $revisar_is_current ? ' class="active"' : '' ?>><a href="?<?= makePageUrl('revisar',$i) ?>"><?= $i ?></a></li>
				<?php 
					} else {
						echo '<li class="disabled"><a href="#">...</a></li>';
						if($i < $revisar_page){ 
							$i = $revisar_page -3; 
						}elseif($i > $revisar_page){
							$i = $revisar_pages - 1;
						}
					}
				}
				?>
				<li<?= $revisar_is_last ? ' class="disabled"' : '' ?>><a href="?<?= $revisar_last_url ?>">»</a></li>
				<li><a href="?<?= makePageUrl('revisar',1,$total_revisar) ?>">Mostrar tudo</a></li>
			</ul>
		</div>
		<?php } ?>
		<?php }else{ echo '<p>Nenhuma solicitação.</p>'; } ?>

	</div>
</div>

<?php require_once('templates/footer.php'); ?>