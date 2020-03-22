<?php
spl_autoload_register(function ($className) {
    $fileName = "../controller/$className.php";
    if (file_exists($fileName)) {
        require_once($fileName);
    }
});

spl_autoload_register(function ($className) {
    $fileName = "../model/$className.php";
    if (file_exists($fileName)) {
        require_once($fileName);
    }
});

#Static Class's autoloaders
spl_autoload_register(function ($className) {
    $PHPMailerClassNames = array("PHPMailer", "SMTP");
    if (in_array($className, $PHPMailerClassNames)) {
        require_once("../util/PHPMailer/$className.php");
    }

    $utilClassNames = array("BITMailer", "MessageUtility", "OTPUtility");
    if (in_array($className, $utilClassNames)) {
        require_once("../util/$className.php");
    }

    if($className === 'Route') {
        require_once("../config/routes/$className.php");
    }
});