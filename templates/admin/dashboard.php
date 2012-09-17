<?php require_once('base-top.php'); ?>
<h2 id="qtdsolic">Quantidade de solicitações</h2>
<div class="row-fluid dashboard">
	<div class="span4">
		<h3>Por status</h3>
		<div class="chart" id="by_status"></div>
	</div>
	<div class="span4">
		<h3>Por tipo</h3>
		<div class="chart" id="by_type"></div>
	</div>
	<div class="span4">
		<h3>Por centro de custo</h3>
		<div class="chart" id="by_centro"></div>
	</div>
</div>

<h2 id="qtdaccess">Acesso ao sistema</h2>
<div class="row-fluid dashboard">
	<div class="span12">
		<h3 id="qtdaccess7d">Histórico dos últimos 7 dias</h3>
		<div class="chart" id="history"></div>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<h3>Acumulado por IP</h3>
			<div class="chart" id="by_ip"></div>
		</div>
		<div class="span6">
			<h3>Acumulado por usuário</h3>
			<div class="chart" id="by_user"></div>
		</div>
	</div>
</div>

<div class="row-fluid">
	<div class="span8">
		<h2>Feedbacks</h2>
		<div class="row-fluid">
			<div class="span6">
				<strong>Satisfação dos usuários</strong>
				<div class="chart" id="gauge"></div>
			</div>
			<div class="span6">
				<strong>Tipos de feedback</strong>
				<div class="chart" id="by_error"></div>
			</div>
		</div>
	</div>
	<div class="span4">
		<h2>Banco de dados</h2>
		<div class="progress">
			<div class="bar" style="width: <?= ($D['stats']['dataSize'] / $D['stats']['storageSize']) * 100 ?>%"><?= $D['stats']['dataSize'] ?> / <?= $D['stats']['storageSize'] ?> MB</div>
		</div>
		<dl class="dl-horizontal">
			<dt>Memória usada</dt><dd><?= $D['stats']['dataSize'] ?> MB</dd>
			<dt>Memória alocada</dt><dd><?= $D['stats']['storageSize'] ?> MB</dd>
			<dt>Coleções</dt><dd><?= $D['stats']['collections'] ?></dd>
			<dt>Objetos</dt><dd><?= $D['stats']['objects'] ?></dd>
			<dt>Tamanho físico</dt><dd><?= $D['stats']['fileSize'] ?> MB</dd>
		</dl>
	</div>
</div>

