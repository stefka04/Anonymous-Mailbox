const createNewMessageDiv = document.querySelector('#create-message');

createNewMessageDiv.addEventListener('click', () => {
    const newMessage = document.getElementById('new-message');
    const newMessageDisplay = newMessage.style.display;
    if (newMessageDisplay == 'none') {
        newMessage.style.display = 'block';
    } 
});

const cancelIcon = document.querySelector('#cancel-icon');

cancelIcon.addEventListener('click', () => {
    var newMessageForm = document.getElementById('new-message-form');
    newMessageForm.reset();
    const newMessage = document.getElementById('new-message');
    newMessage.style.display = 'none';
});

