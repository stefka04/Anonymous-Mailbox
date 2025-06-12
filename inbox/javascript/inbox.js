document.addEventListener('DOMContentLoaded', () => {
    var userId;         //From Session or???
    var folderName = 'Inbox';

    function changeStarredStatusOfMessage() {
        const starParent = document.getElementById('inbox-table-body');
        starParent.addEventListener('click', (event) => {
            if (event.target.id.startsWith('star-')) {
                const starId = event.target.id;
                const starIcon = document.getElementById(starId);
                const numIdStart = 5;
                const messageId = starId.substring(numIdStart);
                var starred = true;
                if (starIcon.style.backgroundColor === "yellow") {
                    starred = false;
                    starIcon.style.backgroundColor = '';
                } else {
                    starred = true;
                    starIcon.style.backgroundColor = "yellow";
                }
                fetch('./services/star-message.php', {
                    method: "POST",
                    body: JSON.stringify({
                        "isStarred": starred, "messageId": messageId,
                        "userId": userId, "folderName": folderName
                    }),
                }
                );
            }
        });
    }

    function removeMessage() {
        const binParent = document.getElementById('inbox-table-body');
        binParent.addEventListener('click', (event) => {
            if (event.target.id.startsWith('bin-')) {
                const binId = event.target.id;
                const numIdStart = 4;
                const messageId = binId.substring(numIdStart);

                fetch('./services/remove-message.php', {
                    method: "POST",
                    body: JSON.stringify({
                        "messageId": messageId,
                        "userId": userId,
                        "folderName": folderName
                    }),
                }
                );
                const elementToRemove = document.getElementById('msg-' + messageId);
                binParent.removeChild(elementToRemove);
            }
        });
    }

    function sort() {
        const sortSelect = document.getElementById('sort-options');
        sortSelect.addEventListener('change', () => {
            const tBody = document.getElementById('inbox-table-body');
            tBody.innerHTML = '';
            generateContent({ 'folderName': folderName, 'sortBy': sortSelect.value });
        });
    }

    function filter() {
        const filterSelect = document.getElementById('filter-options');
        filterSelect.addEventListener('change', () => {
            const tBody = document.getElementById('inbox-table-body');
            tBody.innerHTML = '';
            generateContent({ 'folderName': folderName, 'filterBy': filterSelect.value });
        });
    }

    function generateContent(args) {
        fetch('./services/load-user-inbox.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(args),
        })
            .then(result => result.json())
            .then(result => {
                var messages = result.messages;
                userId = result.userId;
                var recipients = result.recipients;

                const tableHead = document.getElementById('inbox-table-head');
                tableHead.style.display = "initial";
                const tableBody = document.getElementById('inbox-table-body');
                messages.forEach(message => {
                    const messageRow = document.createElement("tr");
                    messageRow.setAttribute("class", "table-messages-rows");
                    messageRow.setAttribute("id", "msg-" + message['id']);
                    messageRow.dataset.json = JSON.stringify(message);

                    const star = document.createElement("td");
                    const starIcon = document.createElement("img");
                    starIcon.setAttribute("src", "../img/star.png");
                    starIcon.setAttribute("class", "icons stars");
                    starIcon.setAttribute("alt", "Star icon");
                    //starIcon.setAttribute("class", "icon-btn material-symbols-outlined stars");
                    starIcon.setAttribute("id", "star-" + message['id']);
                    starIcon.style.backgroundColor = message['isStarred'] ? "yellow" : '';

                    star.appendChild(starIcon);
                    messageRow.appendChild(star);
                    //messageSender.appendChild(starIcon);

                    const messageSender = document.createElement("td");
                    messageSender.style.fontWeight = message['isRead'] ? "lighter" : "bold";
                    messageSender.style.fontSize = "24px";
                    var sender;
                    if (folderName === 'SentMessages') {                                    //NEW!!!!
                        var messageRecipients = recipients[message['id']].join();
                        sender = document.createTextNode(`До: ${messageRecipients}`);
                    } else if (message['isAnonymous']) {
                        sender = document.createTextNode('Анонимен');
                    } else {
                        sender = document.createTextNode(message['senderUsername']);
                    }
                    messageSender.appendChild(sender);

                    const aligningDiv = document.createElement("div");
                    const messageTopic = document.createElement("span");
                    const topic = document.createTextNode(message['topic']);
                    messageTopic.style.fontWeight = "normal";
                    messageTopic.style.fontSize = "16px";
                    messageTopic.appendChild(topic);
                    aligningDiv.appendChild(messageTopic);
                    messageSender.appendChild(aligningDiv);
                    messageSender.style.width = "75%";
                    messageRow.appendChild(messageSender);

                    const bin = document.createElement("td");
                    const binIcon = document.createElement("img");
                    binIcon.setAttribute("src", "../img/bin.png");
                    binIcon.setAttribute("class", "icons bins");
                    binIcon.setAttribute("alt", "Bin icon");
                    binIcon.setAttribute("id", "bin-" + message['id']);

                    bin.appendChild(binIcon);
                    messageRow.appendChild(bin);

                    const messageSentAt = document.createElement("td");
                    messageSentAt.style.fontWeight = "bold";
                    const sentAt = document.createTextNode(message['sentAt']);
                    messageSentAt.appendChild(sentAt);
                    messageRow.appendChild(messageSentAt);

                    tableBody.appendChild(messageRow);

                   // messageRow.addEventListener("click", (e) => {
                   /* messageSender.addEventListener("click", (e) => {
                        fetch('./services/set-message-in-session.php', {
                            method: "POST",
                            body: JSON.stringify(message),
                        }).then(window.location.href = ' ../messages/views/open-message.php')
                    });*/

                     const idToSent = message['id'];                //NEW!!!!! CHECK!!!!
                     messageSender.addEventListener("click", (e) => {
                        fetch('./services/set-message-in-session.php', {
                         method: "POST",
                         body: JSON.stringify(message),
                    }).then(() => {
                        window.location.href = '../messages/views/open-message.php';
                    })
            });
                });
            })
    }


    /* sidebarNav.addEventListener('click', (e) => {
         const targetLi = e.target.closest('li');
         if (!targetLi) return;
 
         sidebarNav.querySelectorAll('li').forEach(li => li.classList.remove('active'));
         targetLi.classList.add('active');
 
         const view = targetLi.dataset.view;
         if (view) {
             if (view === 'inbox') {
                 mainViewTitle.textContent = 'Входящи';
                 folderName = 'Inbox';
             } else if (view === 'sent') {
                 mainViewTitle.textContent = 'Изпратени';
                 folderName = 'SentMessages';
             } else if (view === 'starred') {
                 mainViewTitle.textContent = 'Със звезда';
                 folderName = 'Starred';
             } else if (view === 'trash') {
                 mainViewTitle.textContent = 'Изтрити';
                 folderName = 'Deleted';
             }
 
             const mainViewContent = document.getElementById('inbox-table-body');
             mainViewContent.innerHTML = '';
             generateContent({ 'folderName': folderName });
         }
     });*/

    const mainViewTitle = document.getElementById('main-view-title');
    const mainViewContent = document.getElementById('inbox-table-body');

    const inbox = document.getElementById("inbox");
    const sent = document.getElementById("sent");
    const starred = document.getElementById("starred");
    const deleted = document.getElementById("deleted");

    inbox.addEventListener('click', (e) => {
        inbox.style.backgroundColor = "lightblue";
        sent.style.backgroundColor = "initial";
        starred.style.backgroundColor = "initial";
        deleted.style.backgroundColor = "initial";

        mainViewTitle.textContent = 'Входящи';
        folderName = 'Inbox';
        mainViewContent.innerHTML = '';
        generateContent({ 'folderName': folderName });
    });

    sent.addEventListener('click', (e) => {
        sent.style.backgroundColor = "lightblue";
        inbox.style.backgroundColor = "initial";
        starred.style.backgroundColor = "initial";
        deleted.style.backgroundColor = "initial";
        
        mainViewTitle.textContent = 'Изпратени';
        folderName = 'SentMessages';
        mainViewContent.innerHTML = '';
        generateContent({ 'folderName': folderName });
    });

    starred.addEventListener('click', (e) => {
        starred.style.backgroundColor = "lightblue";
        inbox.style.backgroundColor = "initial";
        sent.style.backgroundColor = "initial";
        deleted.style.backgroundColor = "initial";

        mainViewTitle.textContent = 'Със звезда';
        folderName = 'Starred';
        mainViewContent.innerHTML = '';
        generateContent({ 'folderName': folderName });
    });

    deleted.addEventListener('click', (e) => {
        deleted.style.backgroundColor = "lightblue";
        inbox.style.backgroundColor = "initial";
        sent.style.backgroundColor = "initial";
        starred.style.backgroundColor = "initial";

        mainViewTitle.textContent = 'Изтрити';
        folderName = 'Deleted';
        mainViewContent.innerHTML = '';
        generateContent({ 'folderName': folderName });
    });
    
    mainViewTitle.textContent = 'Входящи';    //default page
    generateContent({ 'folderName': 'Inbox' });

    changeStarredStatusOfMessage();
    removeMessage();
    sort();
    filter();
});