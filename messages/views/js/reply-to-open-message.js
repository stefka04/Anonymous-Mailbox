document.getElementById("reply-form").addEventListener("submit", function (e) {
    e.preventDefault();

    const data = {
        recipients: document.getElementById('recipients-title').value,
        topic: document.getElementById('message-title').value,
        messageContent: document.getElementById('reply-text-area').value,
        //without anonymity here
    };

    
        fetch("../../services/reply-to-open-message.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json; charset=utf-8"
            },
            body: JSON.stringify(data)
        });
        var replyMessageForm = document.getElementById('reply-form');
        replyMessageForm.reset();
        const replyFormContainer = document.getElementById('reply-form-container');
        replyFormContainer.style.display = 'none';
    

});

