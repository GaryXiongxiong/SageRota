<?php
/*
 * Get authorization status for staff users
 */
session_start();
header("Content-Type:Application/json;charset=utf-8");
$flag = "";
$sid = 0;
$level = -1;
$name = "";
if (isset($_SESSION['sid']) && isset($_SESSION['level']) && $_SESSION['level'] == 0) {
    $flag = "success";
    $sid = $_SESSION['sid'];
    $level = $_SESSION['level'];
    $name = $_SESSION['name'];
} else {
    $flag = "fail";
}
$resDict = array(
    "result" => $flag,
    "sid" => $sid,
    "level" => $level,
    "name" => $name
);
$resJson = json_encode($resDict);
echo $resJson;
