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
$query = $conn->prepare("SELECT title,content,timestamp,first_name,last_name FROM announcement,supervisor where announcement.suid = supervisor.SuId ORDER BY timestamp DESC LIMIT 1");
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
if (count($result) == 0) {
    $announcement = array();
} else {
    $announcement = array(
        "title" => $result[0][0],
        "content" => $result[0][1],
        "timestamp" => $result[0][2],
        "author_fn" => $result[0][3],
        "author_ln" => $result[0][4],
    );
}

$resJson = json_encode($announcement);
echo $resJson;
