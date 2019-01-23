<?php
require_once("Rest.inc.php");

class API extends REST 
{
	public $data = "";
	const DB_SERVER = "localhost";
	const DB_USER = "stcasper";
	const DB_PASSWORD = "o1fdzc1a";
	const DB = "stcasper";

	private $db = NULL;

	public function __construct()
	{
		parent::__construct();// Init parent contructor
		$this->dbConnect();// Initiate Database connection
	}

	//Database connection
	private function dbConnect()
	{
		$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
		if($this->db)
		mysql_select_db(self::DB,$this->db);
	}
	
	//Encode array into JSON
	private function json($data)
	{
		if(is_array($data)){
			return json_encode($data,JSON_FORCE_OBJECT);
		}
	}

	//Public method for access api.
	//This method dynamically call the method based on the query string
	public function processApi()
	{
		$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
		if((int)method_exists($this,$func) > 0) 
			$this->$func();
		else
			// If the method not exist with in this class, response would be "Page not found".
			$this->response('',404); 
			
	}

	private function login()
	{
		// Cross validation if the request method is POST else it will return "Not Acceptable" status
		if($this->get_request_method() != "POST")
		{
			$this->response('',406);
		}

		$username = $this->_request['username']; 
		$password = $this->_request['password'];
		
		ini_set("session.gc_maxlifetime", 60);

		// Input validations
		if(!empty($username) and !empty($password))
		{
			$sql = mysql_query("SELECT user_id, user_fullname, user_email FROM users WHERE user_fullname = '$username' AND user_password = '".md5($password)."' LIMIT 1", $this->db);
			if(mysql_num_rows($sql) > 0){
				$result = mysql_fetch_array($sql,MYSQL_ASSOC);
				
				session_start ();
				$_SESSION['login'] = $username;
				$_SESSION['pwd'] = md5($password);

				// If success everything is good send header as "OK" and user details
				$this->response($this->json($result), 200);
			}
			$this->response('', 204); // If no records "No Content" status
		}

		// If invalid inputs "Bad Request" status message and reason
		$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
		$this->response($this->json($error), 400);
	}