<!-- Construindo os gráficos com o Google Visualization API -->
<script type="text/javascript">
	google.load('visualization', '1', {packages:['corechart','gauge']});
	google.setOnLoadCallback(drawCharts);
	function drawCharts(){

		by_status();
		by_type();
		by_centro();

		history();
		by_ip();
		by_user();

		gauge();
		by_error();

	}

	/* Quantidade de solicitações */
	// Por status
	function by_status(){
		var data = google.visualization.arrayToDataTable([
			['Status',		'Quantidade'],
			['Pendentes',	<?= $D['by_status']['pendentes'] ?>],
			['Aprovadas',	<?= $D['by_status']['aprovadas'] ?>],
			['Recusadas',	<?= $D['by_status']['recusadas'] ?>],
			['Retornadas',	<?= $D['by_status']['retornadas'] ?>],
			['Em cotação',	<?= $D['by_status']['cotando'] ?>],
			['Em análise',	<?= $D['by_status']['analise'] ?>],
			['Executadas',	<?= $D['by_status']['executadas'] ?>],
			['Canceladas',	<?= $D['by_status']['canceladas'] ?>]
		]);
		var options = {
			is3D: true,
			legend: {position: 'bottom'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('by_status'));
		chart.draw(data, options);

		var total = <?= $D['by_status']['pendentes'] ?> + <?= $D['by_status']['aprovadas'] ?> + <?= $D['by_status']['recusadas'] ?> + <?= $D['by_status']['retornadas'] ?> + <?= $D['by_status']['cotando'] ?> + <?= $D['by_status']['analise'] ?> + <?= $D['by_status']['executadas'] ?> + <?= $D['by_status']['canceladas'] ?>;
		$("#qtdsolic").html('Quantidade de solicitações (Total: ' + total + ')');
	}
	// Por tipo
	function by_type(){
		var data = google.visualization.arrayToDataTable([
			['Tipo',		'Quantidade'],
		<?php
			foreach ($D['by_type'] as $tipo => $qtd) {
				echo "['" . $tipo . "', ". $qtd . "],";
			}
		?>
		]);
		var options = {
			is3D: true,
			legend: {position: 'bottom'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('by_type'));
		chart.draw(data, options);
	}
	// Por centro de custo
	function by_centro(){
		var data = google.visualization.arrayToDataTable([
			['Tipo',		'Quantidade'],
		<?php
			foreach ($D['by_centro'] as $centro => $qtd) {
				echo "['" . $centro . "', ". $qtd . "],";
			}
		?>
		]);
		var options = {
			is3D: true,
			legend: {position: 'bottom'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('by_centro'));
		chart.draw(data, options);
	}

	/* Acesso ao sistema */
	// Histórico
	function history(){
		var data = google.visualization.arrayToDataTable([
		['Dia', 'Acessos'],
			['<?= date("d/m",$D['history']['dt'][6]) ?>',  <?= $D['history']['qt'][6] ?>],
			['<?= date("d/m",$D['history']['dt'][5]) ?>',  <?= $D['history']['qt'][5] ?>],
			['<?= date("d/m",$D['history']['dt'][4]) ?>',  <?= $D['history']['qt'][4] ?>],
			['<?= date("d/m",$D['history']['dt'][3]) ?>',  <?= $D['history']['qt'][3] ?>],
			['<?= date("d/m",$D['history']['dt'][2]) ?>',  <?= $D['history']['qt'][2] ?>],
			['<?= date("d/m",$D['history']['dt'][1]) ?>',  <?= $D['history']['qt'][1] ?>],
			['<?= date("d/m",$D['history']['dt'][0]) ?>',  <?= $D['history']['qt'][0] ?>]
		]);

		var options = {
		legend: {position: 'none'},
		hAxis: {slantedText: true}
		};

		var chart = new google.visualization.LineChart(document.getElementById('history'));
		chart.draw(data, options);

		var total = <?= $D['history']['qt'][6] ?> + <?= $D['history']['qt'][5] ?> + <?= $D['history']['qt'][4] ?> + <?= $D['history']['qt'][3] ?> + <?= $D['history']['qt'][2] ?> + <?= $D['history']['qt'][1] ?> + <?= $D['history']['qt'][0] ?>;
		$("#qtdaccess7d").html('Histórico dos últimos 7 dias (' + total + ' acessos)');
	}
	// Acumulado por IP
	function by_ip(){
		var data = google.visualization.arrayToDataTable([
			['IP',		'Quantidade'],
		<?php
			foreach ($D['by_ip'] as $ip => $qtd) {
				echo "['" . $ip . "', ". $qtd . "],";
			}
		?>
		]);
		var options = {
			is3D: true,
			legend: {position: 'bottom'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('by_ip'));
		chart.draw(data, options);
		var total = 0 <?php foreach ($D['by_ip'] as $qtd) { echo ' + ' . $qtd; } ?>;
		$("#qtdaccess").html('Acessos ao sistema (Total:' + total + ')');
	}
	// Acumulado por usuário
	function by_user(){
		var data = google.visualization.arrayToDataTable([
			['Usuário',		'Quantidade'],
		<?php
			foreach ($D['by_user'] as $usuario => $qtd) {
				echo "['" . $usuario . "', ". $qtd . "],";
			}
		?>
		]);
		var options = {
			is3D: true,
			legend: {position: 'bottom'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('by_user'));
		chart.draw(data, options);
	}

	/* Feedbacks */
	// Satisfação dos usuários
	function gauge() {
		var data = google.visualization.arrayToDataTable([
			['Label', 'Value'],
			['<?= $ratings_map[$D['rating']]['title'] ?>', <?= $D['rating_avg'] ?>],
		]);

		var options = {
			backgroundColor: { fill:'none' },
			min: -2, max: 2,
			redFrom: -2, redTo: -1, redColor: "#FF9600",
			greenFrom:1, greenTo: 2, greenColor: "#0096FF",
		};

		var chart = new google.visualization.Gauge(document.getElementById('gauge'));
		chart.draw(data, options);
	}
	// Tipos de feedback
	function by_error(){
    	var data = google.visualization.arrayToDataTable([
    		['Tipo', 'Total'],
    		['Erro', <?= $D['by_error']['error'] ?>],
    		['Sugestão', <?= $D['by_error']['suggestion'] ?>],
    		['Reclamação', <?= $D['by_error']['complaint'] ?>],
    		['Elogio', <?= $D['by_error']['compliment'] ?>],
    		['Outro', <?= $D['by_error']['other'] ?>]
    	]);
    	var options = {
			is3D: true,
			legend: {position: 'bottom'}
		};

		var chart = new google.visualization.PieChart(document.getElementById('by_error'));
		chart.draw(data, options);
    }

	/* Banco de dados */
</script>

<?php require_once('base-bottom.php'); ?>