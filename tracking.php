<?php
 
//The location of the mailbox.
$mailbox = '{imap.gmail.com:993/ssl/novalidate-cert}';
//The username / email address that we want to login to.
$username = 'speaktoushere@gmail.com';
//The password for this email address.
$password = 'parumaya';
 
//Attempt to connect using the imap_open function.
$imapResource = imap_open($mailbox, $username, $password);
 
//If the imap_open function returns a boolean FALSE value,
//then we failed to connect.
if($imapResource === false){
    //If it failed, throw an exception that contains
    //the last imap error.
    throw new Exception(imap_last_error());
}
 
//If we get to this point, it means that we have successfully
//connected to our mailbox via IMAP.
 
//Lets get all emails that were received since a given date.
$search = 'SINCE "' . date("j F Y", strtotime("-7 days")) . '"';
$emails = imap_search($imapResource, $search);
 
//If the $emails variable is not a boolean FALSE value or
//an empty array.
if(!empty($emails)){
    //Loop through the emails.
    foreach($emails as $email){
        //Fetch an overview of the email.
        $overview = imap_fetch_overview($imapResource, $email);
        $overview = $overview[0];
        //Print out the subject of the email.
        echo '<b>' . htmlentities($overview->subject) . '</b><br>';
        //Print out the sender's email address / from email address.
        echo 'From: ' . $overview->from . '<br><br>';
        //Get the body of the email.
        $message = imap_fetchbody($imapResource, $email, 1, FT_PEEK);
    }
}