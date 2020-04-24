<?php
/*
 * Get authorization status for supervisor users
 */
session_start();
header("Content-Type:Application/json;charset=utf-8");
$flag = "";
$suId = 0;
$level = -1;
$name = "";
if (isset($_SESSION['suid']) && isset($_SESSION['level']) && $_SESSION['level'] == 1) {
    $flag = "success";
    $suId = $_SESSION['suid'];
    $level = $_SESSION['level'];
    $name = $_SESSION['name'];
} else {
    $flag = "fail";
}
$resDict = array(
    "result" => $flag,
    "suid" => $suId,
    "level" => $level,
    "name" => $name
);
$resJson = json_encode($resDict);
echo $resJson;
