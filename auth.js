document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".toggle-pass-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const input = document.getElementById(btn.dataset.target);
            const icon = btn.querySelector("i");
            if (!input) return;

            input.type = input.type === "password" ? "text" : "password";
            if (icon) {
                icon.classList.toggle("fa-eye");
                icon.classList.toggle("fa-eye-slash");
            }
        });
    });
});
