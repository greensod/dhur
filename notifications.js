document.addEventListener("DOMContentLoaded", function () {
    const notificationIcon = document.getElementById("notificationIcon");
    const dropdown = document.getElementById("notificationDropdown");

    notificationIcon.addEventListener("click", function () {
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });
});
