<?php
require_once('conn.php');
require_once('nusoap.php'); 

$server = new nusoap_server();

 /* Method to insert a new student */
function insertStudent($admission, $name, $email, $number, $address, $points) {
  global $dbconn;
  $sql_insert = 'INSERT INTO register (admission, student_name, email, number, address, entry_points) values (:admission, :student_name, :email, :number, :address, :entry_points)';
  $stmt = $dbconn->prepare($sql_insert);
  // insert a row
  $result = $stmt->execute(array(':admission'=>$admission, ':student_name'=>$name, ':email'=>$email, ':number'=>$number, ':address'=>$address, ':entry_points'=>$points));
  if($result) {
    return json_encode(array('status'=> 200, 'msg'=> 'success'));
  }
  else {
    return json_encode(array('status'=> 400, 'msg'=> 'fail'));
  }
  $dbconn = null;
}
/* Fetch student data */
function fetchStudent($admission){
	global $dbconn;
	$sql = 'SELECT admission, student_name, email, number, address, entry_points FROM register WHERE admission = :admission';
  // prepare sql and bind parameters
  $stmt = $dbconn->prepare($sql);
  $stmt->bindParam(':admission', $admission);
  // insert a row
  $stmt->execute();
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  return json_encode($data);
}

$server->configureWSDL('studentServer', 'urn:details');
$server->register('fetchStudent',
		array('admission' => 'xsd:string'),  //parameter
		array('return' => 'xsd:string'),  //output
    // 'urn:details',   //namespace
    // 'urn:details#fetchStudent' //soapaction
    );
    
    $server->register('insertStudent',
		array('admission' => 'xsd:string', 'name' => 'xsd:string', 'email' => 'xsd:string', 'number' => 'xsd:string', 'address' => 'xsd:string', 'entry_points' => 'xsd:string'),  //parameter
		array('return' => 'xsd:string'),  //output
		// 'urn:details',   //namespace
		// 'urn:details#fetchStudent' //soapaction
    );

$server->service(file_get_contents("php://input"));
?>