$(document).ready(function () {
    let auth = suAuthenticate();
    if (auth.level !== 1) {
        window.location.href = "su_login.html";
    }
    loadNav(3, auth);
    let page = getUrlParam("p");
    if (page == null) page = 1;
    loadPages();
    loadContent(page);
});

function loadContent(page) {
    $.ajax({
        url: "api/feedback_list.php",
        method: "POST",
        datatype: "json",
        data: {page: page},
        beforeSend: function () {
            showLoading()
        },
        success: function (result) {
            let fbList = result.feedback;
            if (fbList != null) {
                fbList.forEach(function (feedback) {
                    let feedbackContent =
                        '<div class="card feedback mb-3" id="fb-' + feedback.fid + '">\n' +
                        '        <div class="card-header">\n' +
                        '            <button class="btn btn-link feedback-btn" type="button" data-toggle="collapse" data-target="#fb-' + feedback.fid + ' .collapse">\n' +
                        '                ' + feedback.first_name + ' ' + feedback.last_name + '  ' + new Date(Date.parse(feedback.timestamp)).format('hh:mmTT DS MMM. YYYY') + '\n' +
                        '            </button>\n' +
                        '        </div>\n' +
                        '        <div class="collapse">\n' +
                        '            <div class="card-body fb-content">\n' +
                        '                <p>\n' +
                        '                <i class="fas fa-phone-square-alt"></i>\n' +
                        '                ' + feedback.phone_number + '\n' +
                        '                <br>\n' +
                        '                <i class="fas fa-envelope-square"></i>\n' +
                        '                ' + feedback.e_mail + '\n' +
                        '                <br>\n' +
                        '                <i class="fas fa-briefcase"></i>\n' +
                        '                ' + feedback.job_title + '\n' +
                        '                </p>\n' +
                        '                <p class="mb-0">\n' +
                        '                    ' + feedback.content + '' +
                        '                </p>\n' +
                        '            </div>\n' +
                        '        </div>\n' +
                        '    </div>';
                    $("#feedback-list").append(feedbackContent);
                    if (feedback.unread === 1) {
                        $("#fb-" + feedback.fid + " .feedback-btn").append('<span class="badge badge-pill badge-primary unread-badge">Unread</span>');
                    }
                    removeLoading();
                })
            } else {
                console.log("Can not get feedback list")
            }
            bindReadEvent();
        }
    });

}

function loadPages() {
    $("#pagination").html("<li class='page-item'><a class='page-link' href='#' id='previous-page'>Previous</a></li>\n" +
        "<li class='page-item'><a class='page-link' href='#' id='next-page'>Next</a></li>");

    $.ajax({
        url: "api/feedback_count.php",
        method: "POST",
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
    });
}

function bindReadEvent() {
    $(".unread-badge").parents(".feedback-btn").one("click", function () {
        let fid = $(this).parents(".feedback").attr("id").match(/\d+/g)[0];
        $.ajax({
            url: "api/read_feedback.php",
            method: "POST",
            data: {fid: fid},
            datatype: "json",
            success: function (result) {
                if(result.result==="success"){
                    $("#fb-"+fid).find(".badge").remove();
                }
            }
        })
    });
}
