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
$sql = "SELECT * FROM staff WHERE first_name = ? AND e_mail = ?";

// prepare statement
$stmt = $conn->prepare($sql);

$name = $_REQUEST['first_name'];
$email = $_REQUEST['e_mail'];

// bind statement
$stmt->bind_param('ss', $name, $email);

// execute statement
$stmt->execute();

// fetch results of query and store it as an associative array
$result = $stmt->get_result()->fetch_all();

if (count($result) != 0) {
	$flag = "fail";
	$staff = array();
} else {

	// sql query to insert values
	$sql_in = "INSERT INTO staff(first_name, last_name, phone_number, e_mail, job_title, gender, status) 
	VALUES(?, ?, ?, ?, ?, ?, ?)";

	// prepare statement
	$stmt_in = $conn->prepare($sql_in);

	$surname = $_REQUEST['last_name'];
	$phone = $_REQUEST['phone_number'];
	$title = $_REQUEST['job_title'];
	$gender = $_REQUEST['gender'];
	$status = $_REQUEST['status'];

	// bind statement
	$stmt_in->bind_param('sssssii', $name, $surname, $phone, $email, $title, $gender, $status);

	// execute statement
	if($stmt_in->execute()) {
		$flag = "success";
		//sql statement to get sid
		$sql_sid = "SELECT sid FROM staff WHERE first_name = ? AND e_mail = ?";
		
		// prepare statement
		$stmt_sid = $conn->prepare($sql_sid);

		// bind and execute statement
		$stmt_sid->bind_param('ss', $name, $email);
		$stmt_sid->execute();

		// get the result of query
		$result = $stmt_sid->get_result()->fetch_all();

		// create a staff array to return
		$staff = array(
			"sid" => $result[0][0],
			"first_name" => $name,
			"last_name" => $surname,
			"phone_number" => $phone,
			"e_mail" => $email,
			"job_title" => $title,
			"gender" => $gender,
			"status" => $status
		);

	} else {
		$flag = "fail";
	}

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