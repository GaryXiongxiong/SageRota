<?php
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

// change start and end dates to include the two weeks at the boundaries
$sdate = date('Y-m-d', strtotime($start_date . ' - 7 days'));
$edate = date('Y-m-d', strtotime($end_date . ' + 14 days'));

// Get all registered shifts in the selected period
$newShifts = getAllShifts($conn, $sdate, $edate);
$assignedStaff = array_column($newShifts, "staff_sid", "start_time");
$startDatesOfShifts = array_column($newShifts, "start_time");

// create a list of adjacent shifts that have the same staff; only the first shift is added to the list
$j = count($newShifts) - 1;
$similarShifts = [];
for ($i = 0; $i < $j; $i++) {
    // remove the first shift in the $newShifts list and assign it to $shift
    $shift = array_shift($newShifts);
    // define next week start-date
    $nextWeek = date('Y-m-d', strtotime($startDatesOfShifts[$i] . ' + 7 days'));

    // if the current week and the next week have the same staff
    if ($assignedStaff[$startDatesOfShifts[$i]] == $assignedStaff[$nextWeek])
    {
        // add current shift to the $similarShifts list
        array_push($similarShifts, $shift);
    }
}

// close connection and prepare json-encoded return value
$conn->close();
$resDict = array(
    "shift" => $similarShifts
);
$resJson = json_encode($resDict);
echo $resJson;


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

?>
