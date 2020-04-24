<?php
/*
 * List staff of given page
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
$page = $_REQUEST['page'];
//every page shows 9 rows of information
$itemsPerPage=9;
if ($page == "all") {
    $query = $conn->prepare("SELECT * FROM staff");
} else {
    $startRow = $itemsPerPage * ($page - 1);
    $query = $conn->prepare("SELECT * FROM staff order by sid asc limit ?,?");
    $query->bind_param("ii", $startRow,$itemsPerPage);
}
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
$staffs = array();
if (count($result) == 0) {
    $staff = array();
} else {
    for ($i = 0; $i < count($result); $i++) {
        $staff = array(
            "sid" => $result[$i][0],
            "first_name" => $result[$i][1],
            "last_name" => $result[$i][2],
            "phone_number" => $result[$i][3],
            "e_mail" => $result[$i][4],
            "job_title" => $result[$i][5],
            "gender" => $result[$i][6],
            "status" => $result[$i][7]);
        $staffs[] = $staff;
    }
}
$resDict = array(
    "staff" => $staffs
);
$resJson = json_encode($resDict);
echo $resJson;
