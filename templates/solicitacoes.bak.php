<?php require_once('templates/header.php');?>
<div class="row-fluid">
	<div class="span3 hidden-phone hidden-tablet">
		<div class="well sidebar-nav">
			<ul class="nav nav-list">
				<li class='nav-header'>Mostrar</li>
				<li<?= $s == -1 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>todas">Tudo</a></li>
				<li<?= $s == 0 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>rascunhos">Rascunhos</a></li>
				<li<?= $s == 1 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>pendentes">Pendentes</a></li>
				<li<?= $s == 5 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>cotando">Em cotação</a></li>
				<li<?= $s == 6 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>analisando">Em análise</a></li>
				<li<?= $s == 4 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>retornadas">Retornadas</a></li>
				<li<?= $s == 2 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>aprovadas">Aprovadas</a></li>
				<li<?= $s == 3 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>recusadas">Recusadas</a></li>
				<li<?= $s == 7 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>executadas">Executadas</a></li>
				<li<?= $s == 8 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>canceladas">Canceladas</a></li>
			</select>
		</div>
	</div>
	<div class="row-fluid visible-phone visible-tablet">
		<div class="btn-group pull-right">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="icon-filter"></i>
				Mostrar <?= $status == 'all' ? 'tudo' : 'apenas ' . $status ?>
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li<?= $s == -1 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>todas">Tudo</a></li>
				<li<?= $s == 0 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>rascunhos">Rascunhos</a></li>
				<li<?= $s == 1 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>pendentes">Pendentes</a></li>
				<li<?= $s == 5 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>cotando">Em cotação</a></li>
				<li<?= $s == 6 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>analisando">Em análise</a></li>
				<li<?= $s == 4 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>retornadas">Retornadas</a></li>
				<li<?= $s == 2 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>aprovadas">Aprovadas</a></li>
				<li<?= $s == 3 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>recusadas">Recusadas</a></li>
				<li<?= $s == 7 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>executadas">Executadas</a></li>
				<li<?= $s == 8 ? " class='active'" : "" ?>><a href="<?= SITE_BASE ?>canceladas">Canceladas</a></li>
			</ul>
		</div>
	</div>
	<div class="span9">
		
		<h1><?= $_TITLE ?></h1>
		<?php
			if($total > 0){
		?>
		<table width="100%" class="table table-striped">
			<thead>
				<tr>
					<th width="300">Solicitação</th>
					<th width="80">Data</th>
					<th>Descrição</th>
					<th width="80">Status</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				foreach ($list as $item) {
					$s = new Solicitacao($item['_id']);
			?>
			
				<tr>
					<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
					<td><?= date('d/m/Y',$s->data->sec) ?></td>
					<td><?= $s->descricao ?></td>
					<td class="status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></td>
				</tr>
			
			<?php
				}
			?>
			</tbody>
		</table>
		<?php if($pages > 1){ 
			$is_first = $page == 1;
			$is_last = $page == $pages;
			$first_url = '?p=' . ($page - 1) . (isset($_GET['page_size']) ? "&page_size=" . $_GET['page_size'] : "");
			$last_url = '?p=' . ($page + 1) . (isset($_GET['page_size']) ? "&page_size=" . $_GET['page_size'] : "");
		?>
			<div class="pagination pagination-centered">
				<ul>
					<li<?= $is_first == 1 ? ' class="disabled"' : '' ?>><a href="<?= $first_url ?>">«</a></li>
					<?php 
					for($i=1; $i<=$pages; $i++){ 
						$url = '?p=' . $i . (isset($_GET['page_size']) ? "&page_size=" . $_GET['page_size'] : "");
						$is_current = $page == $i;
					?>
					<li<?= $is_current ? ' class="active"' : '' ?>><a href="<?= $url ?>"><?= $i ?></a></li>
					<?php } ?>
					<li<?= $is_last == 1 ? ' class="disabled"' : '' ?>><a href="<?= $last_url ?>">»</a></li>
				</ul>
			</div>
		<?php }
	} else {
			if($s == -1){
				echo '<p><i class="icon-warning-sign"></i> Você ainda não fez uma solicitação. Clique <a href="' . SITE_BASE. 'nova">aqui</a> e faça a primeira!</p>';
			}else{
				echo '<p><i class="icon-warning-sign"></i> Você não possui uma solicitação ' . strtolower($verbal_status[$s]) ." para exibir.</p>";
			}
		} ?>
	</div>
</div>
<?php require_once('templates/footer.php'); ?>