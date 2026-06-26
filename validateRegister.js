document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registerForm");

    // Inputs
    const username = form.querySelector("input[name='username']");
    const email = form.querySelector("input[name='email']");
    const phone = form.querySelector("input[name='phone']");
    const password = form.querySelector("input[name='password']");
    const confirmPassword = form.querySelector("input[name='confirm_password']");

    // Error divs
    const usernameError = form.querySelector("#usernameError");
    const emailError = form.querySelector("#emailError");
    const phoneError = form.querySelector("#phoneError");
    const passwordError = form.querySelector("#passwordError");
    const confirmPasswordError = form.querySelector("#confirmPasswordError");
    const formError = form.querySelector("#formError");

    form.addEventListener("submit", function (e) {
        // Clear previous errors
        usernameError.textContent = "";
        emailError.textContent = "";
        phoneError.textContent = "";
        passwordError.textContent = "";
        confirmPasswordError.textContent = "";
        formError.textContent = "";

        username.classList.remove("input-error");
        email.classList.remove("input-error");
        phone.classList.remove("input-error");
        password.classList.remove("input-error");
        confirmPassword.classList.remove("input-error");

        let hasError = false;

        // Username validation
        if (username.value.trim().length < 3) {
            usernameError.textContent = "Username must be at least 3 characters long.";
            username.classList.add("input-error");
            hasError = true;
        }

        // Email validation
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (!emailPattern.test(email.value.trim())) {
            emailError.textContent = "Enter a valid email address.";
            email.classList.add("input-error");
            hasError = true;
        }

        // Phone validation (optional)
        if (phone.value.trim() !== "") {
            const phonePattern = /^\+?\d{10,15}$/;
            if (!phonePattern.test(phone.value.trim())) {
                phoneError.textContent = "Enter a valid phone number (10-15 digits).";
                phone.classList.add("input-error");
                hasError = true;
            }
        }

        // Password validation
        if (password.value.length < 6) {
            passwordError.textContent = "Password must be at least 6 characters long.";
            password.classList.add("input-error");
            hasError = true;
        }

        // Confirm Password
        if (password.value !== confirmPassword.value) {
            confirmPasswordError.textContent = "Passwords do not match.";
            confirmPassword.classList.add("input-error");
            hasError = true;
        }

        if (hasError) {
            e.preventDefault(); // Stop form submission
            formError.textContent = "Please fix the errors above before submitting.";
        }
    });
});