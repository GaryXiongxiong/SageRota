<?php
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
//    if(!(isset($_SESSION['sid'])&&isset($_SESSION['level'])&&$_SESSION['level']==0)){
//        return;
//    }
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
// connect to database
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});

// check connection
if (!$conn) {
    echo "Connection error" . mysqli_connect_error();
}

$content = $_REQUEST['content'];
$sid = $_SESSION['sid'];
// sql query to insert values
$sql = "INSERT INTO feedback (from_sid, content) VALUES(?, ?)";

// prepare statement
$stmt = $conn->prepare($sql);

// bind statement
$stmt->bind_param('is', $sid,$content);

// execute statement
if ($stmt->execute()) {
    $flag = "success";

} else {
    $flag = "fail";
}

// close statements and connection to db
$stmt->close();
$conn->close();

$resDict = array(
    "result" => $flag,
);

$resJson = json_encode($resDict);
echo $resJson;
