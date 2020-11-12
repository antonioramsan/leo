<?php
    /*
	* Copyright(c)Antonio Ramirez Santander. All rights reserved.
	* Copyright(c)TrivialSoft 2020.
	*/
	namespace App;
	class Media {

	private $db;
	function __construct($db){
		$this->db = $db;
	}
/**
* devuelve el listado de archivos
*/
function archivos($id){

		$orm = new \App\Core\Model($this->db);

       $filtro = array();
	   if($id > 0){
		  $filtro = array("id"=>$id);
	   }
		$list = $orm->select($filtro,
							"archivos",
							"Archivo",
							array(	"id"=>"id",
									"nombre"=>"nombre",
									"contenido"=>"contenido"
							),
							"","","");
		$items = iterator_to_array ($list);
		/*se convierte en base64 el contenido antes de retornar el objeto*/
		foreach($items as $item){
				$item->contenido = "data:image/png;base64," . base64_encode($item->contenido);
		}
		return  $items;
	}
	/**
	* guarda o actualiza un archivo
	*/
	function save_archivos($data){
		$orm = new \App\Core\Model($this->db);
		if($data["id"]>0){
			/*recuperar la instancia*/
			$instances = $this->archivos( $data["id"] );
			$instance = $instances[0];
			/*se sobreescribir las propiedades*/
			$instance->nombre = $data["nombre"];
			$instance->contenido = $data["contenido"];
			/*se convierte a binario antes de guardar la propiedad "contenido"*/
			if(isset($instance->contenido) ){
				$datab = $instance->contenido;
				list($type, $datab) = explode(';', $datab);
				list(, $datab)      = explode(',', $datab);
				$instance->contenido = base64_decode($datab);
			}
			return $orm->save($instance , "archivos", "id", array("id"=>"id","nombre"=>"nombre" ,"contenido"=>"contenido" ));
		}else{
			if(isset($data["contenido"]) ){
			/*se convierte a binario antes de guardar la propiedad "contenido"*/
			$datab = $data["contenido"];
				list($type, $datab) = explode(';', $datab);
				list(, $datab)      = explode(',', $datab);
				$data["contenido"] = base64_decode($datab);
			}

			return $orm->save($data, "archivos", "id", array("id"=>"id","nombre"=>"nombre","contenido"=>"contenido" ));
		}
	}
}
?>