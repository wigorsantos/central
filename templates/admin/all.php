<?php require_once('base-top.php'); ?>
<select id="filter" onchange="window.location = '<?= SITE_BASE ?>admin/all?status='+this.value;">
	<option value="todas" <?= $D['s'] == -1 ? "selected" : "" ?>>Tudo</option>
	<option value="rascunhos" <?= $D['s'] == 0 ? "selected" : "" ?>>Rascunhos</option>
	<option value="pendentes" <?= $D['s'] == 1 ? "selected" : "" ?>>Pendentes</option>
	<option value="cotando" <?= $D['s'] == 5 ? "selected" : "" ?>>Em cotação</option>
	<option value="analisando" <?= $D['s'] == 6 ? "selected" : "" ?>>Em análise</option>
	<option value="retornadas" <?= $D['s'] == 4 ? "selected" : "" ?>>Retornadas</option>
	<option value="aprovadas" <?= $D['s'] == 2 ? "selected" : "" ?>>Aprovadas</option>
	<option value="recusadas" <?= $D['s'] == 3 ? "selected" : "" ?>>Recusadas</option>
	<option value="executadas" <?= $D['s'] == 7 ? "selected" : "" ?>>Executadas</option>
	<option value="canceladas" <?= $D['s'] == 8 ? "selected" : "" ?>>Canceladas</option>
</select>
<div class="table">
	<?php
		if($D['total'] > 0){
	?>
	<table width='100%' cellpadding='2' cellspacing='2' border='0'>
		<thead>
			<tr>
				<th width="300">Solicitação</th>
				<th width="80"><span class="hidden-phone">Data</span><span class="visible-phone">Info</span></th>
				<th class="hidden-phone">Descrição</th>
				<th class="hidden-phone" width="100">Solicitante</th>
				<th class="hidden-phone" width="80">Status</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			foreach ($D['list'] as $item) {
				try {
					$s = new Solicitacao($item['_id']);
		?>
		<tr>
			<td><a href="<?= SITE_BASE . $s->id ?>"><div class="icon icon-<?= $s->status[1] ?>"></div><?= $s->tipo['nome'] ?> #<?= $s->numero ?></a></td>
			<td><?= date('d/m/Y',$s->data->sec) ?><div class="visible-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></div></td>
			<td class="hidden-phone"><?= $s->descricao ?></td>
			<td class="hidden-phone"><?= $s->solicitante['displayname'] ?></td>
			<td class="hidden-phone status_<?= $s->status[1] ?>"><?= $verbal_status[$s->status[0]] ?></td>
		</tr>
		<?php
				} catch(Exception $e){ echo '<tr><td colspan="5"><i class="icon-warning-sign"></i> A solicitação com id ' . $item['_id'] . ' não é válida.</td></tr>'; }
			}
		?>
		</tbody>
	</table>
	<?php 
		if($D['pages'] > 1) { 
			$is_first = $D['page'] == 1;
			$is_last = $D['page'] == $D['pages'];
			$first_url = $is_first ? '#' : '?p=' . ($D['page'] - 1) . (isset($_GET['page_size']) ? "&page_size=" . $_GET['page_size'] : "");
			$last_url = $is_last ? '#' : '?p=' . ($D['page'] + 1) . (isset($_GET['page_size']) ? "&page_size=" . $_GET['page_size'] : "");
		 ?> 
		<div class="pagination pagination-centered">
			<ul>
				<li<?= $is_first == 1 ? ' class="disabled"' : '' ?>><a href="<?= $first_url ?>">«</a></li>
				<?php 
				for($i=1; $i<=$D['pages']; $i++){ 
					if($i == 1 || $i == $D['pages'] || ($i >= $D['page'] - 2 && $i <= $D['page'] + 2)){
					$url = '?p=' . $i . (isset($_GET['page_size']) ? "&page_size=" . $_GET['page_size'] : "");
					$is_current = $D['page'] == $i;
				?>
				<li<?= $is_current ? ' class="active"' : '' ?>><a href="<?= $url ?>"><?= $i ?></a></li>
				<?php 
					} else {
						echo '<li class="disabled"><a href="#">...</a></li>';
						if($i < $D['page']) {
							$i = $D['page'] - 3;
						} elseif($i > $D['page']) {
							$i = $D['pages'] - 1;
						}
					}
				} 
				?>
				<li<?= $is_last == 1 ? ' class="disabled"' : '' ?>><a href="<?= $last_url ?>">»</a></li>
			</ul>
		</div>
		<?php  }
	}else {
		echo 'Não existem solicitações cadastradas';
	} ?>
</div>
<?php require_once('base-bottom.php'); ?>