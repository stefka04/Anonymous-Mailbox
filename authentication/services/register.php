<?php
    require_once('UserStorage.php');

        $userStorage = new UserStorage();
        echo $userStorage->registerUser();       
?>