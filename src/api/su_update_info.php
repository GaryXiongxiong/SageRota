<?php
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
//    if(!(isset($_SESSION['suid'])&&isset($_SESSION['level'])&&$_SESSION['level']==1)){
//        return;
//    }
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
$suId = $_SESSION['suid'];
$level = $_SESSION['level'];
$firstName = $_REQUEST['first_name'];
$lastName = $_REQUEST['last_name'];
$eMail = $_REQUEST['e_mail'];
$query = $conn->prepare("UPDATE supervisor SET e_mail=?,first_name=?,last_name=? where SuId=?");
$query->bind_param("sssi", $eMail, $firstName, $lastName, $suId);
$flag = "fail";
if ($query->execute()) {
    $flag = "success";
    $_SESSION['name'] = $firstName;
}
$query->close();
$conn->close();

$resDict = array(
    "result" => $flag,
);
$resJson = json_encode($resDict);
echo $resJson;
