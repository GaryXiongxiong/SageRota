<?php
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
if (!((isset($_SESSION['suid']) && isset($_SESSION['level']) && $_SESSION['level'] == 1) || (isset($_SESSION['sid']) && isset($_SESSION['level']) && $_SESSION['level'] == 0))) {
    return;
}
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,start_time,end_time,location,remark,e_mail,phone_number,job_title,gender FROM shift left join staff on staff_sid=sid where start_time >= ? and end_time <= ? order by start_time");
$query->bind_param("ss", $start_date, $end_date);
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
$shifts = array();
if (count($result) == 0) {
    $shift = array();
} else {
    for ($i = 0; $i < count($result); $i++) {
        $shift = array(
            "id" => $result[$i][0],
            "staff_sid" => $result[$i][1],
            "staff_first_name" => $result[$i][2],
            "staff_last_name" => $result[$i][3],
            "start_time" => $result[$i][4],
            "end_time" => $result[$i][5],
            "location" => $result[$i][6],
            "remark" => $result[$i][7],
            "e_mail" => $result[$i][8],
            "phone_number" => $result[$i][9],
            "job_title" => $result[$i][10],
            "gender" => $result[$i][11],
            );
        $shifts[] = $shift;
    }
}
$resDict = array(
    "shift" => $shifts
);
$resJson = json_encode($resDict);
echo $resJson;
