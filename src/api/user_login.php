<?php
/*
 * Login staff user
 */
session_start();
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
$email = $_REQUEST['e_mail'];
$pwd = $_REQUEST['pwd'];
$query = $conn->prepare("SELECT sid,first_name FROM staff where e_mail=? and password=?");
$query->bind_param("ss", $email, $pwd);
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();

$flag = "fail";
$sid = 0;
$level = -1;
$name = "";
if (count($result) == 1) {
    $sid = $result[0][0];
    $level = 0;
    $name = $result[0][1];
    $_SESSION['sid'] = $sid;
    $_SESSION['level'] = $level;
    $_SESSION['name'] = $name;
    $flag = "success";
}
$resDict = array(
    "result" => $flag,
    "sid" => $sid,
    "level" => $level,
    "name" => $name
);
$resJson = json_encode($resDict);
echo $resJson;
