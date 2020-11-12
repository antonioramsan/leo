<?php
/**
* Ejemplo de un api web básico
*/
require_once "vendor/autoload.php";

$api = new \App\Api();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$controller_name = $_GET["c"];
		$endpoint = $_GET["e"];
		echo json_encode( $api->write($controller_name,$endpoint));
}else
{
	if(isset($_GET["c"])&& isset($_GET["e"])){
		$controller_name = $_GET["c"];
		$endpoint = $_GET["e"];
		$id =0;
		if(isset($_GET["i"]))
			$id = $_GET["i"];
		echo json_encode( $api->read($controller_name,$endpoint,$id), JSON_PRETTY_PRINT );
	}
}
?>