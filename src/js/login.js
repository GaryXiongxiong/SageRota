$(document).ready(function () {
    let auth = authenticate();
    if (auth.level === 0) {
        window.location.href = "dashboard.html";
    }
    if($.cookie('acceptCookieUsage')!=="true"){
        $("#cookie-usage-modal").modal('show');
        $("#accept-cookie").click(function () {
            $.cookie('acceptCookieUsage',true,{expires:7,path:"/"});
        })
    }

    $("#login-form").submit(function (e) {
        e.preventDefault();
        let e_mail = $("#e_mail").val();
        let pwd = $("#pwd").val();
        pwd = sha256(pwd);
        $.ajax({
            url: "api/user_login.php",
            method: "post",
            dataType: "json",
            data: {e_mail:e_mail,pwd:pwd},
            success: function (result) {
                if(result.result==="success"){
                    if(result.level===0){
                        window.location.href = "dashboard.html";
                    }
                }
                else{
                    $("#fail-notice").removeClass("invisible");
                    $("#e_mail").addClass("border-danger");
                    $("#pwd").addClass("border-danger");
                }
            }

        })
    })

});
