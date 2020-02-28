<?php
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});
$start_date = $_REQUEST['start_date'];
/**
 * get the start and end date of the week which contains the input date
 * @param string $inputDate  format：YYYY-MM-DD
 * @param int $weekStart   the day that as the begin of a week,0 is Sunday,1 is Monday,etc.
 * @return array array( "startDate ",  "endDate");
 */
function getAWeekTimeSlot($inputDate, $weekStart = 0) {
    if (! $inputDate){
        $inputDate = date ( "Y-m-d" );
    }
    $w = date ( "w", strtotime ( $inputDate ) ); //get the day in order,0 is Sunday,1 is Monday,etc.
    $dn = $w ? $w - $weekStart : 6; //days that should be minus
    $st = date ( "Y-m-d", strtotime ( "$inputDate  - " . $dn . "  days " ) );
    $en = date ( "Y-m-d", strtotime ( "$st  +6  days " ) );
    return array ($st, $en ); //return start date and end date of the week
}

$timeSlot1=getAWeekTimeSlot($start_date,1);//default Monday is the beginning of a week
$weekStartDate=$timeSlot1[0];
$end_date = date ( "Y-m-d", strtotime ( "$start_date  +56  days " ) );//default 9 weeks
$timeSlot2=getAWeekTimeSlot($end_date,1);
$weekEndDate=$timeSlot2[1];

$query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,start_time,end_time,location,remark FROM shift left join staff on staff_sid=sid where start_time >= ? and end_time <= ?");
$query->bind_param("ss", $weekStartDate, $weekEndDate);
$query->execute();
$result = $query->get_result()->fetch_all();
$query->close();
$conn->close();
$shifts=array();
if (count($result) == 0) {
    $shift = array();
} else {
    for($i=0;$i<count($result);$i++){
        $shift = array(
            "id"=>$result[$i][0],
            "staff_sid"=>$result[$i][1],
            "staff_first_name"=>$result[$i][2],
            "staff_last_name"=>$result[$i][3],
            "start_time"=>$result[$i][4],
            "end_time"=>$result[$i][5],
            "location"=>$result[$i][6],
            "remark"=>$result[$i][7]);
        $shifts[] = $shift;
    }
}
$resDict = array(
    "shift" => $shifts
);
$resJson = json_encode($resDict);
echo $resJson;
