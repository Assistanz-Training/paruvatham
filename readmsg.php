<?php
	$username = 'speaktoushere@gmail.com';
    $password = 'parumaya';
	$imapmainbox = "INBOX";
	$messagestatus = "UNSEEN";
	$hostname = "{imap.gmail.com:993/ssl/novalidate-cert}";
    $connection = imap_open($hostname, $username, $password);
    $usermails = array();
    $usermsg = array();
    $emails = imap_search($connection, $messagestatus);
	$totalemails = imap_num_msg($connection);
	echo "Total Emails:".$totalemails."<br>";
    $nom = 0;
    if ($emails) {
		rsort($emails);
        foreach ($emails as $email_number) {
			$headerInfo = imap_headerinfo($connection, $email_number);
            $header = imap_fetch_overview($connection, $email_number, 0);
			$message = imap_fetchbody($connection, $email_number, 1.1);
            $message = imap_fetchbody($connection, $email_number, 1);
            $messageExcerpt = substr($message, 0, 150);
            $partialMessage = trim(quoted_printable_decode($messageExcerpt));
			$status = ($header[0]->seen ? 'read' : 'unread');
            $subject = $header[0]->subject;
            $date = $header[0]->date;
            $output = $headerInfo->from[0]->mailbox . "@" . $headerInfo->from[0]->host;
            $ticketstring =strtoupper($subject);
            $txtToFind = 'ID';
			$pos = strpos($ticketstring, $txtToFind);
			if ($pos !== false) {
				$ticketid = substr($ticketstring, $pos + 4);
            } 
			else {
                $ticketid = "Not Found";
            }
            $conn = mysql_connect('localhost','root','');
			mysql_select_db('issue_tracking', $conn);
            $q = mysql_query("INSERT INTO `tbl_inbox`(`author`, `subject`, `message`, `date`, `ticket id`) VALUES('$output','$subject','$message','$date','$ticketid')") or die(mysql_error());
			echo "status: " . $status . "<br>";
			echo "Author : " . $output. "<br>";
			echo "subject: " . $subject . "<br>";
			echo "date: " . $date . "<br>";
        	echo "message: " . $message . "<br>";
			echo "Ticket ID: ".$ticketid."<br><hr><br>";
        	$nom++;
			$usermails[$nom] = $output;
            $usermsg[$nom] = $message;
        }
	}
	imap_close($connection);
	$conn = mysql_connect('localhost','root','');
	mysql_select_db('issue_tracking', $conn);
    if ($nom > 0) {
        for ($i = 1; $i <= $nom; $i++) {
			$toemail = $usermails[$i];
            $count = 0;
            $origin = $usermsg[$i];
            $found = array();
			$q = mysql_query("select * from tbl_keywords");
            while ($r = mysql_fetch_array($q)) {
                $keyword = $r['keyword'];
                if (strpos($origin, $keyword) !== false) {
                    $count++;
                    $found[$count] = $r['issue_id'];
                }
            }
			if (count($found) > 0) {
                $vals = array_count_values($found);
                $resultval = 0;
                $resultkey = 0;
                foreach ($vals as $key => $value) {
					if ($value > $resultval) {
						$resultval = $value;
                        $resultkey = $key;
                    }
                }
                $q = mysql_query("SELECT * FROM `tbl_issues` WHERE id=$resultkey");
				$r = mysql_fetch_array($q);
                echo "Replymsg: " . $r['replymsg'] . "<br>";
                $resultkey = 0;
                $replymsg = $r['replymsg'];
			}
		}
    }
?>