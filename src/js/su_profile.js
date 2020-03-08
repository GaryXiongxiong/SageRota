$(document).ready(function () {
    let auth = suAuthenticate();
    if (auth.level !== 1) {
        window.location.href = "su_login.html";
    }
    loadNav(-1, auth);
    loadContent();

    $("#edit_name").click(function () {
        $("#edit-profile-popup").modal("show");
    });
    $("#edit_e_mail").click(function () {
        $("#edit-profile-popup").modal("show");
    });

    $("#reset_password").click(function () {
        $("#reset_pwd_popup").modal("show");
    });

    $("#edit-profile-form").submit(function (e) {
        e.preventDefault();
        let data = $("#edit-profile-form").serialize();
        let submitData = decodeURIComponent(data);
        $.ajax({
            method: "POST",
            dataType: "json",
            url: "api/su_update_info.php",
            data: submitData,
            success: function (result) {
                if (result.result === "success") {
                    $("#edit-profile-popup").modal("hide");
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent();
                } else if (result.result === "fail") {
                    $("#add-staff-popup").modal("hide");
                    $("#add-staff-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Fail! Please check input</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                }
            },
            error: function () {
                console.log("Fail");
            }
        });
    });

    $("#reset_pwd_form").submit(function (e) {
        e.preventDefault();
        let curPwd = $("#reset_pwd_form #old_pwd").val();
        let newPwd = $("#reset_pwd_form #new_pwd").val();
        let cfmNewPwd = $("#reset_pwd_form #cfm_pwd").val();
        if (newPwd !== cfmNewPwd) {
            $("#unconfirmed-notice").removeClass("invalid-feedback");
            return;
        }
        $("#unconfirmed-notice").addClass("invalid-feedback");
        $.ajax({
            url: "api/su_reset_pwd.php",
            method: "post",
            dataType: "json",
            data:{old_pwd: sha256(curPwd),new_pwd:sha256(newPwd)},
            success: function (result) {
                if(result.result==="success"){
                    $("#unconfirmed-notice").addClass("invalid-feedback");
                    alert("Password has been updated, please re-login.");
                    $.removeCookie("PHPSESSID", {path: "/"});
                    window.location.href = "su_login.html";
                }else{
                    $("#invalid-notice").removeClass("invalid-feedback");
                }
            }
        });
    })
});

function loadContent() {
    $.ajax({
        url: "api/su_info.php",
        method: "get",
        dataType: "json",
        success: function (result) {
            if (result.result === "success") {
                $("#name").text(result.first_name + " " + result.last_name);
                $("#first_name").val(result.first_name);
                $("#last_name").val(result.last_name);
                $("#email").text(result.e_mail);
                $("#e_mail").val(result.e_mail);

            }
        }
    })
}
