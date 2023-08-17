<?php

/* -------------------------------------------------------------------------
Global variables for all functions in this file
Created 180315 by DAV 
---------------------------------------------------------------------------*/
// require_once(ABSPATH .'/wp-load.php');
// require_once('/home4/o3i4q5u6/public_html/violettefamily.org/wp-load.php' );

// Global variables
// require_once('/home4/o3i4q5u6/public_html/violettefamily.org/wp_includes/pluggable.php' );
// $current_user = wp_get_current_user();
// $username = $current_user->user_login;
$host = "localhost";
$user = "dmj7jvgp_wrdp";
$password = "Ek[=xEL-@xQx";
$database = "dmj7jvgp_wrdp";
// table components
$tableheader = "<table class='datatable'><tbody>";
$rowstart = "<tr>";
$rowend = "</tr>";
$tableender = "</tbody></table>";
$page_position = 0;
$items_per_page = 20;
// SMTP parameters
$genealogist_email = "peteviolette47@gmail.com";
$webmaster_email = "Webmaster@VioletteRegistry.com";
$webmaster_from_name = "Webmaster";
$webmaster_password = "vrWebs^er6";
$president_email = "President@VioletteFamily.org";
$president_from_name = "President";
$president_password = "vfWebs^er3";
$smtp_host = "mail.VioletteFamily.org";
// $smtp_host = "cloud277.hostgator.com";
$smtp_auth = TRUE;
$smtp_secure = TRUE;
$smtp_port = 465;
$smtp_from_name = "David A. Violette (VFA #621)";
// MailChimp parameters
// $mc_vfa_api = "6cf21a268ce7550556dbbc559e8b9fbc-us4";
// $mc_vfa_list_id = "5a9b003581"; // VFA eNewsletters list
$apikey = "6cf21a268ce7550556dbbc559e8b9fbc-us4"; // Violette Family Association
$listid = "dd83a71808"; // VFA Exec Comm (used for testing)
// $listid = "5a9b003581"; // VFA eNewsletters list
// role tests: higher levels include all lower level tests
// refer to role/capability table at https://wordpress.org/support/article/roles-and-capabilities/
$administrator = "manage_options";
$editor = "manage_categories";
$author = "publish_posts";
$contributor = "edit_posts";
$board = ""; // inherits subscriber. no WP functions, used for menu and access control
$membership = ""; // inherits subscriber. no WP functions, used for menu and access control

/* -------------------------------------------------------------------------
General PHP functions used in this module
Created 180209 ff by DAV 
---------------------------------------------------------------------------*/
function test_input( $data ) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function classif_name( $abbrev ) {
	if($abbrev == "M") {
		$str = "Member";
	}
	elseif($abbrev == "C") {
		$str = "Child";
	}
	elseif($abbrev == "A") {
		$str = "Associate";
	}
	else {
		$str = "";
	}
	return $str;
}

function status_name( $abbrev ) {
	if($abbrev == "A") {
		$str = "Active";
	}
	elseif($abbrev == "D") {
		$str = "Deceased";
	}
	elseif($abbrev == "M") {
		$str = "Missing";
	}
	else {
		$str = "";
	}
	return $str;
}

function full_name( $first, $middle, $last ) {
	$name = $first. " ";
	if($middle !== "") {
		$name .= $middle. " ";
	}
	$name .= $last;

	return $name;
}

function format_phone_number($phone_number) {
	// from https://jrtashjian.com/2009/03/code-snippet-validate-a-phone-number/
  $regex = preg_match('/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/', $phone_number, $matches);
  if($matches[0] == "") {
    return "(None)";
  }
  else {
    return '(' .$matches[2]. ') ' .$matches[3]. '-' .$matches[4];
  }
}

function format_postal_code($postalcode) {
		$regex = preg_match('/^(?!00000)(?<zip>(?<zip5>\d{5})(?:[ -](?=\d))?(?<zip4>\d{4})?)|(?<full>(?<part1>[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1})(?:[ ](?=\d))?(?<part2>\d{1}[A-Z]{1}\d{1}))$/', $postalcode, $matches);
		if($matches[3]=="") {
			return $matches[2];
		}else{
			return $matches[2]. '-' .$matches[3];
		}
}

