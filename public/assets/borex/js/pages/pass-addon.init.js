document
    .getElementById("password-addon")
    .addEventListener("click", function () {
        var e = document.getElementById("password-input");
        "password" === e.type ? (e.type = "text") : (e.type = "password");
    });

    document
    .getElementById("password_confirmation-addon")
    .addEventListener("click", function () {
        var e = document.getElementById("password_confirmation-input");
        "password_confirmation" === e.type ? (e.type = "text") : (e.type = "password_confirmation");
    });

