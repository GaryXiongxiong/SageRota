<?php
/*
 * Request given shift information
 */
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
$query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,phone_number,e_mail,job_title,start_time,end_time,location,remark FROM shift,staff where start_time=? and staff_sid=sid");
$query->bind_param("s", $start_date);
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
if (count($result) == 0) {
    $shift = array();
} else {
    $shift = array(
        "id" => $result[0][0],
        "staff_sid" => $result[0][1],
        "staff_first_name" => $result[0][2],
        "staff_last_name" => $result[0][3],
        "staff_phone_number" => $result[0][4],
        "staff_e_mail" => $result[0][5],
        "staff_job_title" => $result[0][6],
        "start_time" => $result[0][7],
        "end_time" => $result[0][8],
        "location" => $result[0][9],
        "remark" => $result[0][10]
    );
}
$shifts = array($shift);
$resDict = array(
    "shift" => $shifts
);
$resJson = json_encode($resDict);
echo $resJson;
