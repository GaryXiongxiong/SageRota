$(document).ready(function () {
    let auth = authenticate();
    if (auth.level !== 0) {
        window.location.href = "login.html";
    }


    loadNav(1, auth);

    //Get page number from url
    let page = getUrlParam("p");
    if (page == null||page=== "") page = 1;

    loadContent(auth,page);
    loadPages(auth);
});

function loadPages(auth) {
    $("#pagination").html("<li class='page-item'><a class='page-link' href='#' id='previous-page'>Previous</a></li>\n" +
        "<li class='page-item'><a class='page-link' href='#' id='next-page'>Next</a></li>");

    $.ajax({
        url: "api/staff_later_shifts_count.php",
        method: "POST",
        data:{sid: auth.sid},
        dataType: "json",
        success: function (result) {
            let pages = result.pageCount;
            let page = parseInt(getUrlParam("p"));
            if (isNaN(page)) page = 1;
            for (let i = 1; i <= pages; i++) {
                let item = document.createElement("li");
                item.setAttribute("class", "page-item");
                if (i === page) {
                    $(item).addClass("active");
                }
                item.innerHTML = "<a class='page-link' href='?p=" + i + "'>" + i + "</a>";
                $("#pagination #next-page").parent(".page-item").before(item);
            }
            if (page === 1) {
                $("#pagination #previous-page").parent(".page-item").addClass("disabled");
            } else {
                $("#pagination #previous-page").attr("href", "?p=" + (page - 1));
            }
            if (page === pages) {
                $("#pagination #next-page").parent(".page-item").addClass("disabled");
            } else {
                $("#pagination #next-page").attr("href", "?p=" + (page + 1));
            }
        }
    })
}

function loadContent(auth,page) {
    $(".shift-info").remove();

    $.ajax({
        url: "api/staff_later_shifts.php",
        method: "POST",
        dataType: "json",
        data: {sid: auth.sid, page: page},
        beforeSend: function () {
            showLoading();
        },
        success: function (result) {
            let shifts = result.shift;
            if (shifts.length===0){
                $("#timetable-list").append('' +
                    '<div class="w-100 p-5 text-center shift-info">' +
                    '<h3 class="text-faded">' +
                    'Empty' +
                    '</h3>' +
                    '</div>')
            }
            shifts.forEach(appendShift);
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
            '                            <button class="btn btn-link p-0 staff-popover">' + shift.first_name + ' ' + shift.last_name + '</button>\n' +
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
            '                            <button class="btn btn-link p-0 staff-popover">' + shift.first_name + ' ' + shift.last_name + '</button>\n' +
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
        .attr("title",shift.first_name+" "+shift.last_name)
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



