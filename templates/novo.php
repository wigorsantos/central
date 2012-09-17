<?php require_once('templates/header.php'); ?>
<h1>Nova solicitação</h1>
<form method="post" id="form">
	Siga os passos a seguir para fazer uma nova solicitação.
	<div class="row-fluid">
		<div id="step1" class="span6">
			<h2>1. Selecione o tipo de solicitação</h2>
			<?php
				if(DB::$tipo->count(null,1) == 0){
					echo "<strong>Opss!</strong><p>Não existe nenhum tipo de solicitação cadastrado, contate o administrador.";
				}else{
					echo '<table class="table table-condensed table-striped" width="100%" cellspacing="2" cellpadding="0" border="0">';
					$tipos = DB::$tipo->find()->sort(array('nome' => 1));
					foreach ($tipos as $t) {
			?>
				<tr>
					<td style="vertical-align:middle;text-align:center;"><input type="radio" name="tipo" id="tipo-<?= $t['_id'] ?>" value="<?= $t['_id'] ?>"></td>
					<td><label for="tipo-<?= $t['_id'] ?>"><strong><?= $t['nome'] ?></strong></label><?= $t['descricao'] ?></td>
				</tr>
			<?php } echo "</table>"; } ?>
		</div>
		<hr class="visible-phone">
		<div id="step2" class="span6 col2-bordered">
			<h2>2. Preencha as informações básicas da solicitação</h2>
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="descricao">Descrição</label>
					<div class="controls">
						<input class="input-xlarge" name="descricao" id="descricao" type="text">
						<span class="help-inline" id="descricao-tip">Descreva brevemente a que se refere essa solicitação (50 caracteres).</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="prazo">Prazo</label>
					<div class="controls">
						<input class="input-xlarge" name="prazo" id="prazo" type="datetime">
						<span class="help-inline" id="descricao-tip">Preencha se essa solicitação possui uma data limite para ser executada.</span>
					</div>
				</div>
				<div class="control-group" id="cg_centro">
					<label class="control-label" for="centro">Centro de custos</label>
					<div class="controls">
						<input type="hidden" name="centro" id="centro">
						<div class="input-prepend">
							<div class="add-on" id="centro-view">#</div><input type="text" class="input-xlarge" name="centro-ahead" id="centro-ahead" data-provide="typeahead" data-items="6" data-source='[<?php
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
		</div>
		<div class="clear"></div>
		<hr>
		<div class="row-fluid">
			<div id="step3">
				<h2>3. Informe os detalhes da solicitação</h2>
				<div id="details-form-part"><p>Selecione um tipo de solicitação para inserir itens.</p></div>
			</div>
			<hr>
			<p>Você poderá visualizar e editar a solicitação antes de enviá-la para aprovação.</p>
			<span id="buttons">
				<button class="btn btn-primary" disabled type="submit">Salvar rascunho</button> 
				<button class="btn" type="button" onclick="window.location = '<?= SITE_BASE ?>';">Cancelar</button>
			</span>
		</div>
	</div>
</form>

<script type="text/javascript">
	
	$(document).ready(function(){
		// INICIALIZAÇÃO
		$("#step2 input").attr("disabled",true);

		// AÇÔES DOS CAMPOS DO FORMULÁRIO

		// Tipos de solicitação
		$("#step1 input:radio").click(function(){
			$("#details-form-part").html('<div class="bar" style="width:100%">Carregando...</div>').addClass("progress").addClass("progress-striped").addClass("active");
			var tipo_id = $("#step1 input:radio:checked").val();
			$("#details-form-part").load("?detailOf=" + tipo_id, function(){
				$("#details-form-part").removeClass("progress").removeClass("progress-striped").removeClass("active");
				$("#buttons button").attr("disabled", false);
				$("#step2 input").attr("disabled",false);
			});
			
		});

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
	});

</script>

<?php require_once('templates/footer.php'); ?>
