<?php
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
if(!(isset($_SESSION['suid'])&&isset($_SESSION['level'])&&$_SESSION['level']==1)){
    return;
}
$suid = $_SESSION['suid'];
$title = $_REQUEST['title'];
$content = $_REQUEST['content'];
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
$query = $conn->prepare("INSERT INTO announcement (title, content, suid) VALUES(?,?,?)");
$query->bind_param("ssi",$title,$content,$suid);
$flag="fail";
if ($query->execute()) {
    if ($query->affected_rows == 1) {
        $flag = "success";
    }
}
$query->close();
$conn->close();
$result = array(
    "result"=>$flag
);
$resJson = json_encode($result);
echo $resJson;
