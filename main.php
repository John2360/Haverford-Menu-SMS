<?php
require './haverford-menu.php';
header('Content-Type: application/json');

$errors         = array();      // array to hold validation errors
$data           = array();      // array to pass back data

// validate the variables ======================================================
// if any of these variables don't exist, add an error to our $errors array

if (empty($_POST['phone']))
    $errors['phone'] = 'Phone number is required.';
if (empty($_POST['request']))
    $errors['request'] = 'Request number is required.';

// return a response ===========================================================

// if there are any errors in our errors array, return a success boolean of false
if ( ! empty($errors)) {

    // if there are items in our errors array, return those errors
    $data['success'] = false;
    $data['errors']  = $errors;
} else {

    // if there are no errors process our form, then return a message

    // DO ALL YOUR FORM PROCESSING HERE
    // THIS CAN BE WHATEVER YOU WANT TO DO (LOGIN, SAVE, UPDATE, WHATEVER)
    $menu = new HaverfordMenu();

    switch ($_POST['request']) {
        case "status":
            $data['info'] = $menu->getNumberStatus($_POST['phone']);
            break;
        case "register":
            $data['info'] = $menu->insertPhoneNumber($_POST['phone'], $_POST['carrier']);
            break;
        case "vote":
            $data['info'] = $menu->insertVote($_POST['id'],$_POST['phone'],$_POST['value']);
        case "update":
            $data['info'] = $menu->updatePhoneNumberPrefs($_POST['id'],$_POST['status']);
        default:
            $data['success'] = false;
            $data['message'] = 'Fail!';
    }

    // show a message of success and provide a true success variable
    $data['success'] = true;
    $data['message'] = 'Success!';
}

// return all our data to an AJAX call
echo json_encode($data);

?>