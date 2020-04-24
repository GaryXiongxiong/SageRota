<?php
/*
 * Auto assign staff to given period with overwriting
 */
session_start();
//    This part is used to control unauthenticated request, uncomment these before deploy
    if(!(isset($_SESSION['suid'])&&isset($_SESSION['level'])&&$_SESSION['level']==1)){
        return;
    }
header("Content-Type:Application/json;charset=utf-8");
$datainfo = file_get_contents("data.json");
$conninfo = json_decode($datainfo);
// connect to database
$conn = new mysqli($conninfo->{"host"}, $conninfo->{"user"}, $conninfo->{"password"}, $conninfo->{"dbname"}, $conninfo->{"port"});

// check connection
if (!$conn) {
    echo "Connection error" . mysqli_connect_error();
}

// Get start and end dates of the selected period from the front-end
// both start and end dates are 'Mondays'
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

// calculate number of days that lie between start and end dates
$numberOfDays = (strtotime($end_date) - strtotime($start_date)) / 86400;
// calculate number of weeks included in the selected period
$numberOfWeeks = (($numberOfDays - $numberOfDays % 7) / 7 + 1);

// Define array of all included weeks; every week is represented by its start date
$allWeeks = array();
$weekStartDate = $start_date;
for ($i = 0; $i < $numberOfWeeks; $i++) {
    array_push($allWeeks, $weekStartDate);
    $weekStartDate = date('Y-m-d', strtotime($weekStartDate . ' + 7 days'));
}

//Defining a list of all staff registered in the system; a staff is represented by its ID (sid)
$staffs = getAllStaff($conn);

