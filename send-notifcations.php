<?php
clearstatcache();
require "./haverford-menu.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$menu = new HaverfordMenu();
$menu->getNowMeal();
$menu->getNowRating();

?>