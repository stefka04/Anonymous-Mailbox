 const tableMessages = document.getElementsByClassName('message-cell');

Array.from(tableMessages).forEach(cell => {
  cell.addEventListener('click', () => {
    setTimeout(() => {
      window.location.href = "messages/views/open-message.html";
    }, 2000);
  });
});