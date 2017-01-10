<?php

require 'vendor/autoload.php';
use Slim\Slim;

$app = new \Slim\Slim();
$app->get('/users', 'getUsers');
$app->get('/users/:id', 'getUser');
$app->get('/getProjects/:id', 'getProjects');
$app->post('/add_user', 'addUser');
$app->post('/user_registered', 'userRegistered');
$app->put('/users/:id', 'updateUser');
$app->delete('/users/:id', 'deleteUser');

$app->post('/login', 'loginUser');

$app->run();

function userRegistered() {
 $request = Slim::getInstance()->request();
 $user = json_decode($request->getBody());
 $sql = "INSERT INTO usersRegistered (userAuthGUID, userAuthFullName ,userAuthEmail) VALUES (:userAuthGUID, :userAuthFullName, :userAuthEmail)";
 try {
  $db = getConnection();
  $stmt = $db->prepare($sql);  
  $stmt->bindParam("userAuthGUID", $user->uid);
  $stmt->bindParam("userAuthFullName", $user->full_name);
  $stmt->bindParam("userAuthEmail", $user->email);
  $stmt->execute();
  $user->id = $db->lastInsertId();
  $db = null;
  echo json_encode($user); 
 } catch(PDOException $e) {
  echo '{"error":{"text":'. $e->getMessage() .'}}'; 
 }
}

function getUsers() {
	$sql = "select * FROM users ORDER BY id";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($wines);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getUser($id) {
	$sql = "select * FROM usersRegistered WHERE userAuthGUID='".$id."'";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($wines);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getProjects($id) {
	$sql = "select * FROM projectsPermissions WHERE FKuserAuthGUID='".$id."'";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($wines);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
function addUser() {
	$request = Slim::getInstance()->request();
	$user = json_decode($request->getBody());
	$sql = "INSERT INTO new_users (full_name, email, password) VALUES (:full_name, :email, :password)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("full_name", $user->full_name);
		$stmt->bindParam("email", $user->email);
		$stmt->bindParam("password", $user->password);
		$stmt->execute();
		$user->id = $db->lastInsertId();
		$db = null;
		echo json_encode($user); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function loginUser(){
	$request = Slim::getInstance()->request();
	$user = json_decode($request->getBody());
	$sql = "SELECT * FROM new_users WHERE email='".$user->email."' AND password='".$user->password."'";

	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"success":{"result":'. sizeof($wines) .'}}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}


function updateUser($id) {
	$request = Slim::getInstance()->request();
	$user = json_decode($request->getBody());
	$sql = "UPDATE users SET username=:username, first_name=:first_name, last_name=:last_name, address=:address WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("username", $user->username);
		$stmt->bindParam("first_name", $user->first_name);
		$stmt->bindParam("last_name", $user->last_name);
		$stmt->bindParam("address", $user->address);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($user); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deleteUser($id) {
	$sql = "DELETE FROM users WHERE id=".$id;
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($wines);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getConnection() {
	$dbhost="127.0.0.1";
	$dbuser="root";
	$dbpass="";
	$dbname="angular_tutorial";
	
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>