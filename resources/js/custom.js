$(document).ready(function() {
    function MakeAJAXRequest(URL, formName) {
        // e.preventDefault();
        $.ajax({
            type: "POST",
            url: URL,
            cache: false,
            data: $(formName).serializeArray(),
            beforeSend: function() {
                $("#resultTable").find("tr:gt(0)").remove();
            },
            success: function(response) {
                console.log(response);
                response = $.parseJSON(response);
                console.log(response.status)
                console.log(Object.keys(response.urls).length);
                if (response.status == 0 && Object.keys(response.urls).length > 0) {
                    // Add this response to the table
                    $.each(response.urls, function(index, result) {
                        $("#resultTable").append("<tr><td>" + index + "</td><td>" + result + "</td></tr>");
                    })
                }
            }
        });
    }
    // Create the new text box if clicked on +/Add button
    $("#formURLs").on("click", ".btnAdd", function(e) {
        e.preventDefault();
        $(".textBox:last").clone().insertAfter(".textBox:last");
        $(".textBox:last input").val("");
    })
    // handle the AJAX request if clicked on Submit button
    $("#formURLs").submit(function() {
        MakeAJAXRequest("process.php", "#formURLs");
        // eventSource.close();
        return false;
    })
})
// Event Stream
/*if (!!window.EventSource) {
    var source = new EventSource("stream.php");
} else {
    console.log("Server side events are not supported");
}
source.addEventListener("message", function(e) {
    console.log(e.data);
}, false);
source.addEventListener("open", function(e) {
    // Connection opened
}, false);
source.addEventListener("error", function(e) {
    if (e.readyState == EventSource.CLOSED) {
        console.log("Connection was closed");
    }
}, false);*/