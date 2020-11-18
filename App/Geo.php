<?php
/**
* Copyight(c) Antonio Ramirez Santander.All rights reserved
* Copyight(c) Trivialsoft 2020.
* Hecho en México
*/
namespace App;
class Geo{
	private $db;
	function __construct($db){
		$this->db = $db;
	}
	function paises($id){
		$orm = new \App\Core\Model($this->db);

       $filtro = array();
	   if($id>0){
		  $filtro= array("id"=>$id);
	   }
		$list = $orm->select($filtro,
							"paises",
							"Pais",
							array("id"=>"id","nombre"=>"nombre"),
							"","","");

		return  iterator_to_array ($list);
	}
	function estados($id){
		return array(0=>"Nuevo Leon",1=>"Veracruz");
	}
	function save_paises($data){
		$orm = new \App\Core\Model($this->db);
	    if($data["id"]>0){
			
			$instances = $this->paises( $data["id"] );
			$instance = $instances[0];
			$instance->nombre = $data["nombre"];
			return $orm->save($instance , "paises", "id", array("id"=>"id","nombre"=>"nombre"));;
		}else{
			return $orm->save($data, "paises", "id", array("id"=>"id","nombre"=>"nombre"));
		}
	}
}
?>