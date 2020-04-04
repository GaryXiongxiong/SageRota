$(document).ready(function () {
    let auth = suAuthenticate();
    if (auth.level !== 1) {
        window.location.href = "su_login.html";
    }
    loadNav(2, auth);
    CKEDITOR.replace("article_content");
    loadContent();

    $("#announcement-editor").submit(function (e) {
        e.preventDefault();
        let title = $("#article_title").val();
        let content = CKEDITOR.instances.article_content.getData();
        $.ajax({
            method: "POST",
            dataType: "json",
            url: "api/set_announcement.php",
            data: {title:title,content:content},
            success: function (result) {
                if (result.result === "success") {
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent();
                } else if (result.result === "fail") {
                    $("#assign-shift-popup").modal("hide");
                    $("#assign-shift-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Fail!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                }
            },
            error: function (e) {
                console.log(e);
                $("#assign-shift-popup").modal("hide");
                $("#assign-shift-form")[0].reset();
                $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                    "  <strong>Update Fail!</strong>" +
                    "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                    "<span aria-hidden='true'>&times;</span> " +
                    "</button> " +
                    "</div>");
            }
        });
    });

});

function loadContent() {
    $.ajax({
        url:"api/get_announcement.php",
        method: "GET",
        dataType: "json",
        success: function (result) {
            if(result.title!==undefined){
                $("#article_title").val(result.title);
                CKEDITOR.instances.article_content.setData(result.content);
            }
        }
    });
}
