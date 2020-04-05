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
$page = $_REQUEST['page'];
//every page shows 9 rows of information
$itemsPerPage = 9;

if ($page == "all") {
    $query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,start_time,end_time,location,remark,e_mail,phone_number,job_title,gender FROM shift,staff where staff_sid = ? and sid=staff_sid and end_time > now() order by start_time");
    $query->bind_param("i", $sid);
} else {
    $startRow = $itemsPerPage * ($page - 1);
    $query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,start_time,end_time,location,remark,e_mail,phone_number,job_title,gender FROM shift,staff where staff_sid = ? and sid=staff_sid and end_time > now() order by id asc limit ?,?");
    $query->bind_param("iii", $sid, $startRow, $itemsPerPage);
}
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
$shift = array();
if (count($result) == 0) {
    $shift = array();
} else {
    for ($i = 0; $i < count($result); $i++) {
        $shift = array(
            "id" => $result[0][0],
            "staff_sid" => $result[0][1],
            "first_name" => $result[0][2],
            "last_name" => $result[0][3],
            "start_time" => $result[0][4],
            "end_time" => $result[0][5],
            "location" => $result[0][6],
            "remark" => $result[0][7],
            "e_mail" => $result[0][8],
            "phone_number" => $result[0][9],
            "job_title" => $result[0][10],
            "gender" => $result[0][11]
        );
        $shifts[] = $shift;
    }
}
$resDict = array(
    "shift" => $shifts
);
$resJson = json_encode($resDict);
echo $resJson;
