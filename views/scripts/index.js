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

    // Error Handling
    const error = urlParams.get("error");
    if (error) {
        switch (error) {
            case 'emptyfields':
                alert("Please fill in all fields.");
                signupModal.classList.add("modal-active");
                break;
            case 'passwordmismatch':
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
                signupModal.classList.add("modal-active");
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
            default:
                alert("An unknown error occurred. Please try again.");
        }
    }

    // Clean up URL parameters
    if (urlParams.has("error") || urlParams.has("verification")) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
