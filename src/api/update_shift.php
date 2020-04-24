<?php
/*
 * Amend information of given shift
 */
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
    if(!(isset($_SESSION['suid'])&&isset($_SESSION['level'])&&$_SESSION['level']==1)){
        return;
    }
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});

$id = $_REQUEST['id'];
$sid = $_REQUEST['staff_sid'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$location = $_REQUEST['location'];
$remark = $_REQUEST['remark'];

$query = $conn->prepare("SELECT * FROM shift where id=? and start_time=?");
$query->bind_param("is", $id, $start_date);
$query->execute();
$result1 = $query->get_result()->fetch_all();


if (count($result1) == 0) {
    $flag = "fail";
    $shift = array();
} else {

    $updateQuery = $conn->prepare("UPDATE timetable.shift SET staff_sid=?, location=?, remark=? WHERE id=?;");
    $updateQuery->bind_param("issi", $sid, $location, $remark, $id);
    if ($updateQuery->execute()) {
        //$updateQuery->execute();
        $flag = "success";
        $shift = array(
            "id" => $id,
            "staff_sid" => $sid,
            "start_time" => $start_date,
            "end_time" => $end_date,
            "location" => $location,
            "remark" => $remark
        );
    } else {
        $flag = "fail";
        $shift = array();
    }
}
$shifts = array($shift);
$resDict = array(
    "result" => $flag,
    "staff" => $shifts
);
$query->close();
$conn->close();
$resJson = json_encode($resDict);
echo $resJson;
