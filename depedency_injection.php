<?php

interface dbConnection
{
	public function query();
	public function result();
}

class mysqlConnection implements dbConnection
{
	private $username;
	private $password;
	private $query;
	private $table;
	private $conn;

	public function __construct($username, $password, $db)
	{
		$this->username = $username;
		$this->password = $password;

		$conn = mysql_connect('localhost', $username, $password);
		mysql_select_db($db);
	}

	public function query($query = NULL)
	{
		$this->query = mysql_query($query);
		return $this;
	}

	public function resultArray()
	{
		$tmp = array();
		while ($row = mysql_fetch_array($this->query))
		{
			$tmp[] = $row;
		}

		return $tmp;
	}

	public function result()
	{
		$tmp = array();
		while ($row = mysql_fetch_object($this->query))
		{
			$tmp[] = $row;
		}

		return $tmp;
	}

	public function get($table = NULL)
	{
		$this->table = $table;
		return $this;
	}

	public function insert($table, $data)
	{
		$this->table = $table;
		$field = "(" . implode(",", array_keys($data)) . ")";
		$value = "('" . implode("','", $data) . "')";
		$sql = "INSERT INTO {$table} {$field} VALUES {$value} ";
		$query = $this->query($sql);
	}

	public function update($table, $data, $where = NULL)
	{
		$this->table = $table;
		$dataUpdate = NULL;
		foreach($data as $key => $value)
		{
			$dataUpdate[] =" $key = '{$value}' ";
		}

		$sql = "UPDATE {$this->table} SET " .implode(",", $dataUpdate) . " WHERE {$where}";

		$query = $this->query($sql);
	}
}



class Register
{
	private $db;

	public function __construct(dbConnection $db)
	{
		$this->db = $db;
	}

	public function getUser()
	{
		$query = $this->db->query('SELECT * FROM users');
		return $query->result();
	}

	public function newUser($data)
	{
		$result = $this->db->insert('users', $data);
		return $result;
	}

	public function updateByName($data, $name)
	{
		$query = $this->db->update('users', $data, "user_name = '{$name}' ");
	}
}



$mysqlConnection = new mysqlConnection('root', '', 'db_skeddo');

$register = new Register($mysqlConnection);
$result = $register->getUser();

$data = array('user_name' => 'xxx123', 'user_password' => 'ridwan');
$register->newUser($data);


$register->updateByName(array('user_password' => 'ridwan sayang kamu'), 'xxx123');