	private function users()
	{ 
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		$sql = mysql_query("SELECT user_id, user_fullname, user_email FROM users WHERE user_status = 1", $this->db);
		if(mysql_num_rows($sql) > 0)
		{
			$result = array();
			while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC))
			{
				$result[] = $rlt;
			}
			// If success everything is good send header as "OK" and return list of users in JSON format
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}
	
	private function createUser()
	{
		if($this->get_request_method() != "POST")
		{
			$this->response('',406);
		}
		$sql = "INSERT INTO `stcasper`.`users` (`user_fullname`, `user_email`, `user_password`, `user_status`) VALUES (".$this->_request['user_fullname']."', '".$this->_request['user_email']."', '".md5($this->_request['user_password'])."', '".$this->_request['user_status']."');";
		$result=mysql_query($sql, $this->db);
		$success = array('status' => "Success", "msg" => "Successfully one record created.");
		$this->response($this->json($success),200);
	}

	private function deleteUser()
	{
		if($this->get_request_method() != "DELETE"){
			$this->response('',406);
		}
		$id = (int)$this->_request['id'];
		if($id > 0)
		{ 
			mysql_query("DELETE FROM users WHERE user_id = $id");
			$success = array('status' => "Success", "msg" => "Successfully one record deleted.");
			$this->response($this->json($success),200);
		}
		else
		{
			$this->response('',204); // If no records "No Content" status
		}
	}
	
	private function check_session() //Check if a session exists
	{ 
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		session_start ();
		if (isset($_SESSION['login']) && isset($_SESSION['pwd'])) 
		{
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}
	
	private function unlogin() // removes an opened session
	{
		if($this->get_request_method() != "POST")
		{
			$this->response('',406);
		}
		session_start();
		session_destroy();
		$this->response($this->json($success),200);
	}

	private function logs() //Renvoie toute la table des températures
	{ 
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		$sql = mysql_query("SELECT DATE_FORMAT(Temp_releve.Date, \"%d/%m/%Y à %H:%i\") as Date,Temp_releve.Temp_Ext,Temp_releve.Temperature, Temp_releve.Humidite, Temp_releve.Tp_Consigne, CONCAT(Radiateurs.Piece, ' (',Temp_releve.Radiateur, ')') as Piece, Temp_releve.Allume FROM Temp_releve, Radiateurs where Temp_releve.Radiateur = Radiateurs.Numero ORDER BY Temp_releve.Date desc", $this->db);
		if(mysql_num_rows($sql) > 0)
		{
			$result = array();
			while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC))
			{
				$result[] = $rlt;
			}
			// If success everything is good send header as "OK" and return list of users in JSON format
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}
	
	private function logs_last() //Renvoie les dernières mesures 
	{ 
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		$sql = mysql_query("SELECT DATE_FORMAT(Temp_releve.Date, \"%d/%m/%Y à %H:%i\") as Date,Temp_releve.Temp_Ext,Temp_releve.Temperature, Temp_releve.Humidite, Temp_releve.Tp_Consigne, CONCAT(Radiateurs.Piece, ' (',Temp_releve.Radiateur, ')') as Piece, Temp_releve.Allume FROM Temp_releve, Radiateurs where Temp_releve.Radiateur = Radiateurs.Numero ORDER BY Temp_releve.Date desc", $this->db);
		if(mysql_num_rows($sql) > 0)
		{
			$result = array();
			$line = 0;
			$lineMax = $this->_request['cpt'];
			//echo $lineMax;
			while(($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)) && $line<$lineMax)
			{
				$line++;
				$result[] = $rlt;
			}
			// If success everything is good send header as "OK" and return list of users in JSON format
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}
	
	private function logs_last_rad() //Renvoie les dernières mesures par radiateur
	{ 
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		
		$sql = mysql_query("SELECT DATE_FORMAT(Temp_releve.Date, \"%d/%m/%Y à %H:%i\") as Date,Temp_releve.Temp_Ext,Temp_releve.Temperature, Temp_releve.Humidite, Temp_releve.Tp_Consigne, CONCAT(Radiateurs.Piece, ' (',Temp_releve.Radiateur, ')') as Piece, Temp_releve.Allume FROM Temp_releve, Radiateurs where Temp_releve.Radiateur = Radiateurs.Numero and Temp_releve.Radiateur = ".$this->_request['radiateur']." ORDER BY Temp_releve.Date desc", $this->db);
		if(mysql_num_rows($sql) > 0)
		{
			$result = array();
			$line = 0;
			$lineMax = $this->_request['cpt'];
			//echo $lineMax;
			while(($rlt = mysql_fetch_array($sql,MYSQL_ASSOC)) && $line<$lineMax)
			{
				$line++;
				$result[] = $rlt;
			}
			// If success everything is good send header as "OK" and return list of users in JSON format
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}

	private function insert_temp()
	{
		if($this->get_request_method() != "POST")
		{
			$this->response('',406);
		}
		$sql = "INSERT INTO `stcasper`.`Temp_releve` (`Date`, `Temp_Ext`, `Temperature`, `Humidite`, `Tp_Consigne`, `Radiateur`, `Allume`) VALUES (CURRENT_TIMESTAMP, '".$this->_request['temp_ext']."', '".$this->_request['temp']."', '".$this->_request['humi']."', '".$this->_request['Consigne']."', '".$this->_request['Radiateur']."', '".$this->_request['Allume']."');";
		$result=mysql_query($sql, $this->db);
		$success = array('status' => "Success", "msg" => "Successfully one record created.");
		$this->response($this->json($success),200);
	}
	
	private function insert_consigne()
	{
		if($this->get_request_method() != "POST")
		{
			$this->response('',406);
		}
		if($this->_request['temp_cons'] > 6 && $this->_request['temp_cons'] < 26)
		{
			session_start();
			if (isset($_SESSION['login']) && isset($_SESSION['pwd'])) 
			{
				$sql = "INSERT INTO `stcasper`.`Temp_consigne` (`Compteur`, `Date`, `Radiateur`, `Temperature`, `Demandeur`, `Date_Application`, `Heure_Application`) VALUES (NULL, NULL, '".$this->_request['radiateur']."', '".$this->_request['temp_cons']."', '".$this->_request['user']."', CURDATE(), CURTIME());";
				echo $sql;
				$result=mysql_query($sql, $this->db);
				$success = array('status' => "Success", "msg" => "Successfully one record created.");
				session_destroy();
				$this->response($this->json($success),200);
			}
		}
		$this->response('',400); // Invalid inputs
			
	}
	
	private function read_consigne()
	{
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		$radia = $this->_request['Radiateur'];
		$sql = mysql_query("SELECT Temperature FROM Temp_consigne where Radiateur = ".$radia." ORDER BY Compteur desc", $this->db);
		//echo $sql;
		if(mysql_num_rows($sql) > 0)
		{
			$result = array();
			$line = 0;
			$rlt = mysql_fetch_array($sql,MYSQL_ASSOC);
			$result[] = $rlt;
			// If success everything is good send header as "OK" and return list of users in JSON format
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}
	
	private function logs_consigne()
	{
		// Cross validation if the request method is GET else it will return "Not Acceptable" status
		if($this->get_request_method() != "GET")
		{
			$this->response('',406);
		}
		$sql = mysql_query("SELECT Radiateur, Temperature, Demandeur, Date_Application, Heure_Application FROM Temp_consigne ORDER BY Compteur desc LIMIT ".$this->_request['cpt'], $this->db);
		if(mysql_num_rows($sql) > 0)
		{
			$result = array();
			while($rlt = mysql_fetch_array($sql,MYSQL_ASSOC))
			{
				$result[] = $rlt;
			}
			// If success everything is good send header as "OK" and return list of users in JSON format
			$this->response($this->json($result), 200);
		}
		$this->response('',204); // If no records "No Content" status
	}
}

// Initiiate Library
$api = new API;
$api->processApi();
?>
