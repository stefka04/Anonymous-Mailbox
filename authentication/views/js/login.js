function createLabel(message, messageColor) {
     const responseLabel = document.getElementById("response-label");
     responseLabel.style.display = "block";
     responseLabel.textContent = message;
     responseLabel.style.color = messageColor;
}

function analyzeResponse(response, message) {
     if (response.status === 200) {
        createLabel(message, "green");    
        } else {
        createLabel(response["message"], "red");
}
}

const form = document.getElementById('login-form');
form.addEventListener('submit', (event) => {
    event.preventDefault();
    const emailField = document.getElementById("email");
    const passwordField = document.getElementById("password");
    const email = emailField.value.trim();
    const password = passwordField.value.trim();

    const userData = {
        email: email,
        password: password
    };

    fetch('../services/new-login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData),
    })
    .then(response => response.json().then(data => {
        data.status = response.status; 
        analyzeResponse(data, "Успешно влизане! Пренасочване към началната страница...");
        if (data.status === 200) {
            setTimeout(() => {
                window.location.href = "../../inbox/index.html";
            }, 2000);
        }
    }));
});

const registerButton = document.getElementById("register-btn");
registerButton.addEventListener('click', () => {
    window.location.href = "./registration-form.html";
});