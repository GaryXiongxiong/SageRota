<?php
/*
 * Count the number of staff
 */
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
    if(!(isset($_SESSION['suid'])&&isset($_SESSION['level'])&&$_SESSION['level']==1)){
        return;
    }
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});

$itemsPerPage = 9;
$query = $conn->prepare("SELECT count(*) as count FROM staff ");
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
if (count($result) == 0) {
    $pageCount = -1;
    $staffCount = -1;
} else {
    $staffCount = $result[0][0];
    $pageCount = ceil($staffCount / $itemsPerPage);
}
$resDict = array(
    "staffCount" => $staffCount,
    "pageCount" => $pageCount
);
$resJson = json_encode($resDict);
echo $resJson;
