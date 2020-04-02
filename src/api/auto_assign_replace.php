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

$rr = (strtotime($end_date) - strtotime($start_date));
// calculate number of weeks that contain and lie between start and end dates
$numberOfDays = (strtotime($end_date) - strtotime($start_date)) / 86400;
$numberOfWeeks = (($numberOfDays - $numberOfDays % 7) / 7 + 1);

// Define array of weeks, every week is represented by its start date
$allWeeks = array();
$d = $start_date;
for ($i = 0; $i < $numberOfWeeks; $i++) {
    array_push($allWeeks, $d);
    $d = date('Y-m-d', strtotime($d . ' + 7 days'));
}

//Defining staff list; a staff is represented by its sid
$staffs = getAllStaff($conn);

if (count($staffs) < 1) {
    $flag = "fail";
} else {
    $sdate = $start_date;
    $edate = date('Y-m-d', strtotime($end_date . ' + 7 days'));
    // Get all registered shifts in the selected period
    deleteShifts($conn, $sdate, $edate);

    $sql_in = "INSERT INTO shift(staff_sid, start_time, end_time) 
            VALUES";
    $j = 0;
    $check = false;
    for ($i = 0; $i < count($allWeeks); $i++) {
        $sd = date('Ymd', strtotime($allWeeks[$i]));
        $ed = date('Ymd', strtotime($allWeeks[$i] . ' + 6 days'));
        if ($i != count($allWeeks) - 1) {
            $text = "($staffs[$j],$sd,$ed),";
        } else {
            $text = "($staffs[$j],$sd,$ed)";
        }

        $sql_in = "$sql_in$text";
        $j++;
        if ($j >= count($staffs))
            $j = 0;
    }

    if ($conn->query($sql_in) === TRUE) {
        $flag = "success";

        $sdate = date('Y-m-d', strtotime($sdate . ' - 7 days'));
        $edate2 = $edate;
        $edate = date('Y-m-d', strtotime($edate . ' + 7 days'));
        $newShifts = getAllShifts($conn, $sdate, $edate);
        $cc2 = array_column($newShifts, "staff_sid", "start_time");
        $c2 = array_column($newShifts, "start_time");
        $a2 = array_column($newShifts, "staff_sid");

        if ($c2[0] == $sdate)
            $temp = array_shift($a2);
        if ($c2[count($c2) - 1] == $edate2)
            $temp = array_pop($a2);
        //$ff=$a2;
        //$temp=array_shift($a2);
        //$temp=array_pop($a2);
        //$a3=$a2;
        $availableStaff2 = array_count_values($a2);
        $max2 = max($availableStaff2);
        $dd2 = array_keys($availableStaff2);

        $finalStaffList2 = array_values(array_diff($staffs, array_keys($availableStaff2)));

        for ($i = 1; $i < $max2; $i++) {
            for ($j = 0; $j < count($staffs); $j++) {
                if (array_key_exists($staffs[$j], $availableStaff2)) {
                    if ($availableStaff2[$staffs[$j]] <= $i) {
                        array_push($finalStaffList2, $staffs[$j]);
                    }
                } else {
                    array_push($finalStaffList2, $staffs[$j]);
                }
            }
        }

        $finalStaffList2 = array_merge($finalStaffList2, $staffs);
        $finalStaffList3 = $finalStaffList2;
        $j = 0;
        $check = true;
        $test2 = 0;
        for ($i = 0; $i < count($newShifts) - 1; $i++) {
            //if staff at week[$i] == staff at week[$i+1]
            if ($cc2[$c2[$i]] == $cc2[$c2[$i + 1]])//if $c2[$i].staff==$c2[$i+1].staff
            {
                // if week[i] is one of the empty weeks (before auto assigning staff)
                if (in_array($c2[$i], $allWeeks)) {
                    while ($cc2[$c2[$i]] == $cc2[$c2[$i + 1]] && $check) {
                        if ($i > 0) {
                            $check2 = ($cc2[$c2[$i - 1]] != $finalStaffList3[$j]);
                        } else {
                            $check2 = true;
                        }

                        if ($finalStaffList3[$j] > -1 && $check2) {
                            //update Shift
                            $start_time = date('Ymd', strtotime($c2[$i]));
                            $query = $conn->prepare("UPDATE timetable.shift SET staff_sid=$finalStaffList3[$j] WHERE start_time=$start_time");

                            if ($query->execute()) {
                                $flag = "success";
                            } else {
                                $flag = $conn->error;
                            }
                            $query->close();

                            //replace
                            $cc2[$c2[$i]] = $finalStaffList3[$j];
                        }
                        $j++;
                        if ($j >= count($finalStaffList3))
                            $check = false;
                    }

                    if ($check) {
                        // add the assigned staff to the end of the staff array and then change the value of its current index to -1
                        array_push($finalStaffList3, $finalStaffList3[$j - 1]);
                        $finalStaffList3[$j - 1] = -1;
                        $j = 0;
                    }
                } // if week[i+1] is one of the empty weeks (before auto assigning staff)
                elseif (in_array($c2[$i + 1], $allWeeks)) {
                    while ($cc2[$c2[$i]] == $cc2[$c2[$i + 1]] && $check) {
                        if ($finalStaffList3[$j] > -1) {
                            //updateShift($conn,$c2[1], $finalStaffList3[$j]);
                            $start_time = date('Ymd', strtotime($c2[$i + 1]));
                            $query = $conn->prepare("UPDATE timetable.shift SET staff_sid=$finalStaffList3[$j] WHERE start_time=$start_time");

                            if ($query->execute()) {
                                $flag = "success2";
                            } else {
                                $flag = $conn->error;
                            }
                            $query->close();

                            //replace
                            $cc2[$c2[$i + 1]] = $finalStaffList3[$j];
                        }
                        $j++;
                        if ($j >= count($finalStaffList3))
                            $check = false;
                    }
                    if ($check) {
                        // add the assigned staff to the end of the staff array and then change the value of its current index to -1
                        array_push($finalStaffList3, $finalStaffList3[$j - 1]);
                        $finalStaffList3[$j - 1] = -1;
                        $j = 0;
                    }
                }
                $check = true;
                $j = 0;
            }
        }

    } else {
        $flag = $conn->error;
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
function getAllStaff($conn)
{
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
            $s = $result[$i][0];
            array_push($staffs, $s);
        }
    }
    return $staffs;
}

;


//=========================================================================
// Function to get all registered shifts in the selected period
//=========================================================================
function getAllShifts($conn, $start_date, $end_date)
{
    $query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,start_time,end_time,location,remark FROM shift left join staff on staff_sid=sid where start_time >= ? and end_time <= ? ORDER BY start_time");
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
            array_push($shifts, $shift);
        }
    }
    return $shifts;
}

;

//=========================================================================
// Function to update a shift with a new staff
//=========================================================================
function updateShift($conn, $start_date, $staff_sid)
{
    $query = $conn->prepare("UPDATE timetable.shift SET staff_sid=$staff_sid WHERE start_time=$start_date;");
    $query->execute();
    //$result = $query->get_result()->fetch_all();
    $query->close();
}

//=========================================================================
// Function to delete all shifts
//=========================================================================
function deleteShifts($conn, $start_date, $end_date)
{
    $query = $conn->prepare("DELETE FROM timetable.shift where start_time >= ? and end_time <= ?");
    $query->bind_param("ss", $start_date, $end_date);
    $query->execute();
    //$result = $query->get_result()->fetch_all();
    $query->close();
}

?>
