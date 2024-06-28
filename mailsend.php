<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
<title>This message is sending now ...</title>
</head>

<body>
<br><h1>This message is sending now ...</h1>
<?php 
/*            if (empty($_POST['knownsender'])) {
                $_POST['knownsender'] = '';
            } else {
                $_POST['knownsender'] = str_replace(array("\r\n", "\n", "\r", ","), "", $_POST['knownsender']);
            }
            if (empty($_POST['recipients'])) {
                $_POST['recipients'] = '';
            } else {
                $_POST['recipients'] = str_replace(array("\r\n", "\n", "\r", ","), "", $_POST['recipients']);
            }
            if (empty($_POST['ccaddress'])) {
                $_POST['ccaddress'] = '';
            } else {
                $_POST['ccaddress'] = str_replace(array("\r\n", "\n", "\r", ","), "", $_POST['ccaddress']);
            }
            if (empty($_POST['subject'])) {
                $_POST['subject'] = '';
            } else {
                $_POST['subject'] = str_replace(array("\r\n", "\n", "\r"), "", $_POST['subject']);
            }
            if (empty($_POST['message'])) {
                $_POST['message'] = '';
            }*/
			
//			$knownsender = "veekay2003@rediffmail.com";
            $mailtos = "veekay2003@gmail.com";
            $subject = "test mail";
            $message = "This is a test mail";
			$ccaddress = "vinod.yadav@vnrseeds.com";
			$headers = 'MIME-Version: 1.0' . "\r";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: kamlesh.patidar@gmail.com'. "\r\n" . "\r" ;
//			$headers .= "Cc: $ccaddress";
			
/*            if (trim($ccaddress) == "") {
                $header = "From: $knownsender";
            } else {
                $header .= "From: $knownsender";
                $header .= "Cc: $ccaddress";
            }*/

            if (@mail($mailtos, $subject, $message, $headers)) {
                echo "<p><i>The message was successfully sent!</i></p>";
            } else {
                echo "<p><i>Error! The message was not successfully sent!</i></p>";
            }
?>
<!---        <p><a href="javascript:history.back()">click here to return back</a></p>  -->
<p><a href="indentorder.php?action=new">click here to return back</a></p>
</body>
</html>