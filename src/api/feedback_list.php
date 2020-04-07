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
$page = $_REQUEST['page'];
//every page shows 9 rows of information
$itemsPerPage=9;
if ($page == "all") {
    $query = $conn->prepare("SELECT fid,first_name,last_name,phone_number,e_mail,job_title,timestamp,content,unread FROM staff,feedback where staff.sid=feedback.from_sid order by timestamp desc");
} else {
    $startRow = $itemsPerPage * ($page - 1);
    $query = $conn->prepare("SELECT fid,first_name,last_name,phone_number,e_mail,job_title,timestamp,content,unread FROM staff,feedback where staff.sid=feedback.from_sid order by timestamp desc limit ?,?");
    $query->bind_param("ii", $startRow,$itemsPerPage);
}
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
$fbList = array();
if (count($result) == 0) {
    $feedback = array();
} else {
    for ($i = 0; $i < count($result); $i++) {
        $feedback = array(
            "fid" => $result[$i][0],
            "first_name" => $result[$i][1],
            "last_name" => $result[$i][2],
            "phone_number" => $result[$i][3],
            "e_mail" => $result[$i][4],
            "job_title" => $result[$i][5],
            "timestamp" => $result[$i][6],
            "content" => $result[$i][7],
            "unread" => $result[$i][8]);
        $fbList[] = $feedback;
    }
}
$resDict = array(
    "feedback" => $fbList
);
$resJson = json_encode($resDict);
echo $resJson;
