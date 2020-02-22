<?php
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"},$conninfo->{"user"},$conninfo->{"password"},$conninfo->{"dbname"},$conninfo->{"port"});

//every page shows 9 rows of information
$query = $conn->prepare("SELECT count(*) as count FROM staff ");
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
if (count($result)==0){
    $pageCount = -1;
    $staffCount=-1;
}
else{
    $staffCount=$result[0][0];
    $pageCount=ceil($staffCount/9);
}
$resDict = array(
    "staffCount"=>$staffCount,
    "pageCount"=>$pageCount
);
$resJson = json_encode($resDict);
echo $resJson;
