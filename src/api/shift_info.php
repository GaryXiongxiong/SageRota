<?php

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
        "start_time" => $result[0][4],
        "end_time" => $result[0][5],
        "location" => $result[0][6],
        "remark" => $result[0][7]
    );
}
$shifts = array($shift);
$resDict = array(
    "shift" => $shifts
);
$resJson = json_encode($resDict);
echo $resJson;