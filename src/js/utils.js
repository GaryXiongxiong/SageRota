/**
 * Get parameter in url
 * @param name Name of the parameter
 * @returns {string|null} The value of the parameter
 */
function getUrlParam(name) {
    let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    let param = window.location.search.substr(1).match(reg);
    if (param != null) return unescape(param[2]);
    return null;
}

/**
 * Display loading modal
 */
function showLoading() {
    $("#loading-popup").modal("show");
}

/**
 * Remove loading modal
 */
function removeLoading() {
    $("#loading-popup").modal("hide");
}

/**
 * Get session to determine if supervisor user has logged in.
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

/**
 * Get session to determine if staff user has logged in.
 * @returns {{level: number, name: string, sid: number}} When the user has not logged in, level will be -1
 */
function authenticate() {
    let level=-1;
    let sid=0;
    let name="";
    $.ajax({
        url:"api/user_auth.php",
        method:"GET",
        dataType:"json",
        async: false,
        success: function(result){
            if(result.result==="success"){
                level = result.level;
                sid = result.sid;
                name = result.name;
            }
        },
    });
    return {level:level,sid:sid,name:name};
}

/**
 * Load navigation
 * @param index The index of current page in navigation bar
 * @param auth The authentication info of current user
 */
function loadNav(index,auth){
    if(auth.level===1){
        $("navigation").load("components/su-nav.html",function () {
            if(index>=0){
                $(".nav-item").eq(index).children("a").addClass("active");
            }
            $(".user-center-link").text(auth.name).attr("href","su_profile.html");
            $(".logout-btn").click(function () {
                $.removeCookie("PHPSESSID",{path:"/"});
                window.location.href="su_login.html";
            });
            $.ajax({
                url:"api/unread_feedback_count.php",
                method:"POST",
                datatype:"json",
                success: function (result) {
                    let unread = result.unread;
                    if(unread>0){
                        $("[href='feedback.html']").append('<span class="badge badge-pill badge-primary ml-1">'+unread+'</span>\n');
                    }
                }
            });
        })
    }
    if(auth.level===0){
        $("navigation").load("components/u-nav.html",function () {
            if(index>=0){
                $(".nav-item").eq(index).children("a").addClass("active");
            }
            $(".user-center-link").text(auth.name);
            $(".logout-btn").click(function () {
                $.removeCookie("PHPSESSID",{path:"/"});
                window.location.href="login.html";
            });
        })
    }
}

/**
 * Generate a random password
 * @returns {string} The generated password
 */
function randomPwd(){
    let charset = "ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678";
    let length = charset.length;
    let rndPwd="";
    for(let i =0;i<8;i++){
        rndPwd+= charset.charAt(Math.floor(Math.random()*length));
    }
    return rndPwd;
}
