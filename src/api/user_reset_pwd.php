<?php
/*
 * Reset password for staff user
 */
session_start();
if (isset($_SESSION['sid']) && isset($_SESSION['level']) && $_SESSION['level'] == 0) {
    header("Content-Type:Application/json;charset=utf-8");
    $datainfo = file_get_contents("data.json");
    $conninfo = json_decode($datainfo);
    $conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
    $oldPwd = $_REQUEST['old_pwd'];
    $newPwd = $_REQUEST['new_pwd'];
    $sid = $_SESSION['sid'];
    $query = $conn->prepare("UPDATE staff SET password=? where sid=? and password=?");
    $query->bind_param("sss", $newPwd, $sid, $oldPwd);
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
}
if (isset($_SESSION['suid']) && isset($_SESSION['level']) && $_SESSION['level']== 1){
    header("Content-Type:Application/json;charset=utf-8");
    $datainfo = file_get_contents("data.json");
    $conninfo = json_decode($datainfo);
    $conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
    $newPwd = $_REQUEST['new_pwd'];
    $sid = $_REQUEST['sid'];
    $query = $conn->prepare("UPDATE staff SET password=? where sid=?");
    $query->bind_param("si", $newPwd, $sid);
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
}
else{
    return;
}

