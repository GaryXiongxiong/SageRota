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
$itemsPerPage = 9;

$query = $conn->prepare("SELECT count(*) as count FROM shift where staff_sid = ? and end_time>now()");
$query->bind_param("i",$sid);
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
if (count($result) == 0) {
    $pageCount = -1;
    $shiftCount = -1;
} else {
    $shiftCount = $result[0][0];
    $pageCount = ceil($shiftCount / $itemsPerPage);
}
$resDict = array(
    "shiftCount" => $shiftCount,
    "pageCount" => $pageCount
);
$resJson = json_encode($resDict);
echo $resJson;
