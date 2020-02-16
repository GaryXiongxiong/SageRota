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

$sid = $_REQUEST['staff_sid'];
$start_date = $_REQUEST['start_date'];	

// sql query to be sent to database
$sql = "SELECT * FROM shift WHERE staff_sid = ? AND start_time = ?";

// prepare statement
$stmt = $conn->prepare($sql);

// bind statement
$stmt->bind_param('is', $sid, $start_date);

// execute statement
$stmt->execute();

// fetch results of query and store it as an associative array
$result = $stmt->get_result()->fetch_all();

// initiate array to be returned
$shift = array();

if (count($result) != 0) {
	$flag = "fail";
} else {
	$location = $_REQUEST['location'];
	$remark = $_REQUEST['remark'];
	$end_date = date('Y-m-d', strtotime($start_date . ' + 6 days'));

	// sql query to insert values
	$sql_in = "INSERT INTO shift(staff_sid, start_time, end_time, location, remark) 
	VALUES(?, ?, ?, ?, ?)";

	// prepare statement
	$stmt_in = $conn->prepare($sql_in);

	// bind statement
	$stmt_in->bind_param('issss', $sid, $start_date, $end_date, $location, $remark);

	// execute statement
	if($stmt_in->execute()) {
		$flag = "success";
		
		// sql statement to get id
		$sql_id = "SELECT id FROM shift WHERE staff_sid = ? AND start_time = ?";
		// sql statement to get staff's first name and last name
		$sql_n = "SELECT first_name, last_name FROM staff WHERE sid = ?";

		// prepare statements
		$stmt_id = $conn->prepare($sql_id);
		$stmt_n = $conn->prepare($sql_n);

		// bind and execute statements
		$stmt_id->bind_param('is', $sid, $start_date);
		$stmt_id->execute();

		// get the result of queries
		if ($result_id = $stmt_id->get_result()) {
			$result_id = $result_id->fetch_all();
		} else {
			echo "Error while getting ID";
		}

		$stmt_n->bind_param('i', $sid);
		$stmt_n->execute();

		if ($result_n = $stmt_n->get_result()) {
			$result_n = $result_n->fetch_all();
		} else {
			echo "Error while getting NAME";
		}

		// create a staff array to return
		$shift = array(
			"id" => $result_id[0][0],
			"staff_sid" => $sid,
			"staff_first_name" => $result_n[0][0],
			"staff_last_name" => $result_n[0][1],
			"start_time" => $start_date,
			"end_time" => $end_date,
			"location" => $location,
			"remark" => $remark,
		);

	} else {
		$flag = "fail";
	}
}
$shifts = array($shift);
$resDict = array(
	"result" => $flag,
	"shift" => $shifts
);

// close statements and connection to db
$stmt->close();
$conn->close();

$resJson = json_encode($resDict);
echo $resJson;
?>