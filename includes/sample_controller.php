<?php
session_start();
include("Db.php");

if(!isset($_REQUEST)){
	return false;
}

Class sample_controller{
	public $db_handle;
	public function __construct(){ 
		$this->db_handle = new Db();
	}

	public function add($params){	
		if(!empty($params["quantity"])) {
			$productByCode = $this->db_handle->select("SELECT * FROM tblproduct WHERE code='" . $params["code"] . "'");
			$itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$params["quantity"], 'price'=>$productByCode[0]["price"]));
			
			if(!empty($_SESSION["cart_item"])) {
				if(in_array($productByCode[0]["code"],$_SESSION["cart_item"])) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($productByCode[0]["code"] == $k)
								$_SESSION["cart_item"][$k]["quantity"] = $params["quantity"];
					}
				} else {
					$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
				}
			} else {
				$_SESSION["cart_item"] = $itemArray;
			}

			return json_encode($_SESSION); 
		}
	}

	public function getData(){
		$product_array = $this->db_handle->select("SELECT * FROM tblproduct ORDER BY id ASC");
		return json_encode($product_array); 
	}

	public function getcartList(){
		if(!empty($_SESSION["cart_item"])) {
			return json_encode($_SESSION["cart_item"]); 
		}
	}

	public function removeCart($params){	
		if(!empty($_SESSION["cart_item"])) { 
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($params["code"] == $k)
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	}

	public function emptyCart(){
		unset($_SESSION["cart_item"]);
	}

	public function loginProcess($params){ 
		$password = $this->db_handle->escape_string(md5($params['password']));
		$res = $this->db_handle->select("SELECT id, username FROM tbluser WHERE username = ".$this->db_handle->escape_string($params['username'])." and password = ".$password." LIMIT 1 ");
		if(count($res[0]) >0){
			$ret_arr = array("username"=>$res[0]["username"], "userid"=>$res[0]["id"] );	
			$_SESSION["user_details"] = $ret_arr;
		}
		return json_encode($_SESSION["user_details"]);
	}

	public function register($params){
		$username = $this->db_handle->escape_string($params['username']);
		$password = $this->db_handle->escape_string(md5($params['password']));
		$res = $this->db_handle->select("SELECT id, username FROM tbluser WHERE username = ".$username." and password = ".$password." LIMIT 1 ");
		
		if(count($res) == 0){
			$this->db_handle->query("INSERT INTO tbluser(username, password) VALUES(".$username.",".$password.") ");
			//echo "INSERT INTO tbluser(username, password) VALUES('".$username."',".$password.") "; exit;
		}
		$ret = $this->loginProcess($params);
		return $ret;
	}

	public function loginsession(){
		if(isset($_SESSION['user_details'])){ return json_encode($_SESSION["user_details"]); }else{ return false; }
	}

	public function logout(){
		if(isset($_SESSION['user_details'])){
			unset($_SESSION['user_details']);
		}
	}

	public function checkout(){  
		$user_details = $_SESSION['user_details'];
		$cart_item = $_SESSION["cart_item"];
		foreach($_SESSION["cart_item"] as $val) {
			$vaule = $val['quantity'] * $val['price'];
			$this->db_handle->query("INSERT INTO tbluser_cart(user_id, code, quantity, amount, purchased_date) VALUES (".$user_details['userid'].", '".$val['code']."', '".$val['quantity']."', '".$vaule."','".date('Y-m-d H:i:s')."' ) "); 
		}
		return json_encode($_SESSION["cart_item"]);
	}

}

$params = json_decode(file_get_contents('php://input'),true);

$contrl= new sample_controller();

switch ($params['action']) {
	case "add":	
		echo $contrl->add($params); exit;
	break;
	
	case "getData":
		echo $contrl->getData(); exit;
	break;

	case "getcart":
		echo $contrl->getcartList(); exit;
	break;

	case 'remove':
		echo $contrl->removeCart($params); exit;
	break;

	case 'empty':
		echo $contrl->emptyCart(); exit; 
	break;

	case "login":
		echo $contrl->loginProcess($params); exit;
	break;

	case "register":
		echo $contrl->register($params); exit;
	break;	

	case 'loginsession':
		echo $contrl->loginsession(); exit;
	break;

	case"logout":
		echo $contrl->logout(); exit;
	break;

	case 'checkout':
		echo $contrl->checkout(); exit;
	break;

	default:
		# code...
		break;
}

