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
$fid = $_REQUEST['fid'];

$query = $conn->prepare("update feedback set unread=0 where fid=?");
$query->bind_param("i", $fid);
$flag = "fail";

if ($query->execute()) {
    if ($query->affected_rows == 1) {
        $flag = "success";
    }
}

$resDict = array(
    "result" => $flag,
);

$query->close();
$conn->close();

$resJson = json_encode($resDict);
echo $resJson;