function deformat_phone_number($phone_number) {
	$result = str_replace("-", "", $phone_number);
	$result = str_replace(".", "", $result);
	$result = str_replace("+", "", $result);
	$result = str_replace("(", "", $result);
	$result = str_replace(")", "", $result);
	$result = str_replace(" ", "", $result);
	return $result;
}

function deformat_postal_code($postalcode) {
	$result = str_replace("-", "", $postalcode);
	$result = str_replace(".", "", $result);
	$result = str_replace(" ", "", $result);
	return $result;
}

function vfa_getmemberID( $username) {
	// get member's name and VFA
	global $wpdb;
	$query = "SELECT m_vfa, m_first_name, m_middle_name, m_last_name 
		FROM vfa_members 
		WHERE username = %s";
	$result = $wpdb->prepare( $query, $username );
	$rowdata = $wpdb->get_row( $result, ARRAY_A);
	$vfa_id = str_replace ( "VFA", "VFA #", $username );
	$m_firstname = $rowdata['m_first_name'];
	$m_middlename = $rowdata['m_middle_name'];
	$m_lastname = $rowdata['m_last_name'];
	$m_fullname = $m_firstname ." ". $m_middlename ." ". $m_lastname;
	return $vfa_id. ", ".$m_fullname;
}

function vfa_person_data( $personID) {
	// get member's name and VFA and other info from their tng_personID
  global $wpdb;
	$query = "SELECT username, m_vfa, m_first_name, m_middle_name, m_last_name, m_status, m_email, s_first_name, s_last_name, s_status, s_email 
		FROM vfa_members 
		WHERE tng_personID = %s";
	$result = $wpdb->prepare( $query, $personID );
  $rowdata = $wpdb->get_row( $result, ARRAY_A);
  if ( $rowdata <> NULL ) {
    $username = $rowdata['username'];
    $vfa_id = str_replace ( "VFA", "VFA #", $username );
    $m_email = $rowdata['m_email'];
    $m_status = $rowdata['m_status'];
    $m_firstname = $rowdata['m_first_name'];
    $m_middlename = $rowdata['m_middle_name'];
    $m_lastname = $rowdata['m_last_name'];
    $m_fullname = $m_firstname ." ". $m_middlename ." ". $m_lastname;
    $s_status = $rowdata['s_status'];
    $s_firstname = $rowdata['s_first_name'];
    $s_lastname = $rowdata['s_last_name'];
    $s_email = $rowdata['s_email'];
    $data = array( 'VFA'=>$vfa_id, 'first_name'=>$m_firstname, 'last_name'=>$m_lastname, 'm_email'=>$m_email, 'm_status'=>$m_status, 's_firstname'=>$s_firstname, 's_lastname'=>$s_lastname, 's_status'=>$s_status, 's_email'=>$s_email );
  } else {
      $data = null;
  }
  return $data;
}

/* -------------------------------------------------------------------------
Function to generate a password
Created 180807 by DAV 
---------------------------------------------------------------------------*/
function vfa_generate_password() {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
  return $password = substr( str_shuffle( $chars ), 0, 12 );
}

/* -------------------------------------------------------------------------
Function to register a VFA user in wp_users and wp_usermeta
Created 180807 by DAV 
Mod 180904 by DAV to add error tracking
Mod 181113 by DAV to modify flow if error at any step
---------------------------------------------------------------------------*/
function vfa_register_user( $username, $fname, $mname, $lname, $vfa, $rin, $email, $password ) {

  $error_message = '';
  
  // check if already a wp user, to determine how to proceed below
  $user_id = get_user_by( 'login', $username );
  if ( is_wp_error( $user_id ) ) {
    $error_message = $user_id->get_error_message();
    $error_message .= " 1 - ".$username;
    // echo $error_message;
  } else {
    // echo ", user_id1=".$user_id->ID."<br/>";
  }

  // try adding to wp_users. If already a user update info, otherwise add user
  $m_display_name = $fname." ".$lname;
  if ( $user_id ) { // already a user, so include user_id to update user
    // echo "username2=".$rowdata['username'];
    $data = array(
      'ID' => $user_id->ID,
      'user_login' => $username, 
      'user_pass' => md5($password), 
      'user_nicename' => $fname.$lname, 
      'user_email' => $email,
      'display_name' => $m_display_name
    );
  } else { // not a user, so do user insert
    // echo " first=".$fname;
    $data = array(
      'user_login' => $username, 
      'user_pass' => $password, 
      'user_nicename' => $fname.$lname, 
      'user_email' => $email,
      'display_name' => $m_display_name
    );
    }
  $user_id = wp_insert_user( $data );

  if ( is_wp_error( $user_id ) ) {
    $error_message = $user_id->get_error_message();
    $error_message .= " 2 - ".$username;
    // echo $error_message;
  
  } else {
    // echo "user_id2=".$user_id."<br/>";

    // and update user meta fields
    update_usermeta( $user_id, 'user_vfa', $vfa );
    update_usermeta( $user_id, 'user_rin', $rin );
    update_usermeta( $user_id, 'first_name', $fname );
    update_usermeta( $user_id, 'middle_name', $mname );
    update_usermeta( $user_id, 'last_name', $lname );
    update_usermeta( $user_id, 'user_pwd', $password );
    update_usermeta( $user_id, 'show_admin_bar_front', "false" );
  }
  return $error_message;
}

function adm_signup_sent( $fname, $lname, $username, $email, $password, $display_name, $vfa ) {
  // used to update vfa_members to show sign up was sent and to send email to Member
  global $wpdb;
  global $genealogist_email, $webmaster_email, $president_email, $president_password, $president_from_name, $smtp_host, $smtp_auth, $smtp_from_name, $smtp_secure, $smtp_port;
  
  $message = '';

  // update Member's data
  $table = 'vfa_members';
  $data = array('signup_sent' => 1, 'user_pwd' => $password );
  $where = array( 'username' => $username );
  $update = $wpdb->update($table, $data, $where);

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
  $mail->isHTML(true);
  $mail->From = $president_email;
  $mail->FromName = $president_from_name;
  $mail->addAddress($email, $fname." ".$lname);
  $mail->addCC($webmaster_email);
  // $mail->addCC($genealogist_email);
  $mail->Subject = "Your Violette Registry and Family Association membership";
  $body = "<i>Hello, ".$fname." ".$lname.".<br/><br/>";
  $body .= "We have recently changed the Violette Registry and Family Association web site to allow Members to update their own Member Profile</i><br/><br/>";
  $body .= "You should go to our web site and update your Member Profile by editing your contact info and preferences.<br/><br/>";
  $body .= "Your login credentials are:<br/>";
  $body .= "<ul><li>Username: ".$username."</li>";
  $body .= "<li>Password: ".$password."</li></ul>";
  $body .= "Use this link to login: <a href='http://VioletteRegistry.com/login'>VioletteRegistry.com/login</a><br/><br/>";
  $body .= "Once there, select <b>Membership/Member Profile Editing</b> from the menu. You can use this at any time to update your Member Profile.<br/><br/>";
  $body .= "Be sure to keep your email address up-to-date so you can receive news and other items!<br/><br/>";
  $body .= "Use <b>Genealogy/FamilyTree</b> to see what family tree info we have for you. Contact our Genealogist for any questions or updates to your family tree. You can use <a href='mailto:rviolette@att.net?subject=".$m_display_name.", ".$username."'>this link</a> to reach Rod by email or look for his contact info in the footer of any page at our web site.<br/><br/>";
  $body .= "Contact me if you have any membership questions. You can use <a href='mailto:President@VioletteFamily.org?subject=".$m_display_name.", ".$username."'>this link</a> to reach me by email or look for my contact info in the footer of any page at our web site<br/><br/>";
  $body .= "David A. Violette, VFA #621, President/Webmaster";
  $mail->Body = $body;
  $mail->AltBody = $body;
  if(!$mail->send()) {
    $message .= "Mailer Error: " . $mail->ErrorInfo;
  } else {
    $message = "Message to member has been sent successfully";
  }

  return $message;
}
/* -------------------------------------------------------------------------
vfa_birthdays() sends birthday greetings to members
Created 180831 by DAV 
---------------------------------------------------------------------------*/
// add_action( 'vfa-birthdays', 'vfa_birthdays' );
// function vfa_birthdays() {
// 	global $wpdb, $tableheader, $rowstart, $rowend, $tableender;
// 	global $president_email, $webmaster_email, $webmaster_password, $webmaster_from_name;
// 	global $noreply_email, $noreply_password, $noreply_from_name;
// 	global $smtp_host, $smtp_password, $smtp_secure, $smtp_port, $smtp_auth;

