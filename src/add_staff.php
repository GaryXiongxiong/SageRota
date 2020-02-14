<?php
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
// connect to database
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});

// check connection
if(!$conn) {
	echo "Connection error" . mysqli_connect_error();
}

	// sql query to be sent to database
	$sql = "SELECT * FROM staff WHERE sid = ? and first_name = ?";

	// prepare statement
	$stmt = $conn->prepare($sql);

	// bind statement
	$stmt->bind_param('is', $sid, $name);
	
	$sid = $_REQUEST['sid'];
	$name = $_REQUEST['first_name'];

	// execute statement
	$stmt->execute();

	// fetch results of query and store it as an associative array
	$result = $stmt->get_result()->fetch_all();

	if (count($result) != 0) {
    	$flag = "fail";
    	$staff=array();
    } else {

    	// sql query to insert values
    	$sql_in = "INSERT INTO staff(sid, first_name, last_name, phone_number, e_mail, job_title, gender, status) 
    	VALUES(?, ?, ?, ?, ?, ?, ?, ?)";

    	// prepare statement
    	$stmt_in = $conn->prepare($sql_in);

    	// bind statement
    	$stmt_in->bind_param('isssssii', $sid, $name, $surname, $phone, $email, $title, $gender, $status);

    	$surname = $_REQUEST['last_name'];
    	$phone = $_REQUEST['phone'];
    	$email = $_REQUEST['email'];
    	$title = $_REQUEST['title'];
    	$gender = $_REQUEST['gender'];
    	$status = $_REQUEST['status'];

    	// execute statement
    	if($stmt_in->execute()) {
    		$flag = "success";
    	} else {
    		$flag = "fail";
    	}

    	// create a staff array to return
		$staff = array(
			"sid" => $sid,
			"first_name" => $name,
			"last_name" => $surname,
			"phone_number" => $phone,
			"e_mail" => $email,
			"job_title" => $title,
			"gender" => $gender,
			"status" => $status
		);

    	$staffs = array($staff);
		$resDict = array(
			"result" => $flag,
			"staff" => $staffs
		);

    	// close statements and connection to db
    	$stmt->close();
    	$stmt_in->close();
    	$conn->close();
    }

    $resJson = json_encode($resDict);
	echo $resJson;
?>