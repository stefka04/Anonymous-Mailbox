<?php
     require_once(__DIR__ . '/UserStorage.php');

        $userStorage = new UserStorage();
        echo $userStorage->login();       
?>