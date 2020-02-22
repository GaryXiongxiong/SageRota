function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}
//TODO: Put loading into modal
function showLoading(path) {
    $(path).after("    <div class='row justify-content-center' id='loading'>\n" +
        "        <button class='btn btn-primary' disabled>\n" +
        "    <span class='spinner-border spinner-border-sm'></span>\n" +
        "    Loading..\n" +
        "    </button>\n" +
        "    </div>")
}

function removeLoading() {
    $("#loading").remove();
}
export {getUrlParam,showLoading,removeLoading};