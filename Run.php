<?php
/**
 * Created by PhpStorm.
 * User: Michael Marshal
 * Date: 11/9/2017
 * Time: 4:51 PM
 */

    ini_set('error_log', 'error.log');                                 //initializing an error log. Useful when something goes wrong :)
    include 'lib/SMSSender.php';                                    //include SMSSender library file.This will be used to send SMS data
    include 'lib/SMSReceiver.php';                                //include SMSReceiver Library file. This will be used to get SMS data
    date_default_timezone_set("Asia/Colombo");                                        //Setting up a timezone


//Application ID and Pasasword will be provided by the Ideamart team after creation of the Ideamart application. 
//After the approval you can use this.

    $applicationId = "<Your application ID>";
    $password = "<Your Password>";

//To send SMS we have to talk to this URL
    $serverurl = "https://api.dialog.lk/sms/send";

    //to handle exceptions we surround our code with Try Catch block
        try {


            $receiver = new SMSReceiver(file_get_contents('php://input'));//this line will get the data which comes to our php file 
                                                                          //and using SMSReceiver which in is SMSReceiver class
                                                                           //we create new object $receiver
            $content = $receiver->getMessage();                            // In this line we get the Message the user has sent us
            $content = preg_replace('/\s{2,}/', ' ', $content);   // By using preg_replace we replace the unwanted pattern with space
            $address = $receiver->getAddress();                                                     //Here we get the user's MSISDN

            $sender = new SMSSender($serverurl, $applicationId, $password); // Here we initiate the SMSSender which is used to send SMS


            list($key, $context) = explode(" ", $content);// As we require the second word after the Keyword here
                                                          //I have used explode function to get the second word which 
                                                           //comes after a space

            $random = mt_rand(1, 86); // We require a random value to be passed along with the url as to get random names

            $json = file_get_contents("https://swapi.co/api/people/$random");// through this line we access the API url and
                                                                               //get the relevant data

            $decode = json_decode($json, true); // As the data is in json format we have to decode it so that we can easily use it.


            if ($context == "male") {

                $gender = $decode['gender'];      // getting the gender of the received data

                while ($x < 87) {                  // There are 87 named characters in Star wars
                    if ($gender == "male") {

                        $Name_url = $decode['url'];                  // If the gender is male to get the name we use the same url

                        $json_name = file_get_contents($Name_url);    // To get the data for the specific gender and random
                                                                        //character name

                        $decode_name = json_decode($json_name, true);        // decoding the acquired data

                        $name = $decode_name['name'];         //getting the required name from the data acquired

                        $sender->sendMessage('You are ' . $name, $address);      //Sending out the message using sender object

                        break;              // If i leave the while loop open the loop will not end until it executes 87 times

                    }
                    $x++;
                }


            } elseif ($context == "female") {

                $gender = $decode['gender'];   // getting the gender of the received data

                while ($x < 87) {      // There are 87 named characters in Star wars
                    if ($gender == "female") {

                        $Name_url = $decode['url'];        // If the gender is female to get the name we use the same url

                        $json_name = file_get_contents("https://swapi.co/api/people/$random/");  // To get the data for the specific 
                                                                                                 //gender and random character name

                        $decode_name = json_decode($json_name, true);                                // decoding the acquired data

                        $name = $decode_name['name'];     //getting the required name from the data acquired

                        $sender->sendMessage('You are ' . $name, $address);       //Sending out the message using sender object

                        break;     // If i leave the while loop open the loop will not end until it executes 87 times

                    }
                    $x++;
                }

            }
            if (!$name) {       // This If blocks checks if the name is returned null or empty if that is the case the specified
                                //message is sent
                $sender->sendMessage('oops something went wrong please try again. May the force be with you', $address);
            }
        } catch (SMSServiceException $e) {
            error_log("Passed Exception-not working " . $e);  // If there is an exception this will not down the exception 
                                                              //in error log which may be useful for reference.
        }
