<?php
/**
* Copyight(c) Antonio Ramirez Santander.All rights reserved
* Copyight(c) Trivialsoft 2020.
* Hecho en México
*/
namespace App\Core;
class Model implements \JsonSerializable{
	// -- nombre de la tabla en la base de datos
    public $table = "";
	public $columnid = "";
	public $tsstatus;
    public $tsmsg;
	public $entitymode;
	private $m_SQL = "";
	private $m_level = 0;
	public $_context;

function tsCurrentTime()
	{
		 date_default_timezone_set("Mexico/General");
		 return date("Y-m-d H:i:s");
	}

function __tostring()
	{
		return "";
	}

public function jsonSerialize()
	{
		return [];
	}

function __construct($db)
	{
		$this->_db = $db;
	}
	// -- obtiene el ultimo transaction id de una instancia
	function MaxTsid()
	{
		$this->_context->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$sql_condition ="select max(Tsid) as Tsid from " . $this->table . ";";
		$stmt = $this->_context->db->prepare($sql_condition);
			if($stmt->execute())
			{
				$result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
				foreach( (new \RecursiveArrayIterator($stmt->fetchAll())) as $key => $value )
				{
					return $value["Tsid"];
				}
			}
	}
	function Counter()
	{
		$this->_context->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$sql_condition ="select count(*) as Tsid from " . $this->table . ";";
		$stmt = $this->_context->db->prepare($sql_condition);
			if($stmt->execute())
			{
				$result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
				foreach( (new \RecursiveArrayIterator($stmt->fetchAll())) as $key => $value )
				{
					return $value["Tsid"];
				}
			}
	}

	/**
	* para recuperar una lista de entidades
	*/
	function select($filter,$tablename,$modelname, $list_properties ,$order = "", $selection = "",$page="" )
	{

        $this->table = $tablename;
		$this->_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

		$sql_condition = "";

        foreach( $filter as $field => $value )
        {

		 	if($sql_condition != "")
            {
                $sql_condition .= " And ";
            }
            $sql_condition .= $field . " = :" . $field;
		}


	   $colums_names = "*";


		if($sql_condition!="")
		{
			$sql_condition ="select " . $colums_names . " from " . $this->table . " where " . $sql_condition;
		}else
		{
				$sql_condition ="select " . $colums_names . " from " . $this->table ;
		}
		if( trim($order)  !="")
		{
			$sql_condition .= " " . $order;
		}

        $stmt = $this->_db->prepare($sql_condition);


		 try{
			foreach( $filter as $field => $value )
			{
				$storage[$field] = $value;
				$stmt->bindparam(':' . $field ,  $storage[$field]);
			}
			$aus = "\\App\\" . $modelname;

			if($stmt->execute())
			{

				$result = $stmt->setfetchmode(\PDO::FETCH_ASSOC);
				foreach( (new \recursivearrayiterator($stmt->fetchall())) as $key => $value )
				{
				  $omodel = new $aus($this->_db);
				foreach($list_properties as $keyp => $valuep)
					{
							$keyp_db = $valuep;

							if(isset($value[$keyp_db]) )// -- valida si existe el campo
							{
								$omodel->{$keyp} = $value[$keyp_db];
							}else{
								if(isset($value[$keyp]) )// -- valida si existe el campo
								{
									$omodel->{$keyp} = $value[$keyp];
								}
							}
					}
					yield $omodel;
				}
			}
		 }catch(\exception $e)
		 {
			 throw new \exception("bad select in ..."   );
		}
	}

/**
* para actualiza una entidad
*/
   function save($instance, $tablename, $id, $list_properties)
   {
		$this->table = $tablename;

		$idcoc =  0;
		$modelnameid = $id;
		if($this->columnid!="")
		{
			$modelnameid = $this->columnid;
		}

		if( isset($instance->id) )
		{
			$idcoc = $instance->id;
		}
		 else
		 {
			if( isset($instance->{$modelnameid} ) )
				{
					$idcoc = $instance->{$modelnameid};
				}
		 }

	   if($idcoc == 0)
	   {
		   // -- se esta guardando una nueva entidad
		   return $this->addnew($instance ,$tablename, $id, $list_properties);
	   }

		// -- sentencia sql
		$sql = "UPDATE " . $this->table  . " SET ";
		$sql_condition = "";

        foreach( $list_properties as $field => $value )
        {
				$db_field = $value;
				if($sql_condition != "")
				{
					$sql_condition .= " , ";
				}
				$sql_condition .= $db_field . " = :" . $field;
		}
		$sql .=  $sql_condition  . " WHERE " . $modelnameid  . " = " . $idcoc;

		$stmt = $this->_db->prepare($sql);

		$storage =[];
		foreach( $list_properties as $field => $value )
        {
				$storage[$field] = $instance->{$field};
				$stmt->bindValue(':' . $field, $storage[$field]);
        }

		$rs = $stmt->execute();
		return 0;
	}
	/**
	* para insertar una entidad
	*/
    private function addnew($instance, $tablename, $id, $list_properties)
   {

		$modelnameid = $id;
		$sql = "INSERT INTO " . $this->table  . "  ";
		$sql_condition = "";
		$sql_condition2 = "";


        foreach( $list_properties as $field => $value )
        {
			if($sql_condition != "")
            {
                $sql_condition .= " , ";
				$sql_condition2 .=", ";
            }
            $sql_condition .= $value ;
			$sql_condition2 .= "  :" . $value ;
        }

		$sqlaux = "";
		$sql .= "(" . $sql_condition .") " . $sqlaux .  " VALUES(" . $sql_condition2 . ")";

		$stmt = $this->_db->prepare($sql);

		$storage =[];
		foreach( $list_properties as $field => $value )
        {
				$storage[$field] = $instance[$field];
				$stmt->bindParam(':' . $field ,  $storage[$field]);
        }

		$rs = $stmt->execute();

		if($rs == 1)
		{
				return  $this->_db->lastInsertId();
		}
		else
			return 0;
		 }
/**
* para borrar una entidad
*/
function delete()
	{
		$modelname =  get_class($this);
		$modelnameid = explode("\\", $modelname)[1];
		$idcoc =  0;

		if( isset($this->id) )
		 {
			$idcoc = $this->id;
		 }
		 else
		 {
			if( isset($this->{$modelnameid} ) )
				{
					$idcoc = $this->{$modelnameid};
				}
		 }

		$sql ="DELETE FROM " . $this->table ." WHERE " . $modelnameid ." = :id;";
		$stmt = $this->_context->db->prepare($sql);

		 $stmt->bindParam(':id'  , $idcoc  );

		$rs = $stmt->execute();
		if($rs == 1)
		{
			return $idcoc;
		}else{
			return 0;
		}
	}

 function shrink($props)
 {
	 $modelname =  get_class( $this);
	   foreach($props as $key => $val )
	   {
			if( array_key_exists(  $key , $this->_context->ssd[$modelname] )  )
			{
			  $s = 1;
			}
			else
			{
				 if($key!="modelname"){
				    unset($props[$key]);
				 }
			}
	   }
    $trivial_tql_schema[$modelname] = $props;
	 return $props;
 }
}
?>