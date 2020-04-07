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
$sid = $_REQUEST['sid'];
$name = $_REQUEST['name'];
$query = $conn->prepare("SELECT * FROM staff where sid=? and first_name=?");
$query->bind_param("is", $sid, $name);
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
if (count($result) == 0) {
    $staff = array();
} else {
    $staff = array(
        "sid" => $result[0][0],
        "first_name" => $result[0][1],
        "last_name" => $result[0][2],
        "phone_number" => $result[0][3],
        "e_mail" => $result[0][4],
        "job_title" => $result[0][5],
        "gender" => $result[0][6],
        "status" => $result[0][7]
    );
}
$staffs = array($staff);
$resDict = array(
    "staff" => $staffs
);
$resJson = json_encode($resDict);
echo $resJson;
