<?php 
	try{
		require_once(dirname(__FILE__) . '/setup.php');
		// Carregar a view de acordo com o nome do método
		$views = new Views();
		$name = REQUEST_METHOD_NAME;
		if(IS_POST){
			$append = "_post";
		}else{
			$append = "_get";
		}
		$method_name = $name . $append;
		if(!method_exists($views, $method_name)){
			$method_name = $name . "_all";
		}
		if(!method_exists($views, $method_name)){
			$_SESSION['IS_ERROR'] = true;
			header(':', true, 404);
			require_once(dirname(__FILE__) . '/templates/404.php');
		}else{
			$views->$method_name();
		}
		
	}catch(Exception $e){
		$_SESSION['IS_ERROR'] = true;
		$E = array(
			'message' => $e->getMessage(),
			'code' => $e->getCode(),
			'file' => $e->getFile(),
			'line' => $e->getLine(),
			'trace' => $e->getTrace(),
			'trace_string' => $e->getTraceAsString()
		);
		header(":", true, 500);
		require_once(dirname(__FILE__) . '/templates/500.php');
	}
?>
