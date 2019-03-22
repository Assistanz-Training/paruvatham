<?php
	$username = 'speaktoushere@gmail.com';
	$password = 'parumaya';
	//$imapmainbox = "INBOX";
	//Select messagestatus as ALL or UNSEEN which is the unread email
	$messagestatus = "ALL";
	$hostname = "{imap.gmail.com:993/ssl/novalidate-cert}";
	$connection = imap_open($hostname,$username,$password);
	$emails = imap_search($connection,$messagestatus);
	$totalemails = imap_num_msg($connection);
	echo "Total Emails: " . $totalemails . "<br>";
	$conn = mysqli_connect("localhost","root","","mailtrack");
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	echo "Connected successfully"."<br>";
	if($emails) {
		//sort emails by newest first
		rsort($emails);
		//loop through every email int he inbox
		foreach($emails as $email_number) {
			//grab the overview and message
			$header = imap_fetch_overview($connection,$email_number,0);
			//Because attachments can be problematic this logic will default to skipping the attachments    
			$message = imap_fetchbody($connection,$email_number,1.1);
			$message = imap_fetchbody($connection, $email_number, 1);
			$messageExcerpt = substr($message, 0, 150);
			$partialMessage = trim(quoted_printable_decode($messageExcerpt)); 
			//split the header array into variables
			$status = ($header[0]->seen ? 'read' : 'unread');
			$subject = $header[0]->subject;
			$from = $header[0]->from;
			$date = $header[0]->date;
			echo "status: " . $status. "<br>";
			echo "subject: " . $subject. "<br>";
			echo "from: " . $from. "<br>";
			echo "date: " . $date . "<br>";
			echo "message: " . $partialMessage . "<br>";
			$str=parse_str("from=$from \n status=$status&subject=$subject&date=$date&message=$partialMessage", $array); 
			print_r($array); 
			echo"<br>";
			$int = (int) filter_var($subject, FILTER_SANITIZE_NUMBER_INT);
			echo "Ticket Id: ".$int."<br><hr><br>";
			$sql="INSERT INTO `mailtrack`.`tbl_maildetails` (`from`,`status`,`subject`,`message`,`date`)VALUES ('$from','$status','$subject','$partialMessage','$date')";
			if (mysqli_query($conn, $sql)) {
				echo "New record created successfully";
			} 
			else {
				echo "Error: " . $sql . "<br>" . mysqli_error($conn);
			}
		}  
	} 
mysqli_close($conn);
imap_close($connection);
?>