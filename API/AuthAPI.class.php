<?php
class AuthAPI {
	private $error, $errorMessage, $token;
	public $app;

	public function loadApp($id,$secret){
		$id = $id ? $id : "no-id";
		$secret = $secret ? $secret : "no-secret";
		try {
			$this->app = new Application($id);
			if($this->app->secret != $secret)
				throw new Exception("Application secret invalid");
			$this->error = false;
			return true;
		} catch (Exception $e) {
			$this->error = true;
			$this->errorMessage = $e->getMessage();
		}
		return false;
	}

	public function getCallbackURL($callback){
		$url = $callback;
		$params = array();
		if($this->token) $params['token'] = $this->token;
		if($this->error) {
			$params['error'] = true;
			$params['message'] = $this->errorMessage;
		}
		$url .= stripos($url, '?') === false ? '?' : '&';
		$url .= http_build_query($params);
		return $url;
	}

	public function action($action, $user){
		if ($action == 'accept') {
			$auth = new Authorization();
			$auth->application = $this->app;
			$auth->user = $user;
			$auth->save();
			$this->token = $auth->token;
			$this->error = false;
		} elseif ($action == 'refuse') {
			$this->error = true;
			$this->errorMessage = 'Refused from user.';
		} else {
			$this->error = true;
			$this->errorMessage = 'Unknow action.';
		}
	}

	public function isAuthorized($user){
		$auth = new Authorization();
		$loaded = $auth->loadAuth($user,$this->app->id);
		if($loaded){
			$this->token = $auth->token;
			$this->error = false;
		}
		return $loaded;
	}
}