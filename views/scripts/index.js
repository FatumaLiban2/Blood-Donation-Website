document.addEventListener("DOMContentLoaded", function() {
    const learnButton = document.getElementById("learnButtonHero");
    const donateButton = document.getElementById("donateButtonHero");

    const loginButton = document.getElementById("loginButton");
    const signupButton = document.getElementById("signupButton");

    const loginModal = document.getElementById("loginModal");
    const signupModal = document.getElementById("signupModal");

    const closeLogin = document.getElementById("closeLogin");
    const closeSignup = document.getElementById("closeSignup");

    const toLoginbtn = document.getElementById("toLogin");
    const toSignupbtn = document.getElementById("toSignup");

    const signupVerificationModal = document.getElementById("signupVerificationModal");
    const closeSignupVerification = document.getElementById("closeSignupVerification");
    const resendCodeLink = document.getElementById("resendCode");

    const forgotPasswordModal = document.getElementById("forgotPasswordModal");
    const forgotPasswordOtpModal = document.getElementById("forgotPasswordOtpModal");
    const closeForgotPassword = document.getElementById("closeForgotPassword");
    const closeForgotPasswordOtp = document.getElementById("closeForgotPasswordOtp");
    const toLoginFromForgot = document.getElementById("toLoginFromForgot");
    const resendOtp = document.getElementById("resendOtp");
    const forgotPasswordEmailInput = document.getElementById("forgotPasswordEmail");
    const forgotPassword = document.getElementById("forgotPassword");

    const resetPasswordModal = document.getElementById("resetPasswordModal");
    const closeResetPassword = document.getElementById("closeResetPassword");


    const urlParams = new URLSearchParams(window.location.search);

    // Toast Notification


    learnButton.addEventListener("click", function() {
        document.getElementById("learn").scrollIntoView({ behavior: 'smooth' });
        console.log("Learn More button clicked");
    });

    donateButton.addEventListener("click", function() {
        document.getElementById("donate").scrollIntoView({ behavior: 'smooth' });
        console.log("Donate Now button clicked");
    }); 

    loginButton.addEventListener("click", function() {
        loginModal.classList.add("modal-active");
    });

    signupButton.addEventListener("click", function() {
        signupModal.classList.add("modal-active");
    });

    closeLogin.addEventListener("click", function() {
        loginModal.classList.remove("modal-active");
    });

    closeSignup.addEventListener("click", function() {
        signupModal.classList.remove("modal-active");
    });

    toLoginbtn.addEventListener("click", function() {
        signupModal.classList.remove("modal-active");
        loginModal.classList.add("modal-active");
    });

    toSignupbtn.addEventListener("click", function() {
        loginModal.classList.remove("modal-active");
        signupModal.classList.add("modal-active");
    });

    closeSignupVerification.addEventListener("click", function() {
        signupVerificationModal.classList.remove("modal-active");
    });

    forgotPassword.addEventListener("click", function() {
        loginModal.classList.remove("modal-active");
        forgotPasswordModal.classList.add("modal-active");
    });

    closeForgotPassword.addEventListener("click", function() {
        forgotPasswordModal.classList.remove("modal-active");
    });

    closeForgotPasswordOtp.addEventListener("click", function() {
        forgotPasswordOtpModal.classList.remove("modal-active");
    });

    toLoginFromForgot.addEventListener("click", function() {
        forgotPasswordModal.classList.remove("modal-active");
        loginModal.classList.add("modal-active");
    });

    closeResetPassword.addEventListener("click", function() {
        resetPasswordModal.classList.remove("modal-active");
    });

    if (resendCodeLink) {
        resendCodeLink.addEventListener("click", async function(event) {
            event.preventDefault();

            resendCodeLink.classList.add("disabled-link");
            try {
                const response = await fetch("handlers/resendVerificationHandler.php", {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    credentials: "same-origin"
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok || payload.error) {
                    throw new Error(payload.error || "unknown_error");
                }

                alert("A new verification code has been sent to your email.");
            } catch (error) {
                console.error("Resend OTP failed: ", error);
                alert("Unable to resend the verification code right now. Please try again later.");
            } finally {
                resendCodeLink.classList.remove("disabled-link");
            }
        });
    }

    if (resendOtp) {
        resendOtp.addEventListener("click", async function(event) {
            event.preventDefault();

            resendOtp.classList.add("disabled-link");

            const email = forgotPasswordEmailInput ? forgotPasswordEmailInput.value.trim() : "";

            if (!email) {
                alert("Enter the email address you want the OTP sent to first.");
                resendOtp.classList.remove("disabled-link");
                return;
            }
            try {
                const response = await fetch("handlers/resendOtpHandler.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({ email })
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok || payload.error) {
                    throw new Error(payload.error || "unknown_error");
                }

                alert("A new OTP code has been sent to your email.");
            } catch (error) {
                console.error("Resend OTP failed: ", error);
                alert("Unable to resend the verification cde right now. Please try again later.");
            } finally {
                resendOtp.classList.remove("disabled-link");
            }
        });

    }


    // Handle verification sent successfully
    if (urlParams.get("verification") === "sent") {
        signupModal.classList.remove("modal-active");
        signupVerificationModal.classList.add("modal-active");
    }

    // Handle verification success
    if (urlParams.get("verification") === "success") {
        signupVerificationModal.classList.remove("modal-active");
        alert("Verification successful! You can now log in.");
        loginModal.classList.add("modal-active");
    }

    if (urlParams.get("login") === "success") {
        alert("Login successful! Welcome back.");
        loginModal.classList.remove("modal-active");
    }

    if (urlParams.get("admin") === "registered") {
        signupModal.classList.remove("modal-active");
        alert("Signup success");
        loginModal.classList.add("modal-active");

    }

    if (urlParams.get("forgotpassword") === "otpsent") {
        forgotPasswordModal.classList.remove("modal-active");
        alert("An OTP code has been sent to your email.");
        forgotPasswordOtpModal.classList.add("modal-active");
    }

    if (urlParams.get("resetPassword") === "start") {
        forgotPasswordOtpModal.classList.remove("modal-active");
        alert("OTP verified! You can now reset your password.");
        resetPasswordModal.classList.add("modal-active");
    }

    if (urlParams.get("resetpassword") === "success") {
        resetPasswordModal.classList.remove("modal-active");
        alert("Password reset successful! You can now log in with your new password.");
        loginModal.classList.add("modal-active");
    }


    // Error Handling
    const error = urlParams.get("error");
    if (error) {
        switch (error) {
            case 'emptyfields':
                alert("Please fill in all fields.");
                signupModal.classList.add("modal-active");
                break;
            case 'emailnotfound':
                alert("Email not found");
                exit();
            case 'passwordmissmatch':
                alert("Passwords do not match. Please try again.");
                signupModal.classList.add("modal-active");
                break;
            case 'invalidemail':
                alert("Invalid email address. Please enter a valid email.");
                signupModal.classList.add("modal-active");
                break;
            case 'emailexists':
                alert("Email already exists. Please use a different email.");
                signupModal.classList.add("modal-active");
                break;
            case 'failedtosendverificationemail':
                alert("Failed to send verification email. Please try again later.");
                signupVerificationModal.classList.add("modal-active");
                break;
            case 'invalidsession':
                alert("An error occurred. Please refresh the page and try again.");
                break;
            case 'codeexpired':
                alert("Verification code has expired. Please request a new code.");
                signupVerificationModal.classList.add("modal-active");
                break;
            case 'verificationfailed':
                alert("Verification failed. Please check your credentials and try again.");
                signupVerificationModal.classList.add("modal-active");
                break;
            case 'invalidcode':
                alert("Invalid verification code. Please check your email and try again.");
                signupVerificationModal.classList.add("modal-active");
                break;
            case 'invalidcredentials':
                alert("Invalid login credentials. Please try again.");
                loginModal.classList.add("modal-active");
                break;
            case 'emailfailed':
                alert("Failed to send email. Please try again later.");
                forgotPasswordModal.classList.add("modal-active");
                break;
            case 'accountnotfound':
                alert("Account not found. Please check your email and try again.");
                forgotPasswordModal.classList.add("modal-active");
                break;
            case 'forgotcodeexpired':
                alert("OTP code has expired. Please request a new code.");
                forgotPasswordOtpModal.classList.add("modal-active");
                break;
            case 'invalidresetcode':
                alert("Invalid OTP code. Please check your email and try again.");
                forgotPasswordOtpModal.classList.add("modal-active");
                break;
            case 'resetfailed':
                alert("Failed to reset password. Please try again later.");
                resetPasswordModal.classList.add("modal-active");
                break;
            default:
                alert("An unknown error occurred. Please try again.");
        }
    }

    // Clean up URL parameters
    if (urlParams.has("error") || urlParams.has("verification")) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
