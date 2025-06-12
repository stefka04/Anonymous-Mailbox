<?php
require_once('../MessageController.php');
require_once('../../authentication/services/UserStorage.php');

// This service expects json input 
$message = json_decode(file_get_contents('php://input'), true);

//if (isset($_SESSION["user"])) {
     //   $senderId = $_SESSION['user']['id'];
        $senderId = 4;
        $messageController = new MessageController();
        $userStorage = new UserStorage();

        $sentAt = date('Y-m-d H:i:s');    //current time
        $chainNumber = 0;
        $message['recipients'] = trim($message['recipients']);
        $recipientsUsernames = explode(' ', $message['recipients']);
        $recipientsIds = [];
        foreach ($recipientsUsernames as $recipientUsername) {
            $recipientsIds[] = $userStorage->getIdOfUserByUsername($recipientUsername);
        }
        if (count($recipientsIds) != 0) {
            $messageController->addMessage($senderId, $sentAt, $message['topic'], $message['messageContent'], $chainNumber, $message['isAnonymous'], $recipientsIds);
        }
     /*} else {
        http_response_code(400);
        error_log(date("Y-m-d H:i:s") . " - Error occurred while generatung messages: ", 3, __DIR__ . "/../../logs/error_log.txt");
        echo json_encode(["message" => "Грешка при отварянето на кутия със съобщения!"]);
        exit();
    }*/
?>