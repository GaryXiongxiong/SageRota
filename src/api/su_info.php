<?php
session_start();
if (!(isset($_SESSION['suid']) && isset($_SESSION['level']) && $_SESSION['level'] == 1)) {
    return;
}
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
$suId = $_SESSION['suid'];
$firstName = $_SESSION['name'];
$level = $_SESSION['level'];
$query = $conn->prepare("SELECT SuId,e_mail,first_name,last_name,supervisor.level FROM supervisor where SuId=? and first_name=? and level=?");
$query->bind_param("isi", $suId, $firstName, $level);
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();

$flag = "fail";
$eMail = "";
$lastName = "";
if (count($result) == 1) {
    $suId = $result[0][0];
    $eMail = $result[0][1];
    $firstName = $result[0][2];
    $lastName = $result[0][3];
    $level = $result[0][4];
    $flag = "success";
}
$resDict = array(
    "result" => $flag,
    "suid" => $suId,
    "e_mail" => $eMail,
    "first_name" => $firstName,
    "last_name" => $lastName,
    "level" => $level,
);
$resJson = json_encode($resDict);
echo $resJson;