// check if there is no staff in the database
if (count($staffs) < 1) {
    $flag = "fail";
} else {
    // extend end-date by 7 days to include the last week in the selected period
    $sdate = $start_date;
    $edate = date('Y-m-d', strtotime($end_date . ' + 7 days'));

    // delete all shifts between start and end dates
    deleteShifts($conn, $sdate, $edate);

    // prepare query to insert new staff into all weeks in the selected period
    $sql_in = "INSERT INTO shift(staff_sid, start_time, end_time) 
            VALUES";
    $j = 0;
    $check = false;
    for ($i = 0; $i < count($allWeeks); $i++) {
        $sd = date('Ymd', strtotime($allWeeks[$i]));
        $ed = date('Ymd', strtotime($allWeeks[$i] . ' + 6 days'));
        // check if $j is not the index of the last week
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

    // execute the query and check if execution was successful
    if ($conn->query($sql_in) === TRUE) {
        $flag = "success";
        if (count($staffs) > 1)
        {
            // change start and end dates to include the two weeks at the boundaries of the selected period
            $sdate = date('Y-m-d', strtotime($sdate . ' - 7 days'));
            $edate2 = $edate;
            $edate = date('Y-m-d', strtotime($edate . ' + 7 days'));

            // get list of new registered shifts
            $newShifts = getAllShifts($conn, $sdate, $edate);
            // get associated array of shifts' start dates and corresponding staff sid
            $newAssignedStaff = array_column($newShifts, "staff_sid", "start_time");
            // list of start dates of the new shifts
            $startDatesOfNewShifts = array_column($newShifts, "start_time");
            // list of staff sids assigned in the new shifts
            $a2 = array_column($newShifts, "staff_sid");

            // these if statements are to eleminate staff appointed in boundary weeks
            if ($startDatesOfNewShifts[0] == $sdate)
                $temp = array_shift($a2);
            if ($startDatesOfNewShifts[count($startDatesOfNewShifts) - 1] == $edate2)
                $temp = array_pop($a2);

            // get list of new staff with their frequency of appearance
            $availableStaff2 = array_count_values($a2);
            // get the greatest number of staff appearance
            $max2 = max($availableStaff2);

            // get list of staff who were not assigned in any shift
            $finalStaffList2 = array_values(array_diff($staffs, array_keys($availableStaff2)));
            // this for-loop is to create the list of staff that would make even and fiar auto-staff-assignment
            for ($i = 1; $i <= $max2; $i++) {
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

            // add the list of all staff to the final staff list2 defined above in the for-loop
            $finalStaffList2 = array_merge($finalStaffList2, $staffs);
            // the following parameters and for-loop are for replacing staff who appear in two consecutive weeks
            $finalStaffList3 = $finalStaffList2;
            $j = 0;
            $check = true;
            $test2 = 0;
            for ($i = 0; $i < count($newShifts) - 1; $i++) {
                //if staff at week[$i] == staff at week[$i+1]
                if ($newAssignedStaff[$startDatesOfNewShifts[$i]] == $newAssignedStaff[$startDatesOfNewShifts[$i + 1]])//if $startDatesOfNewShifts[$i].staff==$startDatesOfNewShifts[$i+1].staff
                {
                    // if week[i] is one of the empty weeks (before auto assigning staff)
                    if (in_array($startDatesOfNewShifts[$i], $allWeeks)) {
                        // loop while staff at week[$i] is the same staff at week[$i+1] and $check = true
                        while ($newAssignedStaff[$startDatesOfNewShifts[$i]] == $newAssignedStaff[$startDatesOfNewShifts[$i + 1]] && $check) {
                            // check if the staff at the current shift is same staff at the previous shift; this is done after
                            // confirming that current shift is not shift[0]
                            if ($i > 0) {
                                $check2 = ($newAssignedStaff[$startDatesOfNewShifts[$i - 1]] != $finalStaffList3[$j]);
                            } else {
                                $check2 = true;
                            }
                             // check if current staff sid is greater than -1 and staff at current shift is different from staff at previous shift
                            if ($finalStaffList3[$j] > -1 && $check2) {
                                // replace staff at current shift with staff[$j]
                                $start_time = date('Ymd', strtotime($startDatesOfNewShifts[$i]));
                                $query = $conn->prepare("UPDATE timetable.shift SET staff_sid=$finalStaffList3[$j] WHERE start_time=$start_time");

                                if ($query->execute()) {
                                    $flag = "success";
                                } else {
                                    $flag = $conn->error;
                                }
                                $query->close();

                                // replace old staff with staff[$j] in $newAssignedStaff list
                                $newAssignedStaff[$startDatesOfNewShifts[$i]] = $finalStaffList3[$j];
                            }
                            $j++;
                             // if $j exceeded $finalStaffList3 index, change $check to false to get out of the while loop
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
                    elseif (in_array($startDatesOfNewShifts[$i + 1], $allWeeks)) {
                        while ($newAssignedStaff[$startDatesOfNewShifts[$i]] == $newAssignedStaff[$startDatesOfNewShifts[$i + 1]] && $check) {
                            if ($finalStaffList3[$j] > -1) {
                                // replace staff at shift[i+1] with staff[$j]
                                $start_time = date('Ymd', strtotime($startDatesOfNewShifts[$i + 1]));
                                $query = $conn->prepare("UPDATE timetable.shift SET staff_sid=$finalStaffList3[$j] WHERE start_time=$start_time");

                                if ($query->execute()) {
                                    $flag = "success2";
                                } else {
                                    $flag = $conn->error;
                                }
                                $query->close();

                                // replace old staff at shift[i+1] with staff[$j] in $newAssignedStaff list
                                $newAssignedStaff[$startDatesOfNewShifts[$i + 1]] = $finalStaffList3[$j];
                            }
                            $j++;
                            // if $j exceeded $finalStaffList3 index, change $check to false to get out of the while loop
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
                    // reset $check to true and $j to zero
                    $check = true;
                    $j = 0;
                }
            }
        }
    }
    // if staff auto-assignment querry was unseccesseful
    else {
        $flag = $conn->error;
    }

}

// close connection and prepare the value to be returned encoded in json
$conn->close();
$resDict = array(
    "status" => $flag
);
$resJson = json_encode($resDict);
echo $resJson;


/**
 * This function returns all registered staff
 * @var $conn database connection
 * @return $staff all registered staff in the database (table staff); ordered by their sid
 */
function getAllStaff($conn)
{
    // prepare select query
    $query = $conn->prepare("SELECT sid FROM staff ORDER BY sid");
    // execute query
    $query->execute();
    // fetch all rows and columns
    $result = $query->get_result()->fetch_all();
    // close query
    $query->close();
    // put arrays of staff in one array (array of arrays)
    // each array of staff has only one element and that is sid
    $staffs = array();
    if (count($result) == 0) {
        $staff = array();
    } else {
        for ($i = 0; $i < count($result); $i++) {
            $s = $result[$i][0];
            array_push($staffs, $s);
        }
    }
    // return the array of staff
    return $staffs;
}

;


/**
 * This function returns all registered shifts in the selected period
 * @var $conn database connection
 * @var $start_date period start-date (first Monday)
 * @var $end_date period end-date (Monday after last Sunday)
 * @return $shifts all registered shifts in the selected period
 */
function getAllShifts($conn, $start_date, $end_date)
{
    // prepare select query
    $query = $conn->prepare("SELECT id,staff_sid,first_name,last_name,start_time,end_time,location,remark FROM shift left join staff on staff_sid=sid where start_time >= ? and end_time <= ? ORDER BY start_time");
    $query->bind_param("ss", $start_date, $end_date);
    // execute query
    $query->execute();
    // fetch all results
    $result = $query->get_result()->fetch_all();
    // close query
    $query->close();
    // put arrays of shifts in one array (array of arrays)
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
    // return all shifts between start and end dates
    return $shifts;
}

;

/**
 * This function is to delete all shifts in the selected period
 * @var $conn database connection
 * @var $start_date period start-date (first Monday)
 * @var $end_date period end-date (Monday after last Sunday)
 */
function deleteShifts($conn, $start_date, $end_date)
{
    // prepare delete-query
    $query = $conn->prepare("DELETE FROM timetable.shift where start_time >= ? and end_time <= ?");
    $query->bind_param("ss", $start_date, $end_date);
    // execute query
    $query->execute();
    // close query
    $query->close();
}

?>