// 	$subject = "Happy Birthday";
// 	$message = "Happy Birthday from the Violette Family Association! We hope you have a happy day.";
// 	include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 
//   $mail = new PHPMailer;
//   $mail->SMTPDebug = 0;                               
//   $mail->isSMTP();            
//   $mail->Host = $smtp_host;
//   $mail->SMTPAuth = $smtp_auth;                          
//   $mail->Username = $webmaster_email;                 
//   $mail->Password = $webmaster_password;                           
//   $mail->SMTPSecure = $smtp_secure;                           
//   $mail->Port = $smtp_port;                                   
//   $mail->From = $president_email;
// 	$mail->FromName = $smtp_from_name;
// 	$mail->addCC($webmaster_email);
// 	$mail->Subject = $subject;
// 	$mail->Body = $message;
// 	$mail->AltBody = $message;

//   date_default_timezone_set('America/Phoenix');
//   $timezone = date_default_timezone_get();
//   $this_date = getdate(date("U"));
//   $this_year = $this_date[year];
// 	$this_month = substr("00".$this_date[mon],-2, 2);
//   $this_day = substr("00".$this_date[mday],-2, 2);
//   $this_time = $this_date[hours];
//   $send_date = $this_year."-".$this_month."-".$this_day;
//   // $send_date = '1939-08-10';
//   echo $send_date."<br/>";

// 	$query = "SELECT vfa_members.username, vfa_members.tng_personID, vfa_members.m_first_name AS first_name, vfa_members.m_last_name AS last_name, vfa_members.m_email AS email  
// 	FROM vfa_members INNER JOIN tng_people ON vfa_members.tng_personID = tng_people.personID 
// 	WHERE tng_people.birthdatetr = '".$send_date."'";
//   echo $query."<br/>";
//   $rowdata = $wpdb->get_results( $query );
// 	foreach ( $rowdata as $member ) {
// 		$rp_user = $member->vfa_user;
//     $email = $member->email;
//     echo $email."<br/>";

// 		// now send
// 		if ( $email ) {
// 			$mail->addAddress($member->email, $member->first_name." ".$member->last_name);
//       if(!$mail->send()) {
//         $outcome = "Mailer Error: " . $mail->ErrorInfo;
//       } else {
//         $outcome = "Message has been sent successfully";
//       }
//       $mail->clearAddresses();
// 		} 
// 		// if ( $sms_contact ) {
// 		// 	$mail->addAddress($sms_contact, $member->first_name." ".$member->last_name);
//     //   if(!$mail->send()) {
//     //     $outcome = "Mailer Error: " . $mail->ErrorInfo;
//     //   } else {
//     //     $outcome = "Message has been sent successfully";
//     //   }
// 		// 	$mail->clearAddresses();
// 		// }
//   }
//   return $outcome;
// }  //vfa_birthdays
/* -------------------------------------------------------------------------
Function to write a query to CSV output
From http://php.net/manual/en/function.fputcsv.php#104980, Note by jamie at agendeisgn dot co dot uk
   // Using the function
    $sql = "SELECT * FROM table";
    // $db_conn should be a valid db handle

    // output as an attachment
    query_to_csv($db_conn, $sql, "test.csv", true);

    // output to file system
    query_to_csv($db_conn, $sql, "test.csv", false);
Created and modified 180712 by DAV 
---------------------------------------------------------------------------*/
function query_to_csv($query, $filename, $attachment = false, $headers = true) {
  
  global $host, $database, $user, $password;
  $cxn = new mysqli($host, $user, $password, $database);
  if ($cxn->connect_error) {
    die ('Error : ('. $cxn->connect_errno .') '. $cxn->connect_error);
  }
  
  if($attachment) {
      // send response headers to the browser
      header( 'Content-Type: text/csv' );
      header( 'Content-Disposition: attachment;filename='.$filename);
      $fp = fopen('php://output', 'w');
  } else {
      $fp = fopen($filename, 'w');
  }
  
  $result = mysqli_query($cxn, $query) or die( mysqli_error( $cxn ) );

  if($headers) {
      // output header row (if at least one row exists)
      $row = mysqli_fetch_assoc($result);
      if($row) {
          fputcsv($fp, array_keys($row));
          // reset pointer back to beginning
          mysqli_data_seek($result, 0);
      }
  }
  
  while($row = mysqli_fetch_assoc($result)) {
      fputcsv($fp, $row);
  }
  
  fclose($fp);
}
/* -------------------------------------------------------------------------
vfa_family_list() get parents, siblings, and children of missing members
Created 180831 by DAV 
---------------------------------------------------------------------------*/
function vfa_family_list( $personVFA ) {
	global $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $president_email, $webmaster_email, $webmaster_password, $webmaster_from_name;
	global $noreply_email, $noreply_password, $noreply_from_name;
	global $smtp_host, $smtp_password, $smtp_secure, $smtp_port, $smtp_auth;
  
  $outcome = "";

  // get personID for VFA#
  $query = "SELECT tng_personID, m_display_name, street_addr_1, street_addr_2, po_address, city, state_prov, postal_code, phone_home, m_cellphone, m_email FROM vfa_members 
  WHERE m_vfa = %d";
	$result = $wpdb->prepare( $query, $personVFA );
  $rowdata = $wpdb->get_row( $result, ARRAY_A);
  if ( $rowdata ) {
    $personID = $rowdata['tng_personID'];
    $name = $rowdata['m_display_name'];

    // set up email
    $subject = "Missing member info";
    $message = "<p>The Violette Family Association has lost contact with member ".$name." and hope you can help us. If you can provide current contact info, please fill in the contact data you have and reply to this email. You may also forward this to the person in question so they can provide their contact info themselves.</p>";
    $message .= "";
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

    $tableheader = "<table width='400px' border='1'><tbody>";
    $message = "<p>The Violette Family Association has lost contact with the member below and hope you can help us. If you can provide current contact info, please fill in the contact data you have and reply to this email. You may also forward this to the person in question so they can provide their contact info themselves.</p>";
    $message .= $tableheader;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "Name";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $name;
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "Person ID";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $personID;
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "Street address 1";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['street_addr_1'];
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "Street address 2";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['street_addr_2'];
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "PO address";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['po_address'];
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "City";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['city'];
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "State/Province";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['state_prov'];
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "Postal code";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['postal_code'];
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "Phone";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['phone_home'];
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "Cell Phone";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['m_cellphone'];
        $message .= "</td>";
      $message .= $rowend;
      $message .= $rowstart;
        $message .= "<td width='30%'>";
          $message .= "Email";
        $message .= "</td>";
        $message .= "<td width='70%'>";
          $message .= $rowdata['m_email'];
        $message .= "</td>";
      $message .= $rowend;
    $message .= $tableender;
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = $message;
    echo $message;

    // get person's family
    $family_query = "SELECT FAMC FROM tng_people WHERE personID='".$personID."'";
    $famc_parents = $wpdb->get_results( $family_query );
    foreach( $famc_parents as $family) {
      $outcome = "Family: ".$family->FAMC."<br/>";
      // get person's parents
      $parent_query = "SELECT husband, wife FROM tng_families WHERE familyID='".$family->FAMC."'";
      $parents = $wpdb->get_row( $parent_query );
      $fatherID = $parents->husband;
      $motherID = $parents->wife;
      // we don't know if husband or wife is the VFA member so try both
      $data = vfa_person_data( $fatherID ); // check if husband is member of VFA
      if ( $data ) { // he is
        $outcome .= "Parents: ".$fatherID.", ". $data['first_name'].", ".$data['last_name'].", ".$data['VFA'].", ".$data['m_status'].", ".$data['m_email'].$data['s_firstname'].", ".$data['s_lastname'].", ".$data['s_status'].", ".$data['s_vfa'].", ".$data['s_email'];
        if ( $data['m_status'] == "A" AND $data['m_email'] <> "") {
          $mail->addAddress( $data['m_email'], $data['first_name']." ".$data['last_name'] );
          // if(!$mail->send()) {
          //   $outcome .= "Mailer Error for ".$data['first_name'].$mail->ErrorInfo;
          // } else {
            $outcome .= "  Message sent to ".$data['first_name'];
          // }
          $mail->clearAddresses();
        }
        if ( $data['s_status'] == "A" AND $data['s_email'] <> "" AND $data['s_vfa'] == "") { // spouse is NOT a member so OK to send from here
          $mail->addAddress( $data['s_email'], $data['s_firstname']." ".$data['s_lastname'] );
          // if(!$mail->send()) {
          //   $outcome .= "Mailer Error for ".$data['s_firstname'].$mail->ErrorInfo;
          // } else {
            $outcome .= "  Message sent to ".$data['s_firstname'];
          // }
          // $mail->clearAddresses();
        }
        $outcome .= "<br/>";
      }
      $data = vfa_person_data( $motherID ); // check if wife is member of VFA
      if ( $data ) { // she is, so send from here
        $outcome .= "Parents: ".$fatherID.", ". $data['first_name'].", ".$data['last_name'].", ".$data['VFA'].", ".$data['m_status'].", ".$data['m_email'].$data['s_firstname'].", ".$data['s_lastname'].", ".$data['s_status'].", ".$data['s_vfa'].", ".$data['s_email'];
        if ( $data['m_status'] == "A" AND $data['m_email'] <> "") {
          $mail->addAddress( $data['m_email'], $data['first_name']." ".$data['last_name'] );
          // if(!$mail->send()) {
          //   $outcome .= "Mailer Error for ".$data['first_name'].$mail->ErrorInfo;
          // } else {
            $outcome .= "  Message sent to ".$data['first_name'];
          // }
          // $mail->clearAddresses();
        }
        if ( $data['s_status'] == "A" AND $data['s_email'] <> "" AND $data['s_vfa'] == "") { // spouse is NOT a member so OK to send from here
          $mail->addAddress( $data['s_email'], $data['s_firstname']." ".$data['s_lastname'] );
          // if(!$mail->send()) {
          //   $outcome .= "Mailer Error for ".$data['s_firstname'].$mail->ErrorInfo;
          // } else {
            $outcome .= "  Message sent to ".$data['s_firstname'];
          // }
          // $mail->clearAddresses();
        }
        $outcome .= "<br/>";
      }

      // get person's siblings
      $sibling_query = "SELECT personID, FAMC, lastname, firstname FROM tng_people WHERE FAMC='".$family->FAMC."'";
      $siblings = $wpdb->get_results( $sibling_query );
      // get sibling's children
      foreach ( $siblings as $sibling ) {
        $outcome .= "--Sibling: ".$sibling->personID.", ". $sibling->firstname."; ";
        $person = $sibling->personID;
        $data = vfa_person_data( $person );
        $outcome .= $data['first_name'].", ".$data['last_name'].", ".$data['VFA'].", ".$data['m_status'].", ".$data['m_email'];
        if ( $data['m_status'] == "A" AND $data['m_email'] <> "") {
          $mail->addAddress( $data['m_email'], $data['first_name']." ".$data['last_name'] );
          // if(!$mail->send()) {
          //   $outcome .= "Mailer Error for ".$data['first_name'].$mail->ErrorInfo;
          // } else {
            $outcome .= "  Message sent to ".$data['first_name'];
          // }
          // $mail->clearAddresses();
        } 
        $outcome .= "<br/>";
        $outcome .= "---Spouse/Partner: ".$data['s_firstname'].", ".$data['s_lastname'].", ".$data['s_status'].", ".$data['s_email'];
        if ( $data['s_status'] == "A" AND $data['s_email'] <> "") {
          $mail->addAddress( $data['s_email'], $data['s_firstname']." ".$data['s_lastname'] );
          // if(!$mail->send()) {
          //   $outcome .= "Mailer Error for ".$data['s_firstname'].$mail->ErrorInfo;
          // } else {
            $outcome .= "  Message sent to ".$data['s_firstname'];
          // }
          // $mail->clearAddresses();
        }
        $outcome .= "<br/>";
        $sibfam_query = "SELECT familyID FROM tng_families WHERE husband='".$sibling->personID."' OR wife='".$sibling->personID."'";
        $sibfams = $wpdb->get_results($sibfam_query);
        foreach ( $sibfams as $sibfam ) {
          $outcome .= "----Family: ".$sibfam->familyID."<br/>";
          $child_query = "SELECT personID, lastname, firstname FROM tng_people WHERE FAMC='".$sibfam->familyID."'";
          $children = $wpdb->get_results($child_query);
          foreach ( $children as $child ) {
            $outcome .= "------Child: ".$child->personID.", ". $child->firstname."; ";
            $person = $child->personID;
            $data = vfa_person_data( $person );
            $outcome .= $data['first_name'].", ".$data['last_name'].", ".$data['VFA'].", ".$data['m_status'].", ".$data['m_email'];
            if ( $data['m_status'] == "A" AND $data['m_email'] <> "") {
              $mail->addAddress( $data['m_email'], $data['first_name']." ".$data['last_name'] );
              // if(!$mail->send()) {
              //   $outcome .= "Mailer Error for ".$data['first_name'].$mail->ErrorInfo;
              // } else {
                $outcome .= "  Message sent to ".$data['first_name'];
              // }
              // $mail->clearAddresses();
            } 
            $outcome .= "<br/>";
          } // child
        } // sibfam
      } // $sibling
    } // $family
  } else {
    $outcome = "personID not found for VFA ".$personVFA;
  } // member found or not
echo $outcome;
  return $outcome;
}

