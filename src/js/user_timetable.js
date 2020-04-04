$(document).ready(function () {
    let auth = authenticate();
    if (auth.level !== 0) {
        window.location.href = "login.html";
    }


    loadNav(1, auth);

    loadContent();
    loadPages();
    loadGoto();
});

function loadStartDate() {
    let startDate = getUrlParam("start_date");
    let date;
    let dayOfWeek;
    if (startDate === null || startDate === "") {
        date = new Date();
    } else {
        date = new Date(startDate);
    }
    dayOfWeek = date.getDay();
    if (dayOfWeek === 0) dayOfWeek = 7;
    date.setDate(date.getDate() - dayOfWeek + 1);
    return date;
}

function loadPages() {
    let startDate = loadStartDate();
    let previousDate = new Date(startDate);
    let nextDate = new Date(startDate);
    previousDate.setDate(startDate.getDate() - 63);
    nextDate.setDate(startDate.getDate() + 63);
    $("#previous-page").attr("href", "?start_date=" + previousDate.format("YYYY-MM-DD"));
    $("#next-page").attr("href", "?start_date=" + nextDate.format("YYYY-MM-DD"));
}

function loadGoto() {
    $(".datepicker").datepicker({
        defaultValue: getUrlParam("start_date"),
        weekStart: 1
    });
    $("#goto-btn").click(function () {
        window.location.href = '?start_date=' + $(".datepicker").datepicker('getDate');
    });
    $(".datepicker_start").datepicker({
        defaultValue: new Date()
    });
    $(".datepicker_end").datepicker({
        defaultValue: new Date()
    });
}

function loadContent() {
    let startDate = getPreviousMonday(loadStartDate());
    let endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + 62);
    $(".shift-info").remove();

    $.ajax({
        url: "api/timetable_list.php",
        method: "POST",
        dataType: "json",
        data: {start_date: startDate.format("YYYY-MM-DD"), end_date: endDate.format("YYYY-MM-DD")},
        beforeSend: function () {
            showLoading();
        },
        success: function (result) {
            let shifts = result.shift;
            let curDate = startDate;
            for (let i = 0; i < 9; i++) {
                while (shifts.length !== 0 && new Date(shifts[0].start_time).getDay() !== 1) shifts.shift();
                if (shifts.length !== 0 && curDate.format("YYYY-MM-DD") === shifts[0].start_time) {
                    appendShift(shifts.shift());
                } else {
                    appendEmptyShift(curDate);
                }
                curDate.setDate(curDate.getDate() + 7);
            }
            loadCurrentShiftStyle();
            removeLoading();
        }
    });
}

function appendShift(shift) {
    let shiftContent;
    if (shift.location == null || shift.location === "") {
        shiftContent = '<div class="col-lg-4 col-md-6 shift-info" data-id="' + shift.id + '" data-date="' + shift.start_time + '">\n' +
            '                <div class="card">\n' +
            '                    <div class="card-body">\n' +
            '                        <h5>' + new Date(shift.start_time).format("DS MMM") + " - "
            + ' ' + new Date(shift.end_time).format("DS MMM") + '</h5>\n' +
            '                        <p>\n' +
            '                            <i class="fas fa-user"></i>\n' +
            '                            <button class="btn btn-link p-0 staff-popover">' + shift.staff_first_name + ' ' + shift.staff_last_name + '</button>\n' +
            '                            <br>\n' +
            '                            <br>\n' +
            '                        </p>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '            </div>';
    } else {
        shiftContent = '<div class="col-lg-4 col-md-6 shift-info" data-id="' + shift.id + '" data-date="' + shift.start_time + '">\n' +
            '                <div class="card">\n' +
            '                    <div class="card-body">\n' +
            '                        <h5>' + new Date(shift.start_time).format("DS MMM") + " - "
            + ' ' + new Date(shift.end_time).format("DS MMM") + '</h5>\n' +
            '                        <p>\n' +
            '                            <i class="fas fa-user"></i>\n' +
            '                            <button class="btn btn-link p-0 staff-popover">' + shift.staff_first_name + ' ' + shift.staff_last_name + '</button>\n' +
            '                            <br>\n' +
            '                            <i class="fas fa-map-marked-alt"></i>\n' +
            '                            ' + shift.location + '\n' +
            '                            <br>\n' +
            '                        </p>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '            </div>';
    }
    $("#timetable-list").append(shiftContent);
    $("[data-id='"+shift.id+"']").find(".staff-popover")
        .attr("data-toggle","popover")
        .attr("title",shift.staff_first_name+" "+shift.staff_last_name)
        .attr("data-html",true)
        .attr("data-content","" +
            "                            <i class='fas fa-phone-square-alt'></i>" +
            "                            " + shift.phone_number +
            "                            <br>" +
            "                            <i class='fas fa-envelope-square'></i>" +
            "                            " + shift.e_mail +
            "                            <br>" +
            "                            <i class='fas fa-briefcase'></i>" +
            "                            " + shift.job_title
        )
        .attr("data-trigger","focus")
        .attr("data-placement","bottom");
        $('[data-toggle="popover"]').popover();
}

function appendEmptyShift(date) {
    let endDate = new Date(date);
    endDate.setDate(date.getDate() + 6);
    let shiftContent = '<div class="col-lg-4 col-md-6 shift-info invalid" data-id="" data-date="' + date.format("YYYY-MM-DD") + '">\n' +
        '                <div class="card">\n' +
        '                    <div class="card-body">\n' +
        '                        <h5>' + date.format("DS MMM") + ' - ' + endDate.format("DS MMM") + '</h5>\n' +
        '                        <p>\n' +
        '                            <i class="fas fa-user"></i>\n' +
        '                            <strong>Unassigned</strong>\n' +
        '                            <br>\n' +
        '                            <br>\n' +
        '                        </p>\n' +
        '                    </div>\n' +
        '                </div>\n' +
        '            </div>';
    $("#timetable-list").append(shiftContent);
}

function loadCurrentShiftStyle() {
    let date = new Date();
    let dayOfWeek = date.getDay();
    if (dayOfWeek === 0) dayOfWeek = 7;
    date.setDate(date.getDate() - dayOfWeek + 1);
    let dateStr = date.format("YYYY-MM-DD");
    $("[data-date=" + dateStr + "]").children(".card").addClass("current-shift");
}

//Function to get nearest previous Moday
function getPreviousMonday(date) {
    let currentDate = new Date(date);
    let day = currentDate.getDay();
    var z;
    // The difference between two consecutive days is 86400000 mseconds
    if (day > 0) {
        z = currentDate - (day - 1) * 86400000;
    } else {
        z = currentDate - 6 * 86400000;
    }
    return new Date(z);
}


