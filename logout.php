<?php
session_start();       
session_unset();       
session_destroy();      

header("Location: http://127.0.0.1:5500/login.html");
exit();