/* -------------------------------------------------------------------------
Input validation functions used in this module
Created 180506 ff by DAV 
---------------------------------------------------------------------------*/
	
// RIN Validation
function val_rin( $data, $type ) {
  $rin = test_input($data);
  if (empty($name)) { return ""; }
  if ( !empty($rin) && (is_nan($rin) || $rin < 1 || $rin > 90000) ) {
    $error_message .= $type." RIN should be a number lower than 90000<br/>";
  }
  return $error_message;
}
// Name Validation
function val_name( $data, $type ) {
  $name = test_input($data);
  if (empty($name)) { return ""; }
  // if ( !preg_match( "/^[a-zA-ZÀ-ÿ' -]+$/", $name ) ) {
  //   $error_message .= "Only letters, hyphen, apostrophe, and white space allowed in ".$type." name.<br/>";
  if ( !preg_match( "/^[a-zA-Z -]+$/", $name ) ) {
    $error_message .= "Only letters, hyphen, and white space allowed in ".$type." name.<br/>";
  } else { 
    $error_message = "";
  }
  return $error_message;
}
// Place Validation
function val_place( $data, $type ) {
  $name = test_input($data);
  if (empty($name)) { return ""; }
  if ( !preg_match( "/^[a-zA-ZÀ-ÿ', -]+$/", $name ) ) {
    $error_message .= "Only letters, hyphen, apostrophe, comma, and white space allowed in ".$type." name.<br/>";
  } else { 
    $error_message = "";
  }
  return $error_message;
}
// Street address Validation
function val_street( $data, $type ) {
  $name = test_input($data);
  if (empty($name)) { return ""; }
  if ( !preg_match( "/^[0-9a-zA-ZÀ-ÿ' -]+$/", $name ) ) {
    $error_message .= "Only numbers, letters, hyphen, apostrophe, and white space allowed in ".$type." name.<br/>";
  } else { 
    $error_message = "";
  }
  return $error_message;
}
// PO address Validation
function val_pobox( $data, $type ) {
  $name = test_input($data);
  if (empty($name)) { return ""; }
  if ( !preg_match( "/^[0-9a-zA-Z -]+$/", $name ) ) {
    $error_message .= "Only numbers, letters, hyphen, and white space allowed in ".$type.".<br/>";
  } else { 
    $error_message = "";
  }
  return $error_message;
}
// City Validation
function val_city( $data, $type ) {
  $name = test_input($data);
  if (empty($name)) { return ""; }
  if ( !preg_match( "/^[a-zA-ZÀ-ÿ' -]+$/", $name ) ) {
    $error_message .= "Only letters, hyphen, apostrophe, and white space allowed in ".$type." name.<br/>";
  } else { 
    $error_message = "";
  }
  return $error_message;
}
// State/Province Validation
function val_state( $data, $type ) {
  $name = test_input($data);
  if (empty($name)) { return ""; }
  if (!preg_match("/^(?:A[BKLRZ]|BC|C[AOT]|D[CE]|FL|GA|HI|I[ADLN]|K[SY]|LA|M[ABDEINOST]|N[BCDEHJLMSTUVY]|O[HKNR]|P[AE]|QC|RI|S[CDK]|T[NX]|UT|V[AT]|W[AIVY]|YT)*$/", $name)) {
    $error_message .= "Not a valid US state or CA province abbreviation.<br/>"; 
  }
  return $error_message;
}
// Country Validation
function val_country( $data, $type ) {
  $name = test_input($data);
  if (empty($name)) { return ""; }
  if (!preg_match("/^USA|CAN*$/",$name)) {
    $error_message .= "Please enter USA or CAN for country.<br/>"; 
  }
  return $error_message;
}
// Postal code Validation
function val_postalcode( $data, $country ) {
  $code = test_input($data);
  if (empty($code)) { return ""; }
  if (empty($country)) { 
    $error_message .= "Please enter a country code.<br/>"; 
    return $error_message;
  }
  if ($country == "USA") {
    if (!preg_match("/^([0-9]{5}(?:-[0-9]{4})?)*$/",$code)) {
      $error_message .= "Please enter a valid USA postal code.<br/>"; 
    }
  } 
  if ($country == "CAN") {
    if (!preg_match("/^([ABCEGHJKLMNPRSTVXY][0-9][A-Z] [0-9][A-Z][0-9])*$/",$code)) {
      $error_message .= "Please enter a valid Canadian postal code.<br/>"; 
    }
  }
  return $error_message;
}
// Email Validation
function val_email( $data, $type ) {
  $data = test_input($data);
  if (!empty($data)) {
    if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
      $error_message .= $data." is not a valid email address<br/>";
    } else {
      $error_message .= "";
    }
  }
  return $error_message;
}
// Phone Validation
function val_phone( $data, $type ) {
  $data = test_input($data);
  if (!preg_match("/^((([0-9]{1})*[- .(]*([0-9]{3})[- .)]*[0-9]{3}[- .]*[0-9]{4})+)*$/", $data)) {
    $error_message .= "Not a valid ".$type." phone number in format (999) 999-9999 or 999-999-9999.<br/>"; 
  }
  return $error_message;
}

?>