if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}

const togglePassword = function (element) {
    const input = element.previousSibling.previousSibling;
    if (input.type == "password") {
        input.type = "text";
        element.src = element.src.slice(0,-8) + "hide.png";
    } else if (input.type == "text") {
        input.type = "password";
        element.src = element.src.slice(0,-8) + "show.png";
    }
}