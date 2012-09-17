<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>
<form method="post" id="form" class="form-horizontal">
	<h2>Informações básicas da solicitação</h2>
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="descricao">Descrição</label>
			<div class="controls">
				<input class="input-xlarge" name="descricao" id="descricao" type="text" value="<?= $s->descricao ?>">
				<span class="help-inline" id="descricao-tip">Descreva brevemente a que se refere essa solicitação (50 caracteres).</span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="prazo">Prazo</label>
			<div class="controls">
				<input class="input-xlarge" name="prazo" id="prazo" type="datetime" value="<?= $s->prazo ?>">
				<span class="help-inline" id="descricao-tip">Preencha se essa solicitação possui uma data limite para ser executada.</span>
			</div>
		</div>
		<div class="control-group" id="cg_centro">
			<label class="control-label" for="centro">Centro de custos</label>
			<div class="controls">
				<input type="hidden" name="centro" id="centro" value="<?= $s->centro['apelido'] ?>">
				<div class="input-prepend">
					<div class="add-on" id="centro-view">#<?= $s->centro['apelido'] ?></div><input type="text" class="input-xlarge" name="centro-ahead" id="centro-ahead" data-provide="typeahead" data-items="6" data-source='[<?php
							$centros = DB::$centro->find()->sort(array('apelido' => 1));
							$i = 0;
							foreach ($centros as $c) {
								if($i > 0){
									echo ',';
								}
								echo '"'. $c["apelido"] . ' - ' . $c["descricao"] . '"';
								$i++;
							}
						?>]'>
						<span class="help-inline">Selecione o centro de custos referente a essa solicitação.</span>
				</div>
			</div>
		</div>
	</fieldset>
	<hr>
	<h2>Detalhes da solicitação</h2>
<?php
	$t = $s->tipo;
?>
<table class="table-datasheet" id="details">
	<thead><tr>
	<?php
	foreach ($t['detalhe'] as $tc) {
		echo '<th>' . $tc['nome'] . '<small>' . $tc['dica'] . '</small></th>';
	}
	?>
		<th width="90"></th>
	</tr></thead>
	<tbody>
	<?php 
	$d = $s->detalhes;
	$c = 0;
	foreach ($d as $di) {
		echo '<tr>';
		echo '<input type="hidden" name="item[' . $c . '][id]" value="' . $di['_id'] . '"/>';
		foreach ($t['detalhe'] as $tc) {
			echo '<td><input name="item[' . $c . '][' . $tc['nome_unico'] . ']" type="' . ($tc['tipo'] != 'money' ? $tc['tipo'] : 'number') . '" data-type="' . $tc['tipo'] . '" title="' . $tc['dica'] . '" value="' . $di[$tc['nome_unico']] . '" /></td>';
		}
		echo '<td><label type="checkbox"><input type="checkbox" name="item[' . $c . '][remove]"> Remover</label></td></tr>';
		$c++;
	}
	?>
	</tbody>
</table>
<a class="btn" href="javascript:addRow()"><i class="icon-plus"></i> Adicionar item</a>
<hr>
<span id="buttons">
	<button class="btn btn-primary"  type="submit">Salvar</button>
	<button class="btn" type="button" onclick="window.location = '<?= SITE_BASE . $s->id ?>'">Cancelar</button>
</span>
</form>
<script type="text/javascript">
	var rCount = 0;
	var c = <?= $c ?>;
	function addRow(){
		var template = "<tr>";
	<?php
	foreach ($t['detalhe'] as $tc) {
		echo 'template += \'<td><input name="item[__c__][' . $tc['nome_unico'] . ']" type="' . ($tc['tipo'] != 'money' ? $tc['tipo'] : 'number') . '" data-type="' . $tc['tipo'] . '" title="' . $tc['dica'] . '" /></td>\';';
	}
	?>
		template += "<td><a class='btn btn-danger btn-mini' id='remove-" + rCount + "' href='#' onclick='removeRow(this.id);return false;'>Remover</a></td></tr>";
		rCount++;
		while (template.indexOf('__c__') != -1) {
	 		template = template.replace('__c__', c);
		}
		c++;
		$("#details tbody").append(template);
	}
	function removeRow(child){
		if($("#details").find("tr").length > 2){
			$("#" + child).parent().parent().remove();
			return true;
		}else{
			alert("É necessário pelo menos um detalhe na solicitação.");
			return false;
		}
	}
	
	// Seletor de centro de custo
	var centros_validos = [<?php
								$centros = DB::$centro->find()->sort(array('apelido' => 1));
								$i = 0;
								foreach ($centros as $c) {
									if($i > 0){
										echo ',';
									}
									echo $c["apelido"];
									$i++;
								}
							?>];
	$("#centro-ahead").change(function(){
		var c = $(this).val().split(" - ");
		var cn = Number(c[0]);
		var valido = $.inArray(cn,centros_validos) > -1;
		if(valido){
			$("#cg_centro").removeClass("error");
			$("#buttons button.btn-primary").attr("disabled", false);
			$("#centro-view").html("#"+cn);
			$("#centro").val(cn);
		}else{
			$("#cg_centro").addClass("error");
			$("#buttons button.btn-primary").attr("disabled", true);
		}
	})

	// Campo de descrição
	$("#descricao").keyup(function(){
		var MAXCHARS = 50;
		if($("#descricao").val().length > MAXCHARS){
			$("#descricao").val($("#descricao").val().substring(0,MAXCHARS));
		}
		$("#descricao-tip").html("Descreva brevemente a que se refere essa solicitação (" + (MAXCHARS - $("#descricao").val().length) + " caracteres).")
	});
	
</script>
<?php require_once('templates/footer.php'); ?>
