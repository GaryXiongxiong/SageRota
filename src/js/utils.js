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
