<?php

$c = parse_ini_file('config.ini', true);

function return_else($value,$else){
	if(!isset($value) || empty($value) || is_null($value)){
		return $else;
	}
	return $value;
}

/**
 * Organização
 */
# Nome da organização
define('ORG_NAME',return_else($c['organization']['name'],'Central de Solicitações'));
# Informações legais
define('ORG_LEGAL',return_else($c['organization']['legal'],'<a rel="license" href="http://creativecommons.org/licenses/GPL/2.0/deed.pt"><img alt="Licença Creative Commons" style="border-width:0" src="http://i.creativecommons.org/l/GPL/2.0/88x62.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">Central de Solicitações</span> de <span xmlns:cc="http://creativecommons.org/ns#" property="cc:attributionName">Sport Club Corinthians Paulista</span> foi licenciado com uma <a rel="license" href="http://creativecommons.org/licenses/GPL/2.0/deed.pt">Licença Creative Commons - GNU General Public License</a>.<br />Permissões além do escopo dessa licença podem estar disponível em <a xmlns:cc="http://creativecommons.org/ns#" href="mailto://ti@sccorinthians.com.br" rel="cc:morePermissions">mailto://ti@sccorinthians.com.br</a>.'));
# Logo principal
define('ORG_ICON',return_else($c['organization']['icon'],'res/img/icon.png'));
# Logo principal
define('ORG_LOGO',return_else($c['organization']['logo'],'res/img/logo.png'));
# Logo em marca d'agua
define('ORG_WATERMARK',return_else($c['organization']['watermark'],'res/img/watermark.png'));
# Email da TI (ou Service Desk)
define('ORG_TIMAIL',return_else($c['organization']['timail'],'ti@localhost'));

/**
 * Ambiente do sistema
 */
# Localização do sistema
define('SYSTEM_LOCALE',return_else($c['environment']['locale'],'pt_BR.UTF-8'));
# Nome do servidor
define('SERVER_NAME',return_else($c['environment']['server_name'],'localhost'));
# Caminho do site no servidor
define('SITE_BASE',return_else($c['environment']['site_base'],'/'));
# Caminho completo do site (para usar em e-mails)
define('SITE_PATH',return_else($c['environment']['site_path'],'http://' . SERVER_NAME . SITE_BASE));
# Habilitar o Debug?
define('DEBUG_ENABLED',return_else($c['environment']['debug_enabled'],false));
# Nível do Debug
define('DEBUG_LEVEL',return_else($c['environment']['debug_level'],4));
# Tempo limite da sessão
define('SESSION_TIMEOUT',return_else($c['environment']['session_timeout'],1800));

/**
 * Envio de e-mails (SMTP)
 */
# Servidor SMTP
define('SMTP_SERVER',return_else($c['smtp']['smtp_server'],'localhost'));
# Autenticar no servidor SMTP?
define('SMTP_AUTH',return_else($c['smtp']['smtp_auth'],false));
# Usuário do servidor SMTP
define('SMTP_USERNAME',return_else($c['smtp']['smtp_username'],''));
# Senha do usuário do servidor SMTP
define('SMTP_PASSWORD',return_else($c['smtp']['smtp_password'],''));
# Nome padrão do remetente dos e-mails
define('SMTP_DEFAULT_SENDER_NAME',return_else($c['smtp']['smtp_default_sender_name'],'Central de Solicitações'));
# E-mail padrão do remetente dos e-mails
define('SMTP_DEFAULT_SENDER_EMAIL',return_else($c['smtp']['smtp_default_sender_email'],'solicitacao@localhost'));

/**
 * Active directory (LDAP)
 */
# Servidor(es) LDAP
define('ADLDAP_SERVER',return_else((is_array($c['ldap']['ldap_server']) ? serialize($c['ldap']['ldap_server']) : serialize(array($c['ldap']['ldap_server']))),serialize(array('localhost'))));
# Base DN do servidor LDAP
define('ADLDAP_BASE_DN',return_else($c['ldap']['ldap_base_dn'],'CN=localhost'));
# Sufixo de conta no servidor LDAP
define('ADLDAP_ACCOUNT_SUFFIX',return_else($c['ldap']['ldap_account_suffix'],'@localhost'));
# Usuário administrativo do AD
define('ADLDAP_ADMIN_USERNAME',return_else($c['ldap']['admin_username'],null));
# Senha do usuário adminstrativo do AD
define('ADLDAP_ADMIN_PASSWORD',return_else($c['ldap']['admin_password'],null));

/**
 * Banco de dados (MongoDB)
 */
# Servidor do Mongo DB
define('MONGODB_SERVER',return_else($c['mongodb']['mongodb_server'],'localhost'));
# Autenticar no servidor MongoDB?
define('MONGODB_AUTH',return_else($c['mongodb']['mongodb_auth'],false));
# Usuário do MongoDB
define('MONGODB_USER',return_else($c['mongodb']['mongodb_user'],''));
# Senha do MongoDB
define('MONGODB_PASSWORD',return_else($c['mongodb']['mongodb_password'],''));
# Banco de dados selecionado no MongoDB
define('MONGODB_DATABASE',return_else($c['mongodb']['mongodb_database'],'solicitacoes'));
