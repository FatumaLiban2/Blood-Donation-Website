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
});
