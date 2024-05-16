<?php
    session_start();
    session_destroy();
    header("location: principal.php");
    session_start();
    $_SESSION["usuario"]="invitado";
?>