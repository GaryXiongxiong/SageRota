$(document).ready(function () {
    let auth = authenticate();
    if(auth.level!==0){
        window.location.href="login.html";
    }

    loadNav(0,auth);

    loadContent(auth);

    $("#edit-profile").click(function () {
        $.ajax({
            url: "api/staff_info.php",
            dataType: "json",
            method: "POST",
            data: {sid: auth.sid, name: auth.name},
            success: function (result) {
                if (result.staff.length === 1) {
                    let staff = result.staff[0];
                    $("#edit-profile-form #edit_sid").val(staff.sid);
                    $("#edit-profile-form #edit_first_name").val(staff.first_name);
                    $("#edit-profile-form #edit_last_name").val(staff.last_name);
                    $("#edit-profile-form #edit_phone_number").val(staff.phone_number);
                    $("#edit-profile-form #edit_e_mail").val(staff.e_mail);
                    $("#edit-profile-form #edit_job_title").val(staff.job_title);
                    $("#edit-profile-form #edit_gender").val(staff.gender);
                    $("#edit-profile-form #edit_status").val(staff.status);
                } else {
                    alert("No such staff")
                }
            }
        });
        $("#edit-profile-popup").modal("show");
    });

    $("#reset-pwd").click(function () {
        $("#reset_pwd_popup").modal("show");
    });

    $("#edit-profile-form").submit(function (e) {
        e.preventDefault();
        let data = $('#edit-profile-form').serialize();
        let submitData = decodeURIComponent(data);
        $.ajax({
            method: "POST",
            dataType: "json",
            url: "api/update_staff.php",
            data: submitData,
            success: function (result) {
                if (result.result === "success") {
                    $("#edit-profile-popup").modal("hide");
                    $("#edit-profile-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent();
                } else if (result.result === "fail") {
                    $("#edit-profile-popup").modal("hide");
                    $("#edit-profile-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Fail! Please check if the phone number or e-mail is already exist</strong>" +
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
        let unconfirmedNotice=$("#unconfirmed-notice");
        if (newPwd !== cfmNewPwd) {
            unconfirmedNotice.removeClass("invalid-feedback");
            return;
        }
        unconfirmedNotice.addClass("invalid-feedback");
        $.ajax({
            url: "api/user_reset_pwd.php",
            method: "post",
            dataType: "json",
            data:{old_pwd: sha256(curPwd),new_pwd:sha256(newPwd)},
            success: function (result) {
                if(result.result==="success"){
                    unconfirmedNotice.addClass("invalid-feedback");
                    alert("Password has been updated, please re-login.");
                    $.removeCookie("PHPSESSID", {path: "/"});
                    window.location.href = "login.html";
                }else{
                    $("#invalid-notice").removeClass("invalid-feedback");
                }
            }
        });
    });

    $("#feedback-form").submit(function (e) {
       e.preventDefault();
       let sid = auth.sid;
       let content = $("#feedback-content").val();
       $.ajax({
           url:"api/add_feedback.php",
           method:"POST",
           datatype:"json",
           data:{sid:sid,content:content},
           success: function (result) {
                if(result.result==="success"){
                    $("#feedback-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Feedback has been sent!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent();
                    $('html,body').animate({ scrollTop: 0 }, 500);
                }
                else{
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Feedback sent fail!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent();
                    $('html,body').animate({ scrollTop: 0 }, 500);
                }
           }
       })
    });
});

function loadContent(auth) {
    showLoading();
    let curDate = new Date();
    let dayOfWeek = curDate.getDay();
    if (dayOfWeek === 0) dayOfWeek = 7;
    curDate.setDate(curDate.getDate() - dayOfWeek + 1);
    let startDate = curDate.format("YYYY-MM-DD");
    //Load announcement
    $.ajax({
        url:"api/get_announcement.php",
        method: "GET",
        dataType: "json",
        success: function (result) {
           if(result.title!==undefined){
               let time = new Date(Date.parse(result.timestamp));
               $("#anno-title").text(result.title);
               $("#anno-subtitle").text(result.author_fn+" "+result.author_ln+" - "+time.format("hh:mmTT DS MMM. YYYY"));
               $("#anno-content").html(result.content);
           }
           else{
               $("#anno-title").text("Currently No Announcement").siblings().remove();
           }
        }
    });
    //Load current on-call
    $.ajax({
        url: "api/shift_info.php",
        method: "POST",
        dataType: "json",
        data: {start_date: startDate},
        success: function (result) {
            if (result.shift[0].id !== undefined) {
                let shift = result.shift[0];
                $("#cur-staff").text(shift.staff_first_name+" "+shift.staff_last_name);
                $("#cur-e-mail").text(shift.staff_e_mail);
                $("#cur-phone").text(shift.staff_phone_number);
                $("#cur-title").text(shift.staff_job_title);
            } else {
                $("#cur-staff").text("Currently None On-Call").addClass("text-secondary").siblings().remove();
            }
            removeLoading()
        }
    });
}
