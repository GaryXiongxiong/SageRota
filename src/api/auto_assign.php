<?php
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
//    if(!(isset($_SESSION['suid'])&&isset($_SESSION['level'])&&$_SESSION['level']==1)){
//        return;
//    }
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
// connect to database
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});

// check connection
if (!$conn) {
    echo "Connection error" . mysqli_connect_error();
}
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$rr= ( strtotime($end_date) - strtotime($start_date)) ;
// calculate number of weeks that contain and lie between start and end dates
 $numberOfDays = ( strtotime($end_date) - strtotime($start_date))/86400;
$numberOfWeeks = (($numberOfDays-$numberOfDays%7)/7+1);

// Define array of weeks, every week is represented by its start date
$allWeeks= array();
$d = $start_date;
for ($i = 0; $i < $numberOfWeeks; $i++) {
    array_push($allWeeks,$d);
    $d = date('Y-m-d', strtotime($d . ' + 7 days'));
} 

//Defining staff list; a staff is represented by its sid
$staffs = getAllStaff($conn);

$finalResult;
if(count($staffs)<1) 
{
    $flag = "fail";
}
else{
    $sdate = $start_date;
    $edate=date('Y-m-d', strtotime($end_date . ' + 8 days'));
    // Get all registered shifts in the selected period
    $oldShifts = getAllShifts($conn,$sdate, $edate);
    
    // Get empty shifts in the selected period, each shift represented by its start date
    $emptyWeeks=array_values(array_diff($allWeeks,array_column($oldShifts, "start_time")));
    if(count($emptyWeeks)<1) 
    {
        $flag = "fail";
    }
    else{
        $a = array_column($oldShifts, "staff_sid");
        $availableStaff=array_count_values($a);
        $max= max($availableStaff);
        $dd=array_keys($availableStaff);

        $finalStaffList=array_values(array_diff($staffs,array_keys($availableStaff)));

        for($i=1;$i<$max;$i++)
        {
            for($j=0;$j<count($staffs);$j++)
            {
                if (array_key_exists($staffs[$j],$availableStaff))
                {
                    if($availableStaff[$staffs[$j]]<=$i)
                    {
                        array_push($finalStaffList,$staffs[$j]);   
                    }
                }
                else{
                    array_push($finalStaffList,$staffs[$j]);
                }
            }
        }

        $sql_in = "INSERT INTO shift(staff_sid, start_time, end_time) 
            VALUES";
            $j=0;
            $check=false;
        for($i=0;$i<count($emptyWeeks);$i++)
        {
            $sd=date('Ymd', strtotime($emptyWeeks[$i]));
            $ed = date('Ymd', strtotime($emptyWeeks[$i] . ' + 6 days'));
            if($i!=count($emptyWeeks)-1)
            {
                $text="($finalStaffList[$j],$sd,$ed),";
            }
            else{
                $text="($finalStaffList[$j],$sd,$ed)";
            }
            
            $sql_in = "$sql_in$text";
            $j++;
            if($check)
            {
                if($j>=count($finalStaffList)) 
                $j=0;
            }
            else if($j>=count($finalStaffList))
            {
                $finalStaffList=$staffs;
                $j=0;
                $check=true;
            }
        }
        
        if ($conn->query($sql_in) === TRUE) {
            $flag="success";
        } else {
            $flag="fail";
        }
    }
}

$conn->close();
$resDict = array(
    "status" => $flag
);
$resJson = json_encode($resDict);
echo $resJson;


//=========================================================================
// Function to get all registered staff
//=========================================================================
function getAllStaff($conn){
    $query = $conn->prepare("SELECT sid FROM staff ORDER BY sid");
    $query->execute();
    $result = $query->get_result()->fetch_all();
    $query->close();
    //$conn->close();
    $staffs = array();
    if (count($result) == 0) {
        $staff = array();
    } else {
        for ($i = 0; $i < count($result); $i++) {
            $s= $result[$i][0];
            array_push($staffs,$s);
        }
    }
    return $staffs;
};


//=========================================================================
// Function to get all registered shifts in the selected period
//=========================================================================
function getAllShifts($conn,$start_date, $end_date){
    $query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,start_time,end_time,location,remark FROM shift left join staff on staff_sid=sid where start_time >= ? and end_time <= ?");
    $query->bind_param("ss", $start_date, $end_date);
    $query->execute();
    $result = $query->get_result()->fetch_all();
     $query->close();
     //$conn->close();
    $shifts = array();
    if (count($result) == 0) {
        $shift = array();
    } else {
        for ($i = 0; $i < count($result); $i++) {
            $shift = array(
                "id" => $result[$i][0],
                "staff_sid" => $result[$i][1],
                "staff_first_name" => $result[$i][2],
                "staff_last_name" => $result[$i][3],
                "start_time" => $result[$i][4],
                "end_time" => $result[$i][5],
                "location" => $result[$i][6],
                "remark" => $result[$i][7]);
            array_push($shifts,$shift);
        }
    }
   return $shifts;
};

?>