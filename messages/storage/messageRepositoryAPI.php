<?php 
interface MessageRepositoryAPI {
    //@message - new message to add 
    public function addMessage(int $senderId, string $sentAt, string $topic, string $content, $chainNumber, bool $isAnonymous, array $recipientsIds);

    /*
    @messageId -> id of message that must be removed
    @userId -> id of current user
    @folderName = current folder{Inbox, SentMessages or Deleted}
    */
    public function removeMessageOfFolder(int $messageId, int $userId, string $folderName);

    public function getStarredMessagesOfUser(int $userId) : array;
    public function getMessageRecipientsUsernames(int $messageId): array;
     /*
    @messageId -> id of message that must be starred/read
    @userId -> id of current user
    @folderName = current folder{Inbox, SentMessages or Deleted}
    Returns an array with messages
    */
    public function changeStarredStatusOfMessage(bool $isStarred, int $messageId, int $userId, string $folderName);

    public function readMessage(int $messageId, int $userId, string $folderName);

    public function filterByRead(bool $isRead, int $userId, string $folderName) : array;
    public function filterByAnonimity(bool $isAnonimous, int $userId, string $folderName) : array;

    public function sortMessagesByDate(string $order, int $userId, string $folderName) : array;
}
?>