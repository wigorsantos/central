<?php require_once('base-top.php'); ?>
<p>
	<div class="feedback-legend feedback-legend-error">Erro</div>
	<div class="feedback-legend feedback-legend-suggestion">Sugestão</div>
	<div class="feedback-legend feedback-legend-complaint">Reclamação</div>
	<div class="feedback-legend feedback-legend-compliment">Elogio</div>
	<div class="feedback-legend feedback-legend-other">Outro</div>
</p>
<?php 
if($D['total'] > 0) {
	foreach($D['list'] as $f) { $uinfo = AD::info($f['user'],array('displayname')); ?>
	<blockquote class="<?= $f['type'] ?>">
		<p><?= isset($f['message']) && !empty($f['message']) ? $f['message'] : "Sem mensagem" ?></p>
		<small>
			Por <?= $uinfo[0]['displayname'][0] ?> em <?= date('d/m/Y H:i',$f['data']->sec) ?>.
			<?php if(isset($f['rating'])) { $rating = $f['rating'] > 0 ? '+' . $f['rating'] : '' . $f['rating']; ?>
				<strong><img src="<?= SITE_BASE ?>res/img/emot<?= $rating ?>.png" class="smiley"><?= $ratings_map[$f['rating']]['title'] ?></strong>
			<?php } ?>
		</small>
	</blockquote>
	<?php }
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
	<?php } ?>

<?php } else { ?>
	<h2>Que pena...</h2>
	<p>Ninguém enviou um feedback ainda.</p>
<?php } ?>

<?php require_once('base-bottom.php'); ?>