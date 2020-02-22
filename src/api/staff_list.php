<?php
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"},$conninfo->{"user"},$conninfo->{"password"},$conninfo->{"dbname"},$conninfo->{"port"});
$page = $_REQUEST['page'];
$startRow=9*($page-1);
//every page shows 9 rows of information
$query = $conn->prepare("SELECT * FROM staff order by sid asc limit ?,9");
$query->bind_param("i",$startRow);
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
$staffs=array();
if (count($result)==0){
    $staff = array();
}
else{
    for($i=0;$i<count($result);$i++){
        $staff = array(
            "sid"=>$result[$i][0],
            "first_name"=>$result[$i][1],
            "last_name"=>$result[$i][2],
            "phone_number"=>$result[$i][3],
            "e_mail"=>$result[$i][4],
            "job_title"=>$result[$i][5],
            "gender"=>$result[$i][6],
            "status"=>$result[$i][7]);
        $staffs[] = $staff;
    }
}
$resDict = array(
    "staff"=>$staffs
);
$resJson = json_encode($resDict);
echo $resJson;
