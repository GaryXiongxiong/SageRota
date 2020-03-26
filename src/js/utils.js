function getUrlParam(name) {
    let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    let r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

function showLoading() {
    $("#loading-popup").modal("show");
}

function removeLoading() {
    $("#loading-popup").modal("hide");
}

/**
 * Get session to determine if user has logged in.
 * @returns {{level: number, name: string, suid: number}} When the user has not logged in, level will be -1
 */
function suAuthenticate() {
    let level=-1;
    let suid=0;
    let name="";
    $.ajax({
        url:"api/su_auth.php",
        method:"GET",
        dataType:"json",
        async: false,
        success: function(result){
            if(result.result==="success"){
                level = result.level;
                suid = result.suid;
                name = result.name;
            }
        },
    });
    return {level:level,suid:suid,name:name};
}

function loadNav(index,auth){
    $("navigation").load("components/nav.html",function () {
        if(index>=0){
            $(".nav-item").eq(index).children("a").addClass("active");
        }
        $(".user-center-link").text(auth.name);
        $(".user-center-link").attr("href","su_profile.html");
        $(".logout-btn").click(function () {
            $.removeCookie("PHPSESSID",{path:"/"});
            window.location.href="su_login.html";
        });
    })
}
