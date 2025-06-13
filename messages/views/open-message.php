<?php

    session_start();

    $message = $_SESSION['message'] ? $_SESSION['message']  : null;
    if ($message) {
       
      $_SESSION['message']['id'] = $message['id'];
      $_SESSION['message']['senderUsername'] = htmlspecialchars($message['isAnonymous'] ? 'Анонимен' : $message['senderUsername']);
      $_SESSION['message']['topic'] = htmlspecialchars($message['topic']);
      $_SESSION['message']['content'] = htmlspecialchars($message['content']);
    }
?>

<!DOCTYPE html>
<html lang="bg">

<head>
    <title>Secret Unitine</title>
    <meta charset="UTF-8" />
    <link href="./css/open-message.css" rel="stylesheet" />
    <script src="./js/message-reply.js" defer></script> 
     <script src="./js/add-notes.js" defer></script> 
     <script src="./js/logout-message.js" defer></script> 
</head>

<body id="page-body">
    <header>
        <div id="header-part">
            <h1>Secret Unitine</h1>
            <label id="logout-label">Изход</label>
        </div>
    </header>

    <main id = "main-page">
        <section id = "menu-section">
            <div id="menu-div">
                <h2 id = "create-message" class="menu-option">
                    <img id="pencil-icon" class="icons" src="../../img/pencil.png" alt="Pencil icon" />
                    Ново съобщение
                </h2>            
                <h2 id = "inbox" class="menu-option">
                    <img id="inbox-icon" class="icons" src="../../img/inbox.png" alt="Inbox icon" />
                    Входяща поща
                </h2>
                <h2 id = "sent" class="menu-option">
                    <img id="sent-icon" class="icons" src="../../img/sent.png" alt="Sent icon" />
                    Изпратени
                </h2>
                <h2 id = "starred" class="menu-option">
                    <img id="star-icon" class="icons" src="../../img/star.png" alt="Star icon" />
                    Със звезда
                </h2>
                <h2 id = "deleted" class="menu-option">
                    <img id="bin-icon" class="icons" src="../../img/bin.png" alt="Bin icon" />
                    Изтрити
                </h2>
            </div>
        </section>
              
    <section id="messages-table-section"> 
           <form id="open-message-form">
               <h2 id="message-title"  class="annotatable">
                 <?php echo $_SESSION['message']['topic']?>
               </h2>              
               <section id="message-text">
                 <h3 id="message-sender"><?php echo $_SESSION['message']['senderUsername'] ?></h3>
                    <p id="message-paragraph"  class="annotatable">
                      <?php echo $_SESSION['message']['content']?>
                    </p>                               
              </section>                       
           </form>   
            
    <section>
            <div id="new-message" style="display: none;">
                <form id="new-message-form"  class="annotatable">
                    <div>
                        <table>
                            <tbody>
                                <tr>
                                    <td id="new-message-title">
                                        <h2>Ново съобщение</h2>
                                    </td>
                                    <td><img id="minimize-icon" class="new-message-nav-icons" src="../../img/minimize.png"
                                            alt="Minimize icon" title="Намаляване" /></td>
                                    <td><img id="cancel-icon" class="new-message-nav-icons" src="../../img/cancel.png"
                                            alt="Cancel icon" title="Затваряне БЕЗ запазване" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <input type="text" id="receivers" name="receivers" class="message-header"
                            placeholder="Получатели" />
                    </div>
                    <div>
                        <input type="text" id="topic" name="topic" class="message-header" placeholder="Тема" />
                    </div>
                    <div>
                        <textarea id="message-content" name="message_content" rows="15" cols="50"></textarea>
                    </div>
                    <div>
                        <table>
                            <tbody>
                                <tr>
                                    <td><input type="submit" id="send-message-button" value="Изпращане" /></td>
                                    <td><img id="attach-files-icon" class="icons" src="../../img/attach-files.png"
                                            alt="attach-files icon" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </section>         
      </section>   
    </main>
  </body>
</html>
