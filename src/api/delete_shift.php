<?php
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
$start_date = $_REQUEST['start_date'];
$query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,start_time,end_time,location,remark FROM shift,staff where id=? and start_time=? and staff_sid=sid");
$query->bind_param("ss", $id, $start_date);
$query->execute();
$result = $query->get_result()->fetch_all();
if (count($result) == 0) {
    $flag = "fail";
    $shift = array();
} else {
    $shift = array(
        "id" => $result[0][0],
        "staff_sid" => $result[0][1],
        "staff_first_name" => $result[0][2],
        "staff_last_name" => $result[0][3],
        "start_time" => $result[0][4],
        "end_time" => $result[0][5],
        "location" => $result[0][6],
        "remark" => $result[0][7]
    );
    $delQuery = $conn->prepare("DELETE FROM shift where id=? and start_time=?");
    $delQuery->bind_param("is", $id, $start_date);
    if ($delQuery->execute()) {
        $flag = "success";
    } else {
        $flag = "fail";
    }
}
$shifts = array($shift);
$resDict = array(
    "result" => $flag,
    "shift" => $shifts
);
$query->close();
$conn->close();
$resJson = json_encode($resDict);
echo $resJson;
