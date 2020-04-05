<?php
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
//    if(!(isset($_SESSION['sid'])&&isset($_SESSION['level'])&&$_SESSION['level']==1)){
//        return;
//    }
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});

$sid = $_REQUEST['sid'];

$query = $conn->prepare("select id,staff_sid,start_time,end_time,location,remark,first_name,last_name from shift,staff where staff_sid =? and staff_sid=sid and start_time> now() order by id limit 1");
$query->bind_param("i", $sid);
$query->execute();
$result = $query->get_result()->fetch_all();

if (count($result) == 0) {
    $flag = "fail";
    $shift = array();
} else {
    $flag = "success";
    $shift = array(
        "id" => $result[0][0],
        "staff_sid" => $result[0][1],
        "start_time" =>$result[0][2],
        "end_time" => $result[0][3],
        "location" => $result[0][4],
        "remark" => $result[0][5],
        "first_name"=>$result[0][6],
        "last_name"=>$result[0][7]
    );
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
