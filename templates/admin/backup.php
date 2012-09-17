<?php require_once('base-top.php'); ?>
<p>Para fazer o backup manualmente siga o procedimento abaixo:</p>
<ol>
  <li> Tenha em mãos os dados de acesso ao banco de dados MongoDB, exatamente como definido no arquivo config.ini</li>
  <li> Via <a href="http://www.chiark.greenend.org.uk/~sgtatham/putty/download.html">PuTTY</a> acesse o servidor <?= SERVER_NAME ?> na porta 22 com um usuário válido. (Caso o usuário não seja root, use <code>sudo</code> no inicio desses comandos)</li>
  <li> Faça uma cópia do banco de dados com o comando <code>mongodump -h <i>SERVIDOR</i> --db <i>NOME_DO_BANCO</i> -u <i>USUÁRIO</i> -p <i>SENHA</i> -o /tmp/backup-cs</code>.</li>
  <li> Empacote o backup com o comando <code>tar zcvf  <?= $_SERVER['DOCUMENT_ROOT'] . SITE_BASE ?>bak/backup.tar /tmp/backup-cs</code>.</li>
  <li> Apague os arquivos temporários com o comando <code>rm -Rf /tmp/backup-cs</code>.</li>
  <li> Faça o <a href="<?= SITE_BASE ?>bak/backup.tar">download</a> do backup. </li>
</ol>
<?php require_once('base-bottom.php'); ?>