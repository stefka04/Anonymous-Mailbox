<?php
require_once __DIR__ . '/../models/message.php';
require_once __DIR__ . '/messageRepositoryAPI.php';
require_once __DIR__ . '/../../db/db.php';

define("INBOX_FOLDER_ID", 1);
define("SENT_FOLDER_ID", 2);
define("DELETED_FOLDER_ID", 3);

class MessageRepository implements MessageRepositoryAPI {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function addMessage(int $senderId, string $sentAt, string $topic, string $content, $chainNumber, bool $isAnonymous, array $recipientsIds) {
        try {
            $connection = $this->db->getConnection();
            
            $connection->beginTransaction();
            $sql = "INSERT INTO messages (senderId, sentAt, topic, content, chainNumber, isAnonymous) VALUES (?, ?, ?, ?, ?, ?)";
            $insertStatement = $connection->prepare($sql);
            $insertStatement->execute([$senderId, $sentAt, $topic, $content, $chainNumber, $isAnonymous]);  

            $messageId = $connection->lastInsertId();
            //add to message_status_table AS SENT
            $sql = "INSERT INTO user_messages_status (messageId, userId, messageFolderId) VALUES (?, ?, ?)";
            $insertStatement = $connection->prepare($sql);
            $insertStatement->execute([$messageId, $senderId, SENT_FOLDER_ID]); 

            $sql = "INSERT INTO message_recipients (messageId, recipientId) VALUES (?, ?)";
            $insertStatement = $connection->prepare($sql);

            foreach ($recipientsIds as $recipientId) {
                    $insertStatement->execute([$messageId, $recipientId]);

                    //add to message_status_table AS received
                    $sqlInsertInFolder = "INSERT INTO user_messages_status (messageId, userId, messageFolderId) VALUES (?, ?, ?)";
                    $insertStatementMessageStatus = $connection->prepare($sqlInsertInFolder);
                    $insertStatementMessageStatus->execute([$messageId, $recipientId, INBOX_FOLDER_ID]);  
            }
            
            $connection->commit();
            echo "Successfully added message";

        } catch (PDOException $e) {
            $connection->rollback();
            error_log(date("Y-m-d H:i:s") . " - Error occurred while adding message: "
             . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function removeMessageOfFolder(int $messageId, int $userId, string $folderName) {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            if ($messageFolderId == null ) {
                throw new InvalidArgumentException("Folder name of message to remove is not Inbox, SentMessages or Deleted. Input folder name: ".$folderName);
            }
            if (strcasecmp($folderName, "Inbox") == 0 || strcasecmp($folderName, "SentMessages") == 0) {
                //if message is sent from me to me, update it everywhere
                if ($this->haveSameSenderAndRecipient($messageId, $userId)) {
                    //delete from current folder
                    $sql = "DELETE FROM user_messages_status WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                    $deleteStatement = $connection->prepare($sql);
                    $deleteStatement->execute([$messageId, $userId, $messageFolderId]);

                    //move it to trashed
                    $sql = "UPDATE user_messages_status SET messageFolderId = ? WHERE messageId = ? AND userId = ?";
                    $updateStatement = $connection->prepare($sql);
                    $updateStatement->execute([DELETED_FOLDER_ID, $messageId, $userId]);
                } else {
                    $sql = "UPDATE user_messages_status SET messageFolderId = ? WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                    $updateStatement = $connection->prepare($sql);
                    $updateStatement->execute([DELETED_FOLDER_ID, $messageId, $userId, $messageFolderId]);
                }
            } else if (strcasecmp($folderName, "Deleted") == 0) {
                $sql = "SELECT COUNT(*) AS count FROM user_messages_status WHERE messageId = :messageId";
                $selectStatement = $connection->prepare($sql);
                $selectStatement->execute(['messageId' => $messageId]);
                $resultData = $selectStatement->fetch();
                if ($resultData && $resultData['count'] == 1) {
                    $sql = "DELETE FROM messages WHERE id = ?";
                    $deleteStatement = $connection->prepare($sql);
                    $deleteStatement->execute([$messageId]);
                } else {
                    $sql = "DELETE FROM user_messages_status WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                    $deleteStatement = $connection->prepare($sql);
                    $deleteStatement->execute([$messageId, $userId, $messageFolderId]);
                }
            }
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while removing a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function getMessageRecipientsUsernames(int $messageId): array { 
        try {
            $connection = $this->db->getConnection();
            $sql = "SELECT username FROM users AS u JOIN message_recipients AS mr ON mr.recipientId = u.id WHERE mr.messageId = ?";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$messageId]);
            
            $recipientsData = $selectStatement->fetchAll();
            $recipientsUsernames = [];

            foreach ($recipientsData as $recipient) {
                $recipientsUsernames[] = $recipient['username'];
            }
            return $recipientsUsernames;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while getting recipientsIds of message with id=$messageId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function changeStarredStatusOfMessage(bool $isStarred, int $messageId, int $userId, string $folderName) { 
         try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
             if ($this->haveSameSenderAndRecipient($messageId, $userId)) {
                $sql = "UPDATE user_messages_status SET isStarred = ? WHERE messageId = ? AND userId = ?";
                $updateStatement = $connection->prepare($sql);
                $updateStatement->execute([$isStarred, $messageId, $userId]);
            } else {
                $sql = "UPDATE user_messages_status SET isStarred = ? WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
                $updateStatement = $connection->prepare($sql);
                $updateStatement->execute([$isStarred, $messageId, $userId, $messageFolderId]);
            }
        }
         catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while starring a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function readMessage(int $messageId, int $userId, string $folderName) { 
         try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "UPDATE user_messages_status SET isRead = 1 WHERE messageId = ? AND userId = ? AND messageFolderId = ?";
            $updateStatement = $connection->prepare($sql);
            $updateStatement->execute([$messageId, $userId, $messageFolderId]);
        }
         catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while starring a message with id=$messageId : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }
    public function getStarredMessagesOfUser(int $userId) : array {
        try {
            $connection = $this->db->getConnection();
            $sql = "SELECT m.*, u.username AS senderUsername, ums.isRead AS isRead, ums.isStarred AS isStarred
                FROM messages m
                JOIN users AS u ON u.id = m.senderId
                JOIN user_messages_status AS ums ON m.id = ums.messageId
                WHERE ums.userId = ? AND ums.isStarred = 1";
    
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId]);
            
            $starredMessagesData = $selectStatement->fetchAll();
            $starredMessages = [];

            foreach ($starredMessagesData as $starredMessage) {
                $starredMessages[] = Message::fromArray($starredMessage);
            }
            return $starredMessages;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by star: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function filterByRead(bool $isRead, int $userId, string $folderName) : array {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "SELECT m.*, u.username AS senderUsername, ums.isRead AS isRead, ums.isStarred AS isStarred
                FROM messages m
                JOIN users AS u ON u.id = m.senderId
                JOIN user_messages_status AS ums ON m.id = ums.messageId
                WHERE ums.userId = ? AND ums.messageFolderId = ? AND ums.isRead = ?";
    
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId, $isRead]);
            
            $readMessagesData = $selectStatement->fetchAll();
            $readMessages = [];

            foreach ($readMessagesData as $readMessage) {
                $readMessages[] = Message::fromArray($readMessage);
            }
            return $readMessages;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by unread: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }

