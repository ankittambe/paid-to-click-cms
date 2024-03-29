<?php

class User_Models_User extends Account_Models_Account {
	/**
	* The table in database that users are stored
	* @name USER_TABLE
	*/
	const USER_TABLE = 'users';

	/**
	 * The user cannot be registered because some of fields are already in use
	 * These are fields such as username, email and paymentEmail
	 * @name FIELDS_ALREADY_IN_USE
	 */
	const FIELDS_ALREADY_IN_USE = '%s is already in use';

	private $balance;
	private $gender;
	private $referer;
	private $roleID;
	private $origattrs = array();
       //private $errors = array();

	/**
	* Creates a new user using the keys and values from the array in its parameters
	* @param array $args
	*/
	function __construct($args) {
		foreach($this as $key=>$val) {
			if(($key != 'origattrs') and ($key != 'errors')) {
				$this->origattrs[]= $key;
				$this->$key = (isset($args[$key]) ? $args[$key] : null);
			}
		}
	}

	/**
	* Attempts to register the current user.
	* @return SUCCESS
	*/
	function register() {
		$db = Zend_Registry::get('db');

		//create the new user
		$db->insert(self::USER_TABLE, $this->_getPairs());
		return self::SUCCESS;
	}


	/**
	* Updates the users stored details
	* @return boolean
	*/
	function update($args) {
		//check if the user is already in the system
		if($this->accountID != NULL) {
			foreach($args as $key=>$value) {
				$this->$key = $value;
			}
			$db = Zend_Registry::get('db');
			$data = $this->_getPairs();
			$db->update(self::USER_TABLE, $data, 'accountID = ' . $this->accountID);
			return true;
		}
		return false;
	}

        static function getUser($userID) {
            $db = Zend_Registry::get('db');
            $stat = $db->query("SELECT * FROM " . self::USER_TABLE . " WHERE accountID = ?", array($userID));
            $row = $stat->fetch();
            //create and return the new user
            return new User_Models_User($row);
        }

	/**
	* Attempts to login in the user
	* If the visitor cannot be logged in due to no exist username or invalid username and password
	* then an INVALID_LOGIN is returned otherwise a new user is returned
	* If the user exist but is banned the a BANNED is returned
	* @param string $username
	* @param string $password
	* @return INVALID_LOGIN|BANNED|User_Models_User
	*/
	static function login($username, $password) {
		$db = Zend_Registry::get('db');

		$stat = $db->query("SELECT * FROM " . self::USER_TABLE . " WHERE username = ? AND PASSWORD = ? AND status <> ?", array($username,$password, 'advertiser'));
		$row = $stat->fetch();

		//check for wrong username and password
		if(!$row) {
			return self::INVALID_LOGIN;
		}

		//check if the user has been self::BANNED
		if($row['status'] == self::BANNED) {
			$this->errors[]= $row['banReason'];
			return self::BANNED;
		}

		//create and return the new user
		return new User_Models_User($row);
	}

	/**
	 * Gets the names of attributes along with their value in a key-value array
	 * @access private
	 * @return array
	 */
 	private function _getPairs() {
		$pairs = array();
		foreach($this->origattrs as $key) {
			$pairs[$key] = $this->$key;
		}
		return $pairs;
	}

	/**
	 * gets the given attributes name($key) value
	 * @param $key the name of the attribute to get
	 * @return [given keys value type]
	 */
	function __get($key) {
		return $this->$key;
	}

	/**
	 * @param $key the name of the attribute to set
	 * @param $value the value to assign the attribute to
	 * @return void
	 */
	function __set($key, $value) {
		$this->$key = $value;

	}

	function render(){
		return array(
			'Balance'		=> '$' . $this->balance,
			'Username' 		=> $this->username,
			'Gender' 		=> $this->gender,
			'Country' 		=> $this->country,
			'Email' 		=> $this->email,
			'Payment Email' => $this->paymentEmail,
		);
	}
	function resetPass() {

	}

}

?>