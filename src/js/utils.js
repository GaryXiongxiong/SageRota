function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}

function showLoading() {
    $("#loading-popup").modal("show");
}

function removeLoading() {
    $("#loading-popup").modal("hide");
}
export {getUrlParam,showLoading,removeLoading};