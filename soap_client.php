<?php
require_once('nusoap.php');	
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('server.php');

$server = url_origin( $_SERVER );

$error  = '';
$result = array();	
$response = '';
$wsdl = $server['base_url'].$server['directory']."/soap_server.php?wsdl";

// create client object
$client = new nusoap_client($wsdl);
$result = $client->call('fetchStudent', array('admission'));
// var_dump($result);
// die;

if(isset($_POST['admission'])) {
	$admission = trim($_POST['admission']);
	if(!$admission) {
		$error = 'Enter your admission';
	}

	if(!$error){
		// var_dump($error);
		// $err = $client->getError();
		// if ($err) {
		// 	echo '<h2>Constructor error</h2>' . $err;
		// 	var_dump($client->response);
		// 	// At this point, you know the call that follows will fail
		//     exit();
		// }
			
		try {
			$result = $client->call('fetchStudent', array($admission));
			$result = json_decode($result);
	    }catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			die;
		}
	}
}	

/* Add new student **/
if(isset($_POST['addbtn'])){
	$name = trim($_POST['name']);
	$admission = trim($_POST['admission']);
	$email = trim($_POST['email']);
	$number = trim($_POST['number']);
	$address = trim($_POST['address']);
	$points = trim($_POST['points']);

	//Check all values have been entered
	if(!$admission || !$name || !$address || !$email || !$points || !$number){
		$error = 'All fields are required.';
	}

	if(!$error) {
		//create client object
		$client = new nusoap_client($wsdl, true);
		$err = $client->getError();
		if ($err) {
			echo '<h2>Constructor error</h2>' . $err;
			// At this point, you know the call that follows will fail
		    exit();
		}
			
		try {
			/** Call insert student method */
			$response =  $client->call('insertStudent', array($admission, $name, $email, $number, $address, $points));
			$response = json_decode($response);
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Web Service SOAP-PHP</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstr apcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>

    <div class="container">
        <h2>SOAP Web Service PHP</h2>
        <br />

        <div class='row'>
            <form class="form-inline" method='post' name='form1'>
                <?php if($error) { ?>
                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <strong>Error!</strong>&nbsp;<?php echo $error; ?>
                </div>
                <?php } ?>
                <div class="form-group">
                    <label for="email">Admission</label>
                    <input type="text" class="form-control" name="admission" id="admission"
                        placeholder="Admission Number" required>
                </div>
                <input type="submit" name='sub' class="btn btn-default"></input>
            </form>
        </div>
        <br />
        <h3>Results</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Admission</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Entry Points</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result){ ?>
                <tr>
                    <td><?php echo $result->student_name; ?></td>
                    <td><?php echo $result->admission; ?></td>
                    <td><?php echo $result->address; ?></td>
                    <td><?php echo $result->email; ?></td>
                    <td><?php echo $result->number; ?></td>
                    <td><?php echo $result->entry_points; ?></td>
                </tr>
                <?php 
  		}else{ ?>
                <tr>
                    <td colspan='5'>Data does not exist</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class='row'>
            <h3>Registration Form</h3>
            <?php if(isset($response->status)) {

	if($response->status == 200){ ?>
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <strong>Success!</strong>&nbsp;
            </div>
            <?php 
	}elseif(isset($response) && $response->status != 200) { ?>
            <div class="alert alert-danger fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <strong>Error!</strong>&nbsp;
            </div>
            <?php } 
	}
	?>

            <form class="form-inline" method='post' name='form1'>
                <?php if($error) { ?>
                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <strong>Error!</strong>&nbsp;<?php echo $error; ?>
                </div>
                <?php } ?>
                <div class="form-group">

                    <label for="email"></label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Student Name" required>
                    <input type="text" class="form-control" name="admission" id="admission"
                        placeholder="Admission Number" required>
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" required>
                    <input type="text" class="form-control" name="address" id="address" placeholder="Address" required>
                    <input type="text" class="form-control" name="number" id="number" placeholder="Phone Number"
                        required>
                    <input type="text" class="form-control" name="points" id="points" placeholder="Entry Points"
                        required>
                </div>
                <input type="submit" name='addbtn' class="btn btn-default"></input>
            </form>
        </div>
    </div>

</body>

</html>