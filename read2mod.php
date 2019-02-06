<?php
$username = 'speaktoushere@gmail.com';
$password = 'parumaya';
 
//Which folders or label do you want to access? - Example: INBOX, All Mail, Trash, labelname 
//Note: It is case sensitive
//$imapmainbox = "INBOX";
 
//Select messagestatus as ALL or UNSEEN which is the unread email
$messagestatus = "ALL";
 
//-------------------------------------------------------------------
 
//Gmail Connection String
$hostname = "{imap.gmail.com:993/ssl/novalidate-cert}";
 
//Gmail host with folder

 
//Open the connection
$connection = imap_open($hostname,$username,$password);
 
//Grab all the emails inside the inbox
$emails = imap_search($connection,$messagestatus);
 
//number of emails in the inbox
$totalemails = imap_num_msg($connection);
  
echo "Total Emails: " . $totalemails . "<br>";
  
if($emails) {
  
  //sort emails by newest first
  rsort($emails);
  
  //loop through every email int he inbox
  foreach($emails as $email_number) {
    
    //grab the overview and message
    $header = imap_fetch_overview($connection,$email_number,0);
 
    //Because attachments can be problematic this logic will default to skipping the attachments    
    $message = imap_fetchbody($connection,$email_number,1.1);
    //if ($message == "") { // no attachments is the usual cause of this
       $message = imap_fetchbody($connection, $email_number, 1);
	   $messageExcerpt = substr($message, 0, 150);
	   $partialMessage = trim(quoted_printable_decode($messageExcerpt)); 
    //}
    
    //split the header array into variables
    $status = ($header[0]->seen ? 'read' : 'unread');
    $subject = $header[0]->subject;
    $from = $header[0]->from;
    $date = $header[0]->date;
    echo "status: " . $status. "<br>";
    echo "subject: " . $subject. "<br>";
    echo "from: " . $from. "<br>";
    echo "date: " . $date . "<br>";
    echo "message: " . $partialMessage . "<br><hr><br>";
 
//This is where you would want to start parsing your emails, send parts of the email into a database or trigger something fun to happen based on the emails.
 
  }  
} 
 
// close the connection
imap_close($connection);
?>