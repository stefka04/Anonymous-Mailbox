document.getElementById("new-message-form").addEventListener("submit", function (e) {
  e.preventDefault();

  const data = {
    recipients: document.querySelector('[name="receivers"]').value,
    topic: document.querySelector('[name="topic"]').value,
    messageContent: document.querySelector('[name="message_content"]').value,
    isAnonymous: document.querySelector('[name="check_anonymity"]').checked
  };

  console.log(data);

  fetch("./services/send-message.php", {
    method: "POST",
    /*headers: {
      "Content-Type": "application/json; charset=utf-8"
    },*/
    body: JSON.stringify(data)
  })
});