function toggleBarVisibility() {
    var e = document.getElementById("bar_blank");
    e.style.display = (e.style.display == "block") ? "none" : "block";
}

function createRequestObject() {
    var http;
    if (navigator.appName == "Microsoft Internet Explorer") {
        http = new ActiveXObject("Microsoft.XMLHTTP");
    }
    else {
        http = new XMLHttpRequest();
    }
    return http;
}

function sendRequest() {
    var http = createRequestObject();
    http.open("GET", "/uploadprogress", false);
    http.onreadystatechange = function () { handleResponse(http); };
    http.send(null);
}

function handleResponse(http) {
    var response;
    if (http.readyState == 4) {
        response = http.responseText;
        document.getElementById("bar_color").style.width = response + "%";
        document.getElementById("status").innerHTML = response + "%";
        if (response < 100) {
            setTimeout("sendRequest()", 1000);
        }
        else {
            toggleBarVisibility();
            document.getElementById("status").innerHTML = "Done.";
        }
    }
}

function startUpload() {
    toggleBarVisibility();
    setTimeout("sendRequest()", 1000);
}

(function () {
    document.getElementById("sbpform").onsubmit = startUpload;
})();
