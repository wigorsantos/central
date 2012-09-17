<?php require_once('templates/header.php'); ?>
<h1><?= $_TITLE ?></h1>

<?php if ($a == "decidir") { ?>
<p>Essa solicitação precisa da sua autorização para prosseguir. O que você quer fazer?</p>
<p><a class="btn btn-primary" href="?a=aprovar"><b>Aprovar</b></a> Você aceita e autoriza essa solicitação.</p>
<p><a class="btn btn-danger" href="?a=recusar"><b>Recusar</b></a> Você não autoriza essa solicitação.</p>
<p><a class="btn" href="?a=devolver"><b>Devolver para revisão</b></a> Alguma coisa está errada e precisa ser corrigida.</p>
<p><a class="btn" href="?a=analisar"><b>Enviar para análise</b></a> É preciso que um especialista verifique as informações da solicitação.</p>
<p><a class="btn" href="<?= SITE_BASE . $s->id ?>/encaminhar"><b>Encaminhar</b></a> Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.</p>
<p><a class="btn" href="<?= SITE_BASE . $s->id ?>">Voltar</a> Decidir mais tarde.</p>
<?php } elseif ($a == "aprovar" || $a == "recusar" || $a == "devolver") { ?>
<form action="?a=observe" method="post">
	<label for="text">Se desejar, adicione uma observação:</label>
	<textarea name="text" id="text" style='width: 400px;max-width:90%;' rows="5"></textarea>
	<p>
		<button class="btn btn-primary" type="submit">Enviar</button>
		<button class="btn" type="button" onclick="window.location = '<?= SITE_BASE . $s->id ?>';">Voltar</button>
	</p>
</form>
<?php } elseif ($a == "analisar") { if(!IS_POST){ ?>
<form action="?a=analisar" method="post">
	<p>Selecione abaixo para qual especialista deseja enviar o pedido de análise:</p>
	<table class="table table-striped" width="100%" cellspacing="2" cellpadding="2" border="0">
		<thead>
		<tr>
			<th colspan="2">Nome do especialista</th>
			<th>Especialidade</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$especialistas = DB::$especialista->find();
		foreach ($especialistas as $e) {
			$uinfo = AD::info($e['usuario'],array('displayname'));
		?>
		<tr>
			<td width="10"><input name="especialista" value="<?= $e['usuario'] ?>" id="<?= $e['_id'] ?>" type="radio"></td>
			<td><label for="<?= $e['_id'] ?>"><strong><?= $uinfo[0]['displayname'][0] ?></strong></label></td>
			<td><?= $e['especialidade'] ?></td>
		</tr>
		<?php
		}
		?>
		</tbody>
	</table>
	<label for="text">Se desejar, adicione uma observação:</label>
	<textarea name="text" id="text" style='width: 400px;max-width:90%;' rows="5"></textarea>
	<p>
		<button class="btn btn-primary" type="submit">Enviar</button>
		<button class="btn" type="button" onclick="window.location = '<?= SITE_BASE . $s->id ?>/decidir';">Cancelar</button>
	</p>
</form>
<?php } else { ?>
<p>Aguarde a resposta do especialista para tomar a sua decisão.</p>
<button class="btn btn-primary" type="button" onclick="window.location = '<?= SITE_BASE . $s->id ?>';">Voltar</button>
<?php } } ?>

<?php require_once('templates/footer.php'); ?>
