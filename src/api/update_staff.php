<?php
    header("Content-Type:Application/json;charset=utf-8");
    $datainfo = file_get_contents("data.json");
    $conninfo = json_decode($datainfo);
    $conn = new mysqli($conninfo->{"host"},$conninfo->{"user"},$conninfo->{"password"},$conninfo->{"dbname"},$conninfo->{"port"});
    $sid = $_REQUEST['sid'];
    $fname = $_REQUEST['first_name'];
    $lname = $_REQUEST['last_name'];
    $phoneNumber = $_REQUEST['phone_number'];
    $email = $_REQUEST['e_mail'];
    $jobTitle = $_REQUEST['job_title'];
    $gender = $_REQUEST['gender'];
    $status = $_REQUEST['status'];

    $query = $conn->prepare("SELECT * FROM staff where sid=? and first_name=?");
    $query->bind_param("is", $sid, $fname);
    $query->execute();
    $result = $query->get_result()->fetch_all();
    if (count($result) == 0) {
        $flag = "fail";
        $staff = array();
    } else {
        
        $updateQuery = $conn->prepare("UPDATE staff SET first_name=?, last_name=?, phone_number=?, e_mail=?, 
        job_title=?, gender=?, status=? WHERE sid=?;");
        $updateQuery->bind_param("sssssiii",$fname,$lname,$phoneNumber,$email,$jobTitle,$gender,$status,$sid);
        if ($updateQuery->execute()) {
            //$updateQuery->execute();
            $flag = "success";
            $staff = array(
                "sid" => $sid,
                "first_name" => $fname,
                "last_name" => $lname,
                "phone_number" => $phoneNumber,
                "e_mail" => $email,
                "job_title" => $jobTitle,
                "gender" => $gender,
                "status" => $status
            );
        } else {
            $flag = "fail";
        }
    }
    $staffs = array($staff);
    $resDict = array(
        "result" => $flag,
        "staff" => $staffs
    );
    $query->close();
    $conn->close();
    $resJson = json_encode($resDict);
    echo $resJson;