document.getElementById("new-message-form").addEventListener("submit", function (e) {
    e.preventDefault();

    const data = {
        recipients: document.querySelector('[name="receivers"]').value,
        topic: document.querySelector('[name="topic"]').value,
        messageContent: document.querySelector('[name="message_content"]').value,
        isAnonymous: document.querySelector('[name="check_anonymity"]').checked
    };

    if (data['recipients'] === null || data['recipients'].trim().length === 0) {
        const errorMessage = document.getElementById('error-message');
        errorMessage.textContent = 'Моля, посочете поне един получател';
        const error = document.getElementById('error-no-recipients');
        error.style.display = "block";
    } else {
        fetch("./services/send-message.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json; charset=utf-8"
            },
            body: JSON.stringify(data)
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("close-error-btn").addEventListener("click", () => {
        document.getElementById("error-no-recipients").style.display = "none";
    });
})
