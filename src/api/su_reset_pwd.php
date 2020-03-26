<?php
session_start();
if (!(isset($_SESSION['suid']) && isset($_SESSION['level']) && $_SESSION['level'] == 1)) {
    return;
}
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
$oldPwd = $_REQUEST['old_pwd'];
$newPwd = $_REQUEST['new_pwd'];
$suId = $_SESSION['suid'];
$query = $conn->prepare("UPDATE supervisor SET password=? where SuId=? and password=?");
$query->bind_param("sss", $newPwd, $suId, $oldPwd);
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
