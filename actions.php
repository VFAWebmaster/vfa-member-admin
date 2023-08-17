<?php
/* -------------------------------------------------------------------------
vfa_birthdays() provides a routine to find who has a birthday today and send greetings via cron job
Created 180708 by DAV 
---------------------------------------------------------------------------*/
add_action( 'vfa-birthdays', 'vfa_birthdays' );
function vfa_birthdays() {
	// get birthdays from tng_people, see if member in vfa_members, and send birthday greetings if they have email

	global $wpdb, $host, $database, $user, $password, $tableheader, $rowstart, $rowend, $tableender;
	global $president_email, $webmaster_email, $webmaster_password, $webmaster_from_name;
	global $noreply_email, $noreply_password, $noreply_from_name;
	global $smtp_host, $smtp_password, $smtp_secure, $smtp_port, $smtp_auth;

	$subject = "Happy Birthday";
	$message = "Happy Birthday from the Violette Family Association! We hope you have a happy day.";
	include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 
  $mail = new PHPMailer;
  $mail->SMTPDebug = 0;                               
  $mail->isSMTP();            
  $mail->Host = $smtp_host;
  $mail->SMTPAuth = $smtp_auth;                          
  $mail->Username = $webmaster_email;                 
  $mail->Password = $webmaster_password;                           
  $mail->SMTPSecure = $smtp_secure;                           
  $mail->Port = $smtp_port;                                   
  $mail->From = $president_email;
	$mail->FromName = $smtp_from_name;
	$mail->addCC($webmaster_email);
	$mail->Subject = $subject;
	$mail->Body = $message;
	$mail->AltBody = $message;

  $cxn = new mysqli($host, $user, $password, $database);
  if ($cxn->connect_error) {
    die ('Error : ('. $cxn->connect_errno .') '. $cxn->connect_error);
	}
	
	// AND Month(tng_people.birthdatetr) = 8 AND Day(tng_people.birthdatetr) = 10";
	// AND Month(tng_people.birthdatetr) = ".$this_month." AND Day(tng_people.birthdatetr) = ".$this_day;
  date_default_timezone_set('America/Phoenix');
	$this_date = getdate(date("U"));
	$this_month = $this_date[mon];
	$this_day = $this_date[mday];
	$query = "SELECT tng_people.personID, tng_people.birthdatetr, tng_people.deathdatetr, vfa_members.username, vfa_members.m_first_name, vfa_members.m_last_name, vfa_members.tng_personID, vfa_members.m_email 
	FROM tng_people INNER JOIN vfa_members ON vfa_members.tng_personID = tng_people.personID
	WHERE tng_people.deathdatetr = '0000-00-00' AND vfa_members.m_email <> '' AND (vfa_members.m_status = 'A' OR vfa_members.m_status = 'C')
  AND Month(tng_people.birthdatetr) = ".$this_month." AND Day(tng_people.birthdatetr) = ".$this_day;
	$result = $cxn->prepare( $query );
	$result->execute();
	$result->store_result();
	$num_rows = $result->num_rows();
	if ($num_rows > 0) {  // do we have any records?
		$result->bind_result($person, $b_date, $dod, $username, $first_name, $last_name, $personID, $email);
		while ($result->fetch()) {
			$display_name = $first_name." ".$last_name;
			$mail->addAddress($email, $display_name);
			$mail->addCC($webmaster_email);
			$mail->Subject = "Happy Birthday";
			$body = "<i>Hello, ".$display_name.", ".$username."<br/><br/>";
			$body .= "It's your birthday and the Violette Family Association hopes you have a happy one! </i>";
			$body .= "Be sure to keep your email address up-to-date so you can receive news and other items!<br/><br/>";
			$body .= "Go to <a href='http://VioletteRegistry.com/FamilyTree'>VioletteRegistry/FamilyTree</a> to see what family tree info we have for you. Contact Rod Violette, our Genealogist, for any questions or updates to your family tree. You can use <a href='mailto:rviolette@att.net?subject=".$display_name.", ".$username."'>this link</a> to reach Rod by email or look for his contact info in the footer of any page at our web site.<br/><br/>";
			$body .= "Contact me if you have any membership questions. You can use <a href='mailto:President@VioletteFamily.org?subject=".$display_name.", ".$username."'>this link</a> to reach me by email or look for my contact info in the footer of any page at our web site<br/><br/>";
			$body .= "David A. Violette, VFA #621, President/Webmaster<br/>";
			$body .= "Violette Family Association, 2050 W Dunlap Ave Lot D54, Phoenix AZ 85021";
			$mail->Body = $body;
			$mail->AltBody = $body;
      if(!$mail->send()) {
        $outcome = "Mailer Error: " ." Mbr ".$username.", ". $mail->ErrorInfo;
      } else {
        $outcome = "Message has been sent successfully to ".$username;
      }
			$mail->clearAddresses();
		}
	}
	// AND Month(tng_people.birthdatetr) = 1 AND Day(tng_people.birthdatetr) = 14";
	// AND Month(tng_people.birthdatetr) = ".$this_month." AND Day(tng_people.birthdatetr) = ".$this_day;
	$query = "SELECT tng_people.personID, tng_people.birthdatetr, tng_people.deathdatetr, vfa_members.username, vfa_members.s_first_name, vfa_members.s_last_name, vfa_members.tng_spouseID, vfa_members.s_email 
	FROM tng_people INNER JOIN vfa_members ON vfa_members.tng_spouseID = tng_people.personID
	WHERE tng_people.deathdatetr = '0000-00-00' AND vfa_members.s_email <> '' AND (vfa_members.s_status = 'A' OR vfa_members.s_status = 'C')
	AND Month(tng_people.birthdatetr) = ".$this_month." AND Day(tng_people.birthdatetr) = ".$this_day;
	$result_s = $cxn->prepare( $query );
	$result_s->execute();
	$result_s->store_result();
	$num_rows = $result_s->num_rows();
	if ($num_rows > 0) {  // do we have any records?
		$result_s->bind_result($person, $b_date, $dod, $username, $first_name, $last_name, $personID, $email);
		while ($result_s->fetch()) {
			$display_name = $first_name." ".$last_name;
			// $mail->addAddress($email, $display_name);
			$mail->addCC($webmaster_email);
			$mail->Subject = "Happy Birthday";
			$body = "<i>Hello, ".$display_name.".<br/><br/>";
			$body .= "It's your birthday and the Violette Family Association hopes you have a happy one! </i>";
			$body .= "Be sure to keep your email address up-to-date so you can receive news and other items!<br/><br/>";
			$body .= "Go to <a href='http://VioletteRegistry.com/FamilyTree'>VioletteRegistry/FamilyTree</a> to see what family tree info we have for you. Contact Rod Violette, our Genealogist, for any questions or updates to your family tree. You can use <a href='mailto:rviolette@att.net?subject=".$display_name.", ".$username."'>this link</a> to reach Rod by email or look for his contact info in the footer of any page at our web site.<br/><br/>";
			$body .= "Contact me if you have any membership questions. You can use <a href='mailto:President@VioletteFamily.org?subject=".$display_name.", ".$username."'>this link</a> to reach me by email or look for my contact info in the footer of any page at our web site<br/><br/>";
			$body .= "David A. Violette, VFA #621, President/Webmaster<br/>";
			$body .= "Violette Family Association, 2050 W Dunlap Ave Lot D54, Phoenix AZ 85021<br/>";
			$body .= "Ref: ".$username;
			$mail->Body = $body;
			$mail->AltBody = $body;
      if(!$mail->send()) {
        $outcome = "Mailer Error: " ." Mbr ".$username.", ". $mail->ErrorInfo;
      } else {
        $outcome = "Message has been sent successfully to ".$username;
      }
			$mail->clearAddresses();
		}
	}
  return $outcome;
}
/* -------------------------------------------------------------------------
vfa_child_update() provides a routine to update classification from Child to Member upon reaching 18 via cron job
Created 180711 by DAV 
---------------------------------------------------------------------------*/
add_action( 'vfa-child_update', 'vfa_child_update' );
function vfa_child_update() {
	global $wpdb, $host, $database, $user, $password, $tableheader, $rowstart, $rowend, $tableender;
  global $genealogist_email, $webmaster_email, $president_email, $president_password, $smtp_host, $smtp_auth, $smtp_from_name, $smtp_secure, $smtp_port;

	include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 
  $mail = new PHPMailer;
  $mail->SMTPDebug = 0;                               
  $mail->isSMTP();            
  $mail->Host = $smtp_host;
  $mail->SMTPAuth = $smtp_auth;                          
  $mail->Username = $president_email;                 
  $mail->Password = $president_password;                           
  $mail->SMTPSecure = $smtp_secure;                           
  $mail->Port = $smtp_port;                                   
  $mail->From = $president_email;
  $mail->FromName = $smtp_from_name;

  $cxn = new mysqli($host, $user, $password, $database);
  if ($cxn->connect_error) {
    die ('Error : ('. $cxn->connect_errno .') '. $cxn->connect_error);
	}
	
	// AND Month(tng_people.birthdatetr) = 8 AND Day(tng_people.birthdatetr) = 10";
	// AND Month(tng_people.birthdatetr) = ".$this_month." AND Day(tng_people.birthdatetr) = ".$this_day;
  $this_date = getdate(date("U"));
  $this_year = $this_date[year];
  $birth_year = $this_year - 18;
	$this_month = $this_date[mon];
	$this_day = $this_date[mday];
  // AND Year(tng_people.birthdatetr) <= ".$birth_year. " AND Month(tng_people.birthdatetr) = ".$this_month." AND Day(tng_people.birthdatetr) = ".$this_day;
	$query = "SELECT tng_people.personID, tng_people.birthdatetr, vfa_members.username, vfa_members.m_classif, vfa_members.tng_personID, vfa_members.m_email, vfa_members.m_first_name, vfa_members.m_last_name 
	FROM tng_people INNER JOIN vfa_members ON vfa_members.tng_personID = tng_people.personID
	WHERE vfa_members.m_classif = 'C'
  AND tng_people.birthdatetr <= '".$birth_year."-".$this_month."-".$this_day."'";
	$result = $cxn->prepare( $query );
	$result->execute();
	$result->store_result();
  $num_rows = $result->num_rows();
  $outcome = "rows= ".$num_rows.":";
	if ($num_rows > 0) {  // do we have any records?
		$result->bind_result($person, $b_date, $username, $classif, $personID, $email, $first_name, $last_name);
		while ($result->fetch()) {
      $classif = "M";
			$table = 'vfa_members';
			$data = array('m_classif' => $classif );
			$where = array( 'username' => $username );
			$update = $wpdb->update($table, $data, $where);
      $outcome .= $username.", ";
      
      // send email if we have one
      if ( !empty($email) ) {
        $display_name = $first_name." ".$last_name;
        $mail->addAddress($email, $display_name);
        $mail->addCC($webmaster_email);
        $mail->Subject = "Your Violette Family Association classification";
        $body = "<i>Hello, ".$display_name.", ".$username."<br/><br/>";
        $body .= "It's your 18th birthday and the Violette Family Association hopes you have a happy one!";
        $body .= "Now that you are over 18 you are no longer a Child Member of the Association but a Member. </i>";
        $body .= "Be sure to keep your email address up-to-date so you can receive news and other items!<br/><br/>";
        $body .= "Go to <a href='http://VioletteRegistry.com/FamilyTree'>VioletteRegistry/FamilyTree</a> to see what family tree info we have for you. Contact Rod Violette, our Genealogist, for any questions or updates to your family tree. You can use <a href='mailto:rviolette@att.net?subject=".$display_name.", ".$username."'>this link</a> to reach Rod by email or look for his contact info in the footer of any page at our web site.<br/><br/>";
        $body .= "Contact me if you have any membership questions. You can use <a href='mailto:President@VioletteFamily.org?subject=".$display_name.", ".$username."'>this link</a> to reach me by email or look for my contact info in the footer of any page at our web site<br/><br/>";
        $body .= "David A. Violette, VFA #621, President/Webmaster<br/>";
        $body .= "Violette Family Association, 2050 W Dunlap Ave Lot D54, Phoenix AZ 85021";
        $mail->Body = $body;
        $mail->AltBody = $body;
        if(!$mail->send()) {
          $outcome = "Mailer Error: " ." Mbr ".$username.", ". $mail->ErrorInfo;
        } else {
          $outcome = "Message has been sent successfully to ".$username;
        }
        $mail->clearAddresses();
  
      }
		}
	}
  return $outcome;
}
/* -------------------------------------------------------------------------
Cron job functions used in this module
Created 180708 ff by DAV 
---------------------------------------------------------------------------*/
add_action( 'cron_check', 'cron_check' );
function cron_check() {
  global $webmaster_email, $webmaster_password, $webmaster_from_name, $smtp_host, $smtp_auth, $smtp_secure, $smtp_port;
  include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 
  $mail = new PHPMailer;
  $mail->isSMTP();            
  $mail->SMTPDebug = 0;                               
  $mail->Host = $smtp_host;
  $mail->SMTPAuth = $smtp_auth;                          
  $mail->SMTPSecure = $smtp_secure;                           
  $mail->Port = $smtp_port;                                   
  $mail->Username = $webmaster_email;                 
  $mail->Password = $webmaster_password;                           
  $mail->From = $webmaster_email;
  $mail->FromName = $webmaster_from_name;
  $mail->addAddress("David@Violette.com", "David Violette");
  $mail->isHTML(true);
  $mail->Subject = "VFA cron check";
  $body = "A routine check using cron job at ".date("h:i:sa");
  $mail->Body = $body;
  $mail->AltBody = $body;
  // $mail->send;
  if(!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
  } else {
    echo "Message to Dave has been sent successfully";
  }
  return $message;
}
?>