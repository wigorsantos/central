<?php

class Application {
	private $obj;

	public function __construct($id = null){
		if($id){
			if(gettype($id) == "string"){
				$id = new MongoId($id);
			}
			$this->obj = DB::$application->findOne(array('_id'=>$id));
			if(is_null($this->obj)) throw new Exception("Application not found");
		}else{
			$this->obj = array();
		}
	}

	public function __set($name,$value){
		if(in_array($name,array('name','description'))){
			$this->obj[$name] = $value;
		}
	}

	public function __get($name){
		if (in_array($name, array('name','description','secret'))) {
			return $this->obj[$name];
		} elseif ($name == 'id') {
			return (string) $this->obj['_id'];
		} elseif ($name == 'authorizations') {
			$auths = DB::$authorization->find(array('application'=>$this->obj['_id']),array('_id'));
			$list = array();
			foreach ($auths as $auth) {
				$list[] = Authorization($auth['_id']);
			}
			return $list;
		}
	}

	public function save(){
		if(!in_array("secret", $this->obj)){
			$this->obj['secret'] = $this->makeSecret();
		}
		DB::$application->save($this->obj);
	}

	public function remove(){
		DB::$application->remove($this->obj);
	}

	private function makeSecret() {
		$length = 20;
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$string = '';    

		for ($p = 0; $p < $length; $p++) {
		$string .= $characters[mt_rand(0, strlen($characters))];
		}

		return $string;
	}
}

class Authorization {
	private $obj;

	public function __construct($id = null){
		if($id){
			if(gettype($id) == "string"){
				$id = new MongoId($id);
			}
			$this->obj = DB::$authorization->findOne(array('_id'=>$id));
			if(is_null($this->obj)) throw new Exception("Authorization not found");
		} else {
			$this->obj = array();
		}
	}

	public function __set($name,$value){
		if ($name == 'user') {
			$this->obj[$name] = $value;
		} elseif ($name == 'application') {
			if (is_a($value, 'Application')) {
				$this->obj[$name] = new MongoId($value->id);
			} elseif (is_a($value, 'MongoId')) {
				$this->obj[$name] = $value->id;
			} elseif (gettype($value) == "string") {
				$this->obj[$name] = new MongoId($value);
			}
		}
	}

	public function __get($name){
		if (in_array($name, array('user','date'))) {
			return $this->obj[$name];
		} elseif ($name == 'token') {
			return (string) $this->obj['_id'];
		} elseif ($name == 'application') {
			$app = DB::$application->findOne(array('_id'=>$this->obj['application']),array('_id'));
			return new Application($app['_id']);
		}
	}

	public function save(){
		if(!in_array("date", $this->obj)){
			$this->obj['date'] = new MongoDate();
		}
		DB::$authorization->save($this->obj);
	}

	public function remove(){
		DB::$authorization->remove($this->obj);
	}

	public function loadAuth($user,$application){
		if(gettype($application) == "string"){
			$application = new MongoId($application);
		}
		$result = DB::$authorization->findOne(array('user' => $user,'application' => $application));
		if(is_null($result)) return false;
		$this->obj = $result;
		return true;
	}
}

?>