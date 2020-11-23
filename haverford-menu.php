<?php

require './vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class HaverfordMenu {

    private $todaysmenu;
    private $senderemail;
    private $senderpassword;

    private $db_hostname;
    private $db_username;
    private $db_password;

    function __construct() {
        date_default_timezone_set("US/Eastern");
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $this->senderemail = $_ENV['MY_EMAIL'];
        $this->senderpassword = $_ENV['MY_PASSWORD'];

        $this->db_hostname = $_ENV['MY_DB_HOST'];
        $this->db_username = $_ENV['MY_DB_USER'];
        $this->db_password = $_ENV['MY_DB_PASSWORD'];

        $this->getMenu();
    }

    function is_html($string) {
        return preg_match("/<[^<]+>/",$string,$m) != 0;
    }

    function getMenu() {
        $mykey = 'AIzaSyCWlhTZZFgMJcS1Bgi_KntoxiVEl0HWWIk'; // typically like Gtg-rtZdsreUr_fLfhgPfgff
        $calendarid = 'hc.dining@gmail.com'; // will look somewhat like 3ruy234vodf6hf4sdf5sd84f@group.calendar.google.com
        $callurl = 'https://www.googleapis.com/calendar/v3/calendars/'.$calendarid.'/events?key='.$mykey.'&timeMin='.date("Y-m-d", strtotime(' today')).'T09:11:13.562Z&timeMax='.date("Y-m-d", strtotime(' +1 day')).'T09:11:13.562Z';
        $getinfo = file_get_contents($callurl);
        $menuinfo = json_decode($getinfo);

        $menuarray = array();

        foreach($menuinfo as $item){

            foreach($item as $smaller){

                if ($this->is_html($smaller->description)){
                    $smaller->description = str_replace("<br>", "\n", $smaller->description);
                    $smaller->description = strip_tags($smaller->description);
                }

                $mealname = str_replace(' ', '', $smaller->summary);
                $mealarray = array();

                foreach(explode("\n", $smaller->description) as $menuitem){

                    if (strpos($menuitem, 'WEEK') !== false) {
                        //Not real
                    } else {
                        $menuitem = str_replace(' *', '', $menuitem);
                        $menuitem = trim($menuitem);
                        array_push($mealarray, $menuitem);
                    }
                }

                $menuarray[$mealname] = $mealarray;
            }
        }

        $this->todaysmenu = $menuarray;
    }

    function getNowMeal() {
        if (date("D") != "Sun" && date("D") != "Sat"){

            //Breakfast
            $now = new DateTime();
            $begin = new DateTime('6:00 am');
            $end = new DateTime('6:59 am');
            if ($now >= $begin && $now <= $end) {
                //Do Breakfast
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Breakfast"];
                $currentmeal = "Breakfast";
            }

            //Lunch
            $now = new DateTime();
            $begin = new DateTime('11:00 am');
            $end = new DateTime('11:59 am');
            if ($now >= $begin && $now <= $end) {
                //Do Lunch
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Lunch"];
                $currentmeal = "Lunch";
            }

            //Dinner
            $now = new DateTime();
            $begin = new DateTime('5:00 pm');
            $end = new DateTime('5:59 pm');
            if ($now >= $begin && $now <= $end) {
                //Do Dinner
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Dinner"];
                $currentmeal = "Dinner";
            }

            //Send
            if ($currentmenu != "" && $currentmeal != ""){

                if (date("Y-m-d") == "2020-10-30"){
                    //Special actions
                    $this->sendSMSHelper("Please stand in solidarity with Harverford's BIPOC students by not eating at the DC or Coop. Thank you. https://www.instagram.com/haverfordbsl/?hl=en", "custom", 0);
                } else {
                    $menulist = implode(', ', $currentmenu);
                    $mealid = $this->getMealID(strtolower($currentmeal), $menulist);
                    $this->sendSMSHelper($currentmenu, $currentmeal, $mealid);
                }

            }

        } else if (date("D") == "Sun" || date("D") == "Sat"){

            //Brunch
            $now = new DateTime();
            $begin = new DateTime('9:00 am');
            $end = new DateTime('9:59 am');
            if ($now >= $begin && $now <= $end) {
                //Do Brunch
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Brunch"];
                $currentmeal = "Brunch";
            }
    
            //Dinner
            $now = new DateTime();
            $begin = new DateTime('1:00 pm');
            $end = new DateTime('1:59 pm');
            if ($now >= $begin && $now <= $end) {
                //Do Dinner
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Dinner"];
                $currentmeal = "Dinner";
            }

            //Send
            if ($currentmenu != "" && $currentmeal != ""){
                if (date("Y-m-d") == "2020-11-21"){
                    //Special actions
                    $this->sendSMSHelper("Have a good break! Dino already misses you ;(", "custom", 0);
                } else {
                    $menulist = implode(', ', $currentmenu);
                    $mealid = $this->getMealID(strtolower($currentmeal), $menulist);
                    $this->sendSMSHelper($currentmenu, $currentmeal, $mealid);
                }
            }

        }

    }

    function getNowRating() {
        if (date("D") != "Sun" && date("D") != "Sat"){

            //Breakfast
            $now = new DateTime();
            $begin = new DateTime('8:00 am');
            $end = new DateTime('8:59 am');
            if ($now >= $begin && $now <= $end) {
                //Do Breakfast
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Breakfast"];
                $currentmeal = "Breakfast";
            }

            //Lunch
            $now = new DateTime();
            $begin = new DateTime('1:00 pm');
            $end = new DateTime('1:59 pm');
            if ($now >= $begin && $now <= $end) {
                //Do Lunch
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Lunch"];
                $currentmeal = "Lunch";
            }

            //Dinner
            $now = new DateTime();
            $begin = new DateTime('7:00 pm');
            $end = new DateTime('7:59 pm');
            if ($now >= $begin && $now <= $end) {
                //Do Dinner
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Dinner"];
                $currentmeal = "Dinner";
            }

            //Send
            if ($currentmenu != "" && $currentmeal != ""){
                $menulist = implode(', ', $currentmenu);
                $mealid = $this->getMealID(strtolower($currentmeal), $menulist);
                $this->sendSMSRatingHelper($currentmenu, $currentmeal, $mealid);
            }

        } else if (date("D") == "Sun" || date("D") == "Sat"){

            //Brunch
            $now = new DateTime();
            $begin = new DateTime('11:00 am');
            $end = new DateTime('11:59 am');
            if ($now >= $begin && $now <= $end) {
                //Do Brunch
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Brunch"];
                $currentmeal = "Brunch";
            }
    
            //Dinner
            $now = new DateTime();
            $begin = new DateTime('7:00 pm');
            $end = new DateTime('7:59 pm');
            if ($now >= $begin && $now <= $end) {
                //Do Dinner
                $this->getMenu();
                $currentmenu = $this->todaysmenu["Dinner"];
                $currentmeal = "Dinner";
            }

            //Send
            if ($currentmenu != "" && $currentmeal != ""){
                if (date("Y-m-d") == "2020-11-21"){
                    //Special actions
                } else {
                    $menulist = implode(', ', $currentmenu);
                    $mealid = $this->getMealID(strtolower($currentmeal), $menulist);
                    $this->sendSMSRatingHelper($currentmenu, $currentmeal, $mealid);
                }
            }

        }

    }

    function sendSMSHelper($menu, $currentmeal, $mealid){
        $phonenumberlist = $this->getPhoneNumbers();

        if($currentmeal != "custom"){
            $textmessage = $currentmeal.": ";
            foreach($menu as $menuitem){
                if(++$i === count($menu)) {
                    $textmessage = $textmessage.$menuitem.".";
                } else {
                    $textmessage = $textmessage.$menuitem.", ";
                }
            }
        } else {
            $textmessage = $menu;
        }

        if (strlen($textmessage) > 156){
            $middle = strrpos(substr($textmessage, 0, floor(strlen($textmessage) / 2)), ', ') + 1;
            $string1 = substr($textmessage, 0, $middle);
            $string2 = substr($textmessage, $middle);
            
            $textarray = array($string1, $string2);
        } else {
            $textarray = array($textmessage);
        }

        foreach($phonenumberlist as $phonenumber){

            foreach($textarray as $textmsg){
                if ($this->sendSMS($phonenumber, $textmsg)){
                    continue;
                } else {
                    echo  "Text to ". $phonenumber. " was not sent.";
                }
            }
            
        }

    }

    function sendSMSRatingHelper($menu, $currentmeal, $mealid){
        $phonenumberlist = $this->getPhoneNumbers();

        foreach($phonenumberlist as $phonenumber){

            // Wether to poll person
            if (rand(0, 1)) {
                $phonenum = substr($phonenumber, 0, strpos($phonenumber, "@"));
                $textmsg = "Ate the meal? Rate it here: http://haverford.johnfinberg.com/?i=".$mealid."&p=".$phonenum;

                if ($this->sendSMS($phonenumber, $textmsg)){
                    continue;
                } else {
                    echo  "Text to ". $phonenumber. " was not sent.";
                }
            }
            
        }

    }

    function sendSMS($number, $message) {
        //Create a new PHPMailer instance
        $mail = new PHPMailer;

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        //Enable SMTP debugging
        // SMTP::DEBUG_OFF = off (for production use)
        // SMTP::DEBUG_CLIENT = client messages
        // SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        // use
        // $mail->Host = gethostbyname('smtp.gmail.com');
        // if your network does not support SMTP over IPv6

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = 587;

        //Set the encryption mechanism to use - STARTTLS or SMTPS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        $mail->Username   = $this->senderemail;
        $mail->Password   = $this->senderpassword;

        //Set who the message is to be sent from
        $mail->setFrom($this->senderemail, 'Haverford Menu');

        //Set an alternative reply-to address
        //$mail->addReplyTo('replyto@example.com', 'First Last');

        //Set who the message is to be sent to
        $mail->addAddress($number, '');

        //Set the subject line
        // $mail->Subject = 'PHPMailer GMail SMTP test';

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail->Body = $message;

        //send the message, check for errors
        // if (!$mail->send()) {
        //     echo 'Mailer Error: '. $mail->ErrorInfo;
        // } else {
        //     echo 'Message sent!';
        // }

        return $mail->send();
    }

    function getPhoneNumbers(){
        try {
            $conn = new PDO("mysql:host=$this->db_hostname;dbname=haverford_menu", $this->db_username, $this->db_password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $phonenumberlist = array();
        $stmt = $conn->query("SELECT phone_number,status FROM phone_numbers");
        while ($row = $stmt->fetch()) {
            if ($row["status"] == "active"){
                array_push($phonenumberlist,$row['phone_number']);
            }
        }

        return $phonenumberlist;
    }

    function getNumberStatus($phonenumber){
        try {
            $conn = new PDO("mysql:host=$this->db_hostname;dbname=haverford_menu", $this->db_username, $this->db_password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        // $stmt = $conn->prepare("SELECT * FROM phone_numbers WHERE phone_number=:phone LIMIT 1");
        // $phonenumber = "%$phonenumber%";
        // $stmt->execute(['phone' => $phonenumber]); 
        // $user = $stmt->fetch();
 
        //Our statement, which contains the LIKE comparison operator.
        $sql = "SELECT * FROM phone_numbers WHERE phone_number LIKE :phone LIMIT 1";
        
        //Here is where we add our wildcard character(s)!
        $name = "$phonenumber%";
        
        //We prepare our SELECT statement.
        $statement = $conn->prepare($sql);
        
        //We bind our $name variable to the :name parameter.
        $statement->bindValue(':phone', $name);
        
        //Execute statement.
        $statement->execute();
        
        //Fetch the result.
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) > 0){
            $response = array("exists"=>"true", "info"=>$results[0]);
        } else {
            $response = array("exists"=>"false", "info"=>$results[0]);
        }

        return $response;
    }

    function insertPhoneNumber($phonenumber,$carrier){

        try {
            $conn = new PDO("mysql:host=$this->db_hostname;dbname=haverford_menu", $this->db_username, $this->db_password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $data = [
            'phone_number' => $phonenumber."@".$carrier,
            'status' => 'active',
            'date_created' => date("Y-m-d h:i:sa")
        ];
        $sql = "INSERT INTO phone_numbers (phone_number, status, date_created) VALUES (:phone_number, :status, :date_created)";
        $stmt= $conn->prepare($sql);
        if ($stmt->execute($data)){
            $status = true;
        } else {
            $status = false;
        }
        
        return $status;

    }

    function getMealID($mealtype, $mealcontents) {
        
        try {
            $conn = new PDO("mysql:host=$this->db_hostname;dbname=haverford_menu", $this->db_username, $this->db_password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $stmt = $conn->prepare("SELECT * FROM meal_data WHERE meal_type=:type AND meal_contents=:content LIMIT 1");
        $stmt->execute(['type' => $mealtype, 'content' => $mealcontents]); 
        $data = $stmt->fetchAll();
        // and somewhere later:

        if (count($data) > 0){
            //already has id
            $response = $data[0]['id'];
        } else {
            //need to insert
            $data = [
                'meal_type' => $mealtype,
                'meal_contents' => $mealcontents,
                'date_created' => date("Y-m-d h:i:sa")
            ];
            $sql = "INSERT INTO meal_data (meal_type, meal_contents, date_created) VALUES (:meal_type, :meal_contents, :date_created)";
            $stmt= $conn->prepare($sql);
            if ($stmt->execute($data)){
                $stmt = $conn->prepare("SELECT * FROM meal_data WHERE meal_type=:type AND meal_contents=:content LIMIT 1");
                $stmt->execute(['type' => $mealtype, 'content' => $mealcontents]); 
                $data = $stmt->fetchAll();
                $response = $data[0]['id'];
            } else {
                $response = NULL;
            }
        }

        return $response;

    }

    function insertVote($mealid, $phonenum, $vote) {

        try {
            $conn = new PDO("mysql:host=$this->db_hostname;dbname=haverford_menu", $this->db_username, $this->db_password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $stmt = $conn->prepare("SELECT * FROM meal_votes WHERE phone_number=:phonenum AND meal_id=:mealid AND date_created LIKE :datecreated LIMIT 1");
        $stmt->execute(['phonenum' => $phonenum, 'mealid' => $mealid, 'datecreated' => date("Y-m-d")."%"]); 
        $data = $stmt->fetchAll();

        if (count($data) > 0){
            // Already votes
            $response = false;
        } else {
            $data = [
                'meal_id' => $mealid,
                'phone_number' => $phonenum,
                'vote' => $vote,
                'date_created' => date("Y-m-d h:i:sa")
            ];
            $sql = "INSERT INTO meal_votes (meal_id, phone_number, vote, date_created) VALUES (:meal_id, :phone_number, :vote, :date_created)";
            $stmt= $conn->prepare($sql);
            if ($stmt->execute($data)){
                $response = true;
            } else {
                $response = false;
            }
        }
        
        return $response;

    }

    function updatePhoneNumberPrefs($id, $status){

        try {
            $conn = new PDO("mysql:host=$this->db_hostname;dbname=haverford_menu", $this->db_username, $this->db_password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $data = [
            'status' => $status,
            'id' => $id,
        ];
        $sql = "UPDATE phone_numbers SET status=:status WHERE id=:id";
        $stmt= $conn->prepare($sql);
        
        if ($stmt->execute($data)){
            $response = true;
        } else {
            $response = false;
        }

        return $response;

    }

}

?>