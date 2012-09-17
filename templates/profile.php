<?php require_once('templates/header.php'); ?>
<div class="row-fluid">
	<div class="span12">
	<h1><?= $uinfo['displayname']; ?></h1>
	<p>Aqui você pode atualizar as suas informações e conferir uma visão geral da sua relação com a Central de Solicitações.</p>
		<div class="row-fluid">
			<div class="span6 col1-bordered">
				<div style="margin:10px;">
					<h3>Minhas solicitações</h3>
					<table class="table-condensed cpanel-box" width="100%">
						<tr>
							<td width="50%">
								<dl class="dl-horizontal">
									<dt>Total</dt><dd><?= $solicitacoes['total'] ?></dd>
									<dt>Rascunhos</dt><dd><?= $solicitacoes['rascunho'] ?></dd>
									<dt>Pendentes</dt><dd><?= $solicitacoes['pendente'] ?></dd>
									<dt>Aprovadas</dt><dd><?= $solicitacoes['aprovada'] ?></dd>
									<dt>Recusadas</dt><dd><?= $solicitacoes['recusada'] ?></dd>
									<dt>Retornadas</dt><dd><?= $solicitacoes['retornada'] ?></dd>
									<dt>Em cotação</dt><dd><?= $solicitacoes['cotando'] ?></dd>
									<dt>Em análise</dt><dd><?= $solicitacoes['analise'] ?></dd>
									<dt>Executadas</dt><dd><?= $solicitacoes['executada'] ?></dd>
									<dt>Canceladas</dt><dd><?= $solicitacoes['cancelada'] ?></dd>
								</dl>
							</td>
							<td width="50%"><div id="pie-solicitacoes"></div>
							</td>
						</tr>
					</table>
					
					<h3>Meus acessos</h3>
					<table class="table-condensed cpanel-box" width="100%">
						<tr>
							<td width="50%">
								<strong><?= $acessos ?> no total (<?= $acessos_6 + $acessos_5 + $acessos_4 + $acessos_3 + $acessos_2 + $acessos_1 + $acessos_0 ?> em 7 dias)</strong>
								<div id="line-acessos"></div>
							</td>
							<td width="50%">
								<strong>IPs utilizados</strong>
								<div id="pie-acessos"></div>
							</td>
						</tr>
					</table>
					
					<h3>Meus feedbacks e satisfação</h3>
					<table class="table-condensed cpanel-box" width="100%">
						<tr>
							<td width="50%">
								<p>Atualmente a minha satisfação é</p>
								<p><label class="radio">
									<strong><img src="<?= SITE_BASE ?>res/img/emot<?= $rating > 0 ? '+' . $rating : $rating ?>.png" class="smiley" /><?= $rating_map[$rating]['title'] ?></strong>
									<span class="help-block"><?= $rating_map[$rating]['desc'] ?></span>
									<a href="<?= SITE_BASE ?>feedback" class="btn btn-small btn-primary">Enviar feedback</a>
								</label></p>

							</td>
							<td width="50%">
								<div id="pie-feedback"></div>
							</td>
						</tr>
					</table>

				</div>
			</div>
			<hr class="visible-phone">
			<div class="span6">
				<div style="margin:10px;">
				<form method="post">
					<h3>Atualize as suas informações</h3>
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
						<button class="btn btn-primary" type="submit">Salvar</button>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Google Visualization -->
<script type="text/javascript">
	google.load('visualization', '1', {packages:['corechart','gauge']});
	google.setOnLoadCallback(drawCharts);
	function drawCharts(){
		pieSolicitacoes();
		lineAcessos();
		pieAcessos();
		pieFeedback();
	}

	function pieSolicitacoes(){
		var data = google.visualization.arrayToDataTable([
			['Status',		'Quantidade'],
			['Rascunhos',	<?= $solicitacoes['rascunho'] ?>],
			['Pendentes',	<?= $solicitacoes['pendente'] ?>],
			['Aprovadas',	<?= $solicitacoes['aprovada'] ?>],
			['Recusadas',	<?= $solicitacoes['recusada'] ?>],
			['Retornadas',	<?= $solicitacoes['retornada'] ?>],
			['Em cotação',	<?= $solicitacoes['cotando'] ?>],
			['Em análise',	<?= $solicitacoes['analise'] ?>],
			['Executadas',	<?= $solicitacoes['executada'] ?>],
			['Canceladas',	<?= $solicitacoes['cancelada'] ?>]
		]);

		var options = {
			is3D: true,
			legend: {position: 'none'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('pie-solicitacoes'));
		chart.draw(data, options);
	}
	function lineAcessos() {
		var data = google.visualization.arrayToDataTable([
			['Dia', 'Acessos'],
			['<?= date("d/m",$dt_6) ?>',  <?= $acessos_6 ?>],
			['<?= date("d/m",$dt_5) ?>',  <?= $acessos_5 ?>],
			['<?= date("d/m",$dt_4) ?>',  <?= $acessos_4 ?>],
			['<?= date("d/m",$dt_3) ?>',  <?= $acessos_3 ?>],
			['<?= date("d/m",$dt_2) ?>',  <?= $acessos_2 ?>],
			['<?= date("d/m",$dt_1) ?>',  <?= $acessos_1 ?>],
			['<?= date("d/m",$dt_0) ?>',  <?= $acessos_0 ?>]
		]);

		var options = {
			legend: {position: 'none'},
			hAxis: {slantedText: true}
		};

		var chart = new google.visualization.LineChart(document.getElementById('line-acessos'));
		chart.draw(data, options);
	}
	function pieAcessos() {
		var data = google.visualization.arrayToDataTable([
			['IP', 'Total'],
		<?php
			foreach ($total_ips as $ip => $total) {
		?>
			['<?= $ip ?>', <?= $total ?>],
		<?php
			}
		?>
		]);

		var options = {
			is3D: true,
			legend: {position: 'right'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('pie-acessos'));
		chart.draw(data, options);
    }
    function pieFeedback(){
    	var data = google.visualization.arrayToDataTable([
    		['Tipo', 'Total'],
    		['Erro', <?= $feedback_counts['error'] ?>],
    		['Sugestão', <?= $feedback_counts['suggestion'] ?>],
    		['Reclamação', <?= $feedback_counts['complaint'] ?>],
    		['Elogio', <?= $feedback_counts['compliment'] ?>],
    		['Outro', <?= $feedback_counts['other'] ?>]
    	]);
    	var options = {
			is3D: true,
			legend: {position: 'bottom'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('pie-feedback'));
		chart.draw(data, options);
    }
</script>

<?php require_once('templates/footer.php'); ?>