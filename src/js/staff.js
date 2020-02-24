import {getUrlParam, showLoading, removeLoading} from './utils.js';

$(document).ready(function () {

    //Get page number from url
    let page = getUrlParam("p");

    if (page == null) page = 1;

    //Load staff list of page
    loadPages();
    loadContent(page);

    //Add staff form submission
    $("#add-staff-form").submit(function (e) {
        e.preventDefault();
        let data = $('#add-staff-form').serialize();
        let submitData = decodeURIComponent(data);
        $.ajax({
            method: "POST",
            dataType: "json",
            url: "api/add_staff.php",
            data: submitData,
            success: function (result) {
                if (result.result === "success") {
                    $("#add-staff-popup").modal("hide");
                    $("#add-staff-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Add Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent(page);
                    loadPages();
                } else if (result.result === "fail") {
                    $("#add-staff-popup").modal("hide");
                    $("#add-staff-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Add Fail! Please check if the staff is already exist</strong>" +
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

    //Edit staff form submission
    $("#edit-staff-form").submit(function (e) {
        e.preventDefault();
        let data = $('#edit-staff-form').serialize();
        let submitData = decodeURIComponent(data);
        $.ajax({
            method: "POST",
            dataType: "json",
            url: "api/update_staff.php",
            data: submitData,
            success: function (result) {
                if (result.result === "success") {
                    $("#edit-staff-popup").modal("hide");
                    $("#edit-staff-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Update Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent(page);
                } else if (result.result === "fail") {
                    $("#edit-staff-popup").modal("hide");
                    $("#edit-staff-form")[0].reset();
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

    //delete staff form submission
    $("#delete-staff-form").submit(function (e) {
        e.preventDefault();
        let data = $('#delete-staff-form').serialize();
        let submitData = decodeURIComponent(data);
        $.ajax({
            method: "POST",
            dataType: "json",
            url: "api/delete_staff.php",
            data: submitData,
            success: function (result) {
                if (result.result === "success") {
                    $("#delete-staff-popup").modal("hide");
                    $("#delete-staff-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                        "  <strong>Delete Success!</strong>" +
                        "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>" +
                        "<span aria-hidden='true'>&times;</span> " +
                        "</button> " +
                        "</div>");
                    loadContent(page);
                    loadPages();
                } else if (result.result === "fail") {
                    $("#delete-staff-popup").modal("hide");
                    $("#delete-staff-form")[0].reset();
                    $(".main_title").after("<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                        "  <strong>Delete Fail! Please check if the staff is assign to a shift.</strong>" +
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

});

function loadPages() {
    $("#pagination").html("<li class='page-item'><a class='page-link' href='#' id='previous-page'>Previous</a></li>\n" +
        "<li class='page-item'><a class='page-link' href='#' id='next-page'>Next</a></li>");

    $.ajax({
        url: "api/staff_list_count.php",
        method: "POST",
        dataType: "json",
        success(result) {
            let pages = result.pageCount;
            let page = parseInt(getUrlParam("p"));
            if (isNaN(page)) page = 1;
            for (let i = 1; i <= pages; i++) {
                let item = document.createElement("li");
                item.setAttribute("class","page-item");
                if(i===page){
                    $(item).addClass("active");
                }
                item.innerHTML="<a class='page-link' href='?p=" + i + "'>" + i + "</a>";
                $("#pagination #next-page").parent(".page-item").before(item);
            }
            if(page===1){
                $("#pagination #previous-page").parent(".page-item").addClass("disabled");
            }else{
                $("#pagination #previous-page").attr("href","?p="+(page-1));
            }
            if(page===pages){
                $("#pagination #next-page").parent(".page-item").addClass("disabled");
            }else{
                $("#pagination #next-page").attr("href","?p="+(page+1));
            }
        }
    })
}

function loadContent(page) {
    if (page == null) {
        page = getUrlParam("p");
        if (page == null) page = 1;
    }
    $(".staff-info").remove();
    $.ajax({
        url: "api/staff_list.php",
        method: "POST",
        dataType: "json",
        data: {page: page},
        beforeSend: function(){
          showLoading();
        },
        success(result) {
            let staffs = result.staff;
            if (staffs != null) {
                staffs.forEach(appendStaff);
            } else {
                console.log("Can not get staffs")
            }
            removeLoading();
            bindDeleteEvent();
            bindEditEvent();
        },
    });
}

function appendStaff(staff) {
    let staffContent =
        "<div class='col-lg-4 staff-info' data-id='" + staff.sid + "' data-name='" + staff.first_name + "'>" +
        "                <div class='card'>" +
        "                    <div class='card-body'>" +
        "                        <h5>" + staff.first_name + " " + staff.last_name + "</h5>" +
        "                        <p>" +
        "                            <i class='fas fa-phone-square-alt'></i>" +
        "                            " + staff.phone_number +
        "                            <br>" +
        "                            <i class='fas fa-envelope-square'></i>" +
        "                            " + staff.e_mail +
        "                            <br>" +
        "                            <i class='fas fa-briefcase'></i>" +
        "                            " + staff.job_title +
        "                        </p>" +
        "                        <span class='btn btn-primary btn-staff-edit'>Edit</span>" +
        "                        <span class='btn btn-danger btn-staff-delete'>Delete</span>" +
        "                    </div>" +
        "                </div>" +
        "            </div>";
    $("#staff-list").append(staffContent);
}

function bindDeleteEvent() {
    $(".btn-staff-delete").click(function () {
        let sid = $(this).parents(".staff-info").attr("data-id");
        let first_name = $(this).parents(".staff-info").attr("data-name");
        //bind delete popup
        $("#delete-staff-popup").modal("show");
        //bind confirm delete to send request
        $("#delete-staff-popup #confirm-delete").click(function () {
            //console.log("delete:"+sid+","+first_name);
            $("#delete-staff-form #delete_sid").val(sid);
            $("#delete-staff-form #delete_name").val(first_name);
        });
        $("#delete-staff-popup #cancel-delete").click(function () {
            //unbind the delete action after canceling
            $("#delete-staff-popup #delete-staff-form")[0].reset();
        });
        $("#delete-staff-popup .close").click(function () {
            //unbind the delete action after canceling
            $("#delete-staff-popup #delete-staff-form")[0].reset();
        })
    });
}

function bindEditEvent() {
    $(".btn-staff-edit").click(function () {
        let sid = $(this).parents(".staff-info").attr("data-id");
        let first_name = $(this).parents(".staff-info").attr("data-name");
        $("#edit-staff-popup").modal("show");
        $.ajax({
            url: "api/staff_info.php",
            dataType: "json",
            method: "POST",
            data: {sid: sid, name: first_name},
            success(result) {
                if (result.staff.length === 1) {
                    let staff = result.staff[0];
                    $("#edit-staff-form #edit_sid").val(staff.sid);
                    $("#edit-staff-form #edit_first_name").val(staff.first_name);
                    $("#edit-staff-form #edit_last_name").val(staff.last_name);
                    $("#edit-staff-form #edit_phone_number").val(staff.phone_number);
                    $("#edit-staff-form #edit_e_mail").val(staff.e_mail);
                    $("#edit-staff-form #edit_job_title").val(staff.job_title);
                    $("#edit-staff-form #edit_gender").val(staff.gender);
                    $("#edit-staff-form #edit_status").val(staff.status);
                } else {
                    alert("No such staff")
                }
            }
        })
    });
}