     public function filterByAnonimity(bool $isAnonimous, int $userId, string $folderName) : array {
        try {
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $sql = "SELECT m.*, u.username AS senderUsername, ums.isRead AS isRead, ums.isStarred AS isStarred
                FROM messages m
                JOIN users AS u ON u.id = m.senderId
                JOIN user_messages_status AS ums ON m.id = ums.messageId
                WHERE ums.userId = ? AND ums.messageFolderId = ? AND m.isAnonymous = ?";
    
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId, $isAnonimous]);
            
            $anonymousMessagesData = $selectStatement->fetchAll();
            $anonymousMessages = [];

            foreach ($anonymousMessagesData as $anonymousMessage) {
                $anonymousMessages[] = Message::fromArray($anonymousMessage);
            }
            return $anonymousMessages;
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while filtering by anonimity: " 
            . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }

    public function sortMessagesByDate(string $order, int $userId, string $folderName) : array {
         try {
            if (strcasecmp($order, "DESC") != 0 && strcasecmp($order, "ASC") != 0) {
                error_log(date("Y-m-d H:i:s") . " - Error occurred while sorting with order= $order" . "\n", 3,"../logs/error_log.txt");
                http_response_code(500);
                throw new InvalidArgumentException("Sorting order is not DESC or ASC. Input order: ".$order);
            }
            $connection = $this->db->getConnection();
            $messageFolderId = $this->getMessageFolderId($folderName);
            $order = strtoupper($order);

            $sql = "SELECT m.*, u.username AS senderUsername, ums.isRead AS isRead, ums.isStarred AS isStarred
                FROM messages m
                JOIN users AS u ON u.id = m.senderId
                JOIN user_messages_status AS ums ON m.id = ums.messageId
                WHERE ums.userId = ? AND ums.messageFolderId = ?
                ORDER BY m.sentAt $order";

            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute([$userId, $messageFolderId]);

            $sortedMessagesData = $selectStatement->fetchAll();
            $sortedMessages = [];
            
            foreach ($sortedMessagesData as $message) {
                $sortedMessages[] = Message::fromArray($message);
            }
            return $sortedMessages;  
        } catch (PDOException $e) {
            error_log(date("Y-m-d H:i:s") . " - Error occurred while sorting with order= $order ". $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
            http_response_code(500);
        }
    }

    private function getMessageFolderId(string $folderName): ?int {
        switch ($folderName) {
            case 'Inbox': return INBOX_FOLDER_ID;
            case 'SentMessages': return SENT_FOLDER_ID;
            case 'Deleted': return DELETED_FOLDER_ID;
            default: return INBOX_FOLDER_ID;
        }
    }

    private function haveSameSenderAndRecipient(int $messageId, int $userId): bool {
         try {
            $connection = $this->db->getConnection();
            $sql = "SELECT 1 AS haveSameSenderAndRecipient
             FROM message_recipients AS mr
             JOIN messages AS m ON mr.messageId = m.id WHERE m.id = :messageId
             AND m.senderId = :userId AND mr.recipientId = :userId";
            $selectStatement = $connection->prepare($sql);
            $selectStatement->execute(['messageId' => $messageId,
                                       'userId' => $userId]);
            
            $resultData = $selectStatement->fetch();

            return $resultData ? (bool) $resultData['haveSameSenderAndRecipient'] : false;
        } catch (PDOException $e) {
            http_response_code(500);
             error_log(date("Y-m-d H:i:s") . " - Error occurred while getting messageFolderId with folderName=$folderName : "
             . $e->getMessage() . "\n", 3, __DIR__ . "/../../logs/error_log.txt");
        }
    }
}