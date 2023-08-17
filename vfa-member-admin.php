<?php
/*
Plugin Name: vfa-member-admin
Plugin URI: 
Description: Provides for member profile data management for the Violette Family Association. Membership is by member/spouse.
Version: 1.0.1
Author: David A. Violette (VFA #621)
Author URI: 
License: GPLv2 or later
Text Domain: vfa-member-admin
*/

/* -------------------------------------------------------------------------
Global variables for all functions in this file
Created 180209 by DAV 
---------------------------------------------------------------------------*/
// Global variables
require_once( ABSPATH . '/wp-includes/pluggable.php' );

// require_once(ABSPATH .'/wp-load.php');
include_once("functions.php");
// include_once("vfa-utils.php");

$current_user = wp_get_current_user();
// session_start();

$user = "dmj7jvgp_wrdp";
$password = "Ek[=xEL-@xQx";
$database = "dmj7jvgp_wrdp";
$password = "Ek[=xEL-@xQx";
$tableheader = "<table class='datatable'><tbody>";
$rowstart = "<tr>";
$rowend = "</tr>";
$tableender = "</tbody></table>";

/* -------------------------------------------------------------------------
personal_data() displays the names of member and spouse
created 180209 by DAV 
Mod 180812 by DAV to chg RIN to Person ID
---------------------------------------------------------------------------*/
function personal_data() {

	global $current_user, $username, $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $administrator, $editor, $author, $contributor;
		 
	// test whether member or admin
	if( current_user_can( $editor ) ) {
		$username = $_SESSION['mbr'];  // an admin has selected this member
	} else {
		$username = $current_user->user_login;  // it's a member coming in
	}

	// get member's name data
	echo "<h1>Displaying member profile personal info for ". vfa_getmemberID($username) ."</h1>";
	echo "<i><p style='color: #0000FF;'>Blue data is read-only and cannot be edited by the Member</p></i>";

	$query = "SELECT m_vfa, m_rin, m_rita, m_gender, m_status, m_classif, m_title, m_first_name, m_middle_name, 
		m_last_name, m_maiden_name, m_suffix, m_nickname, m_usenickname, s_vfa, s_rin, s_rita, s_gender, s_status, 
		s_title, s_first_name, s_middle_name, s_last_name, s_maiden_name, s_suffix, s_nickname, s_usenickname 
		FROM vfa_members 
		WHERE username = %s";
	$result = $wpdb->prepare( $query, $username );
	$rowdata = $wpdb->get_row( $result, ARRAY_A );

	echo $tableheader;
		echo $rowstart;
			echo "<td width='30%' class='datahead'>Data</td>";
			echo "<td width='35%' class='datahead'>Member</td>";
			echo "<td width='35%' class='datahead'>Spouse/Partner</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>VFA #</td>";
			echo "<td style='color: #0000FF;'>". $rowdata['m_vfa'] ."</td>";
			echo "<td style='color: #0000FF;'>". $rowdata['s_vfa'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Person ID</td>";
			echo "<td style='color: #0000FF;'>". $rowdata['m_rin'] ."</td>";
			echo "<td style='color: #0000FF;'>". $rowdata['s_rin'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Status</td>";
			echo "<td style='color: #0000FF;'>". status_name( $rowdata['m_status'] ) ."</td>";
			echo "<td style='color: #0000FF;'>". status_name( $rowdata['s_status'] ) ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Classification</td>";
			echo "<td style='color: #0000FF;'>". classif_name( $rowdata['m_classif'] ) ."</td>";
			echo "<td>-</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Rita ref</td>";
			echo "<td>". $rowdata['m_rita'] ."</td>";
			echo "<td>". $rowdata['s_rita'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Title</td>";
			echo "<td>". $rowdata['m_title'] ."</td>";
			echo "<td>". $rowdata['s_title'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>First name</td>";
			echo "<td>". $rowdata['m_first_name'] ."</td>";
			echo "<td>". $rowdata['s_first_name'] ."</td>";
		echo $rowend;
			echo "<td class='datalabel'>Middle name</td>";
			echo "<td>". $rowdata['m_middle_name'] ."</td>";
			echo "<td>". $rowdata['s_middle_name'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Last name</td>";
			echo "<td>". $rowdata['m_last_name'] ."</td>";
			echo "<td>". $rowdata['s_last_name'] ."</td>";
		echo $rowend;
			echo "<td class='datalabel'>Maiden name</td>";
			echo "<td>". $rowdata['m_maiden_name'] ."</td>";
			echo "<td>". $rowdata['s_maiden_name'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Suffix</td>";
			echo "<td>". $rowdata['m_suffix'] ."</td>";
			echo "<td>". $rowdata['s_suffix'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Nickname</td>";
			echo "<td>". $rowdata['m_nickname'] ."</td>";
			echo "<td>". $rowdata['s_nickname'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Use nickname?</td>";
			if($rowdata['m_usenickname'] == 1) {
				$usenick = "Yes";
			}
			else {
				$usenick = "";
			}
			echo "<td>". $usenick ."</td>";
			if($rowdata['s_usenickname'] == 1) {
				$usenick = "Yes";
			}
			else {
				$usenick = "";
			}
			echo "<td>". $usenick ."</td>";
		echo $rowend;
	echo $tableender;
}
add_shortcode('vfa-personal', 'personal_data');

/* -------------------------------------------------------------------------
personal_edit() allows editing personal data of member and spouse
Created 180216 by DAV 
Modified 180304 by DAV to add recursive
Modified 180711 by DAV to add display_names
Modified 180812 by DAV to change RIN to Person ID
Mod 180824 by DAV to fix Deceased
---------------------------------------------------------------------------*/
function personal_edit() {

	global $current_user, $username,	$wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $administrator, $editor, $author, $contributor;
		
	// test whether member or admin
	if( current_user_can($editor) ) {
		$username = $_SESSION['mbr'];  // an admin has selected this member
	} else {
		$username = $current_user->user_login;  // it's a member coming in
	}
	
	error_log("in personal edit");
	
	// is this a postback? If so, update database
	if (isset($_POST['update1'])) {
		$m_rin =  $_POST['m_rin'];
		$m_rin = filter_var($m_rin, FILTER_SANITIZE_NUMBER_INT );  // get rid of any prefix added
		$m_status =  $_POST['m_status'];
		$m_classif =  $_POST['m_classif'];
		$m_rita =  $_POST['m_rita'];
		$m_title =  $_POST['m_title'];
		$m_gender =  $_POST['m_gender'];
		$m_first_name = $_POST['m_first_name'];
		$m_middle_name = $_POST['m_middle_name'];
		$m_last_name = $_POST['m_last_name'];
		$m_maiden_name = $_POST['m_maiden_name'];
		$m_suffix =  $_POST['m_suffix'];
		$m_nickname = $_POST['m_nickname'];
		$m_usenickname = $_POST['m_usenickname'];
		$m_display_name = $m_first_name." ".$m_last_name;
		if ( !empty($m_rin) ) {
			$tng_personID = "P".$m_rin; // chg 180810
		}
		$s_rin =  $_POST['s_rin'];
		$s_rin = filter_var($s_rin, FILTER_SANITIZE_NUMBER_INT );  // get rid of any prefix added
		$s_status =  $_POST['s_status'];
		$s_rita =  $_POST['s_rita'];
		$s_title =  $_POST['s_title'];
		$s_gender =  $_POST['s_gender'];
		$s_first_name = $_POST['s_first_name'];
		$s_middle_name = $_POST['s_middle_name'];
		$s_last_name = $_POST['s_last_name'];
		$s_maiden_name = $_POST['s_maiden_name'];
		$s_suffix =  $_POST['s_suffix'];
		$s_nickname = $_POST['s_nickname'];
		$s_usenickname = $_POST['s_usenickname'];
		$s_display_name = $s_first_name." ".$s_last_name;
		$table = 'vfa_members';
		if( current_user_can($editor) ) {
			$data = array('m_rin' => $m_rin, 'm_status' => $m_status, 'm_classif' => $m_classif, 
			'm_rita' => $m_rita, 'm_title' => $m_title, 'm_gender' => $m_gender, 'm_first_name' => $m_first_name, 'm_middle_name' => $m_middle_name, 
			'm_last_name' => $m_last_name, 'm_maiden_name' => $m_maiden_name, 'm_suffix' => $m_suffix, 'm_nickname' => $m_nickname, 
			'm_usenickname' => $m_usenickname, 'm_display_name' => $m_display_name, 'tng_personID' => $tng_personID, 
			's_rin' => $s_rin, 's_status' => $s_status, 's_rita' => $s_rita, 
			's_title' => $s_title, 's_gender' => $s_gender, 's_first_name' => $s_first_name, 's_middle_name' => $s_middle_name, 
			's_last_name' => $s_last_name, 's_maiden_name' => $s_maiden_name, 's_suffix' => $s_suffix, 's_nickname' => $s_nickname, 
			's_usenickname' => $s_usenickname, 's_display_name' => $s_display_name );
		} else {
			$data = array('m_rita' => $m_rita, 'm_title' => $m_title, 'm_first_name' => $m_first_name, 'm_middle_name' => $m_middle_name, 
			'm_last_name' => $m_last_name, 'm_maiden_name' => $m_maiden_name, 'm_suffix' => $m_suffix, 'm_nickname' => $m_nickname, 
			'm_usenickname' => $m_usenickname, 'm_display_name' => $m_display_name, 
			's_rita' => $s_rita, 
			's_title' => $s_title, 's_first_name' => $s_first_name, 's_middle_name' => $s_middle_name, 
			's_last_name' => $s_last_name, 's_maiden_name' => $s_maiden_name, 's_suffix' => $s_suffix, 's_nickname' => $s_nickname, 
			's_usenickname' => $s_usenickname, 's_display_name' => $s_display_name );
		}
		$where = array( 'username' => $username );
		$result = $wpdb->update($table, $data, $where);
		unset($_POST['update1']);  // clear variable
		personal_edit();  // return to form

	} else {

	// declare and clear variables
		$m_vfa = $m_rin = $m_rita = $m_gender = $m_status = $m_classif = $m_title = $m_gender = $m_first_name = $m_middle_name = $m_last_name = "";
		$m_maiden_name = $m_suffix = $m_nickname = $m_usenickname = $m_display_name = "";
		$s_vfa = $s_rin = $s_rita = $s_gender = $s_status = $s_title = $s_gender = $s_first_name = $s_middle_name = $s_last_name = "";
		$s_maiden_name = $s_suffix = $s_nickname = $s_usenickname = $s_display_name = "";

		// get member's name data
		// echo "admin= " .is_admin() ."<br/>";
		// echo "current user login= " .$user->user_login ."<br/>";
		// echo "current user= " .$current_user->user_login ."<br/>";
		// echo "username= " .$username ."<br/>";
		// echo "mbr= " .$_SESSION['mbr'] ."<br/>";

		echo "<h1>Displaying member profile personal info for ". vfa_getmemberID($username) ."</h1>";
		echo "<i><p style='color: #0000FF;'>Blue data is read-only and cannot be edited by the Member</p></i>";
		// echo "path is '" . __DIR__ . "'.<br/>";
		// echo "path and file name is '" . __FILE__ . "'.<br/>";
		// echo "plugin dir path is '". plugin_dir_path( __DIR__ ) . "'.<br/>";
		// echo "plugin dir is '". plugin_dir( __DIR__ ) . "'.<br/>";

		$query = "SELECT m_vfa, m_rin, m_rita, m_gender, m_status, m_classif, m_title, m_gender, m_first_name, m_middle_name, m_last_name, m_maiden_name, 
			m_suffix, m_nickname, m_usenickname, s_vfa, s_rin, s_rita, s_gender, s_status, s_title, s_gender, s_first_name, s_middle_name, s_last_name, 
			s_maiden_name, s_suffix, s_nickname, s_usenickname 
			FROM vfa_members 
			WHERE username = %s";
			$result = $wpdb->prepare( $query, $username );
			$rowdata = $wpdb->get_row( $result, ARRAY_A );

		echo "<form method='POST'>";
			echo $tableheader;
				echo $rowstart;
					echo "<td width='30%' class='datahead'>Data</td>";
					echo "<td width='35%' class='datahead'>Member</td>";
					echo "<td width='35%' class='datahead'>Spouse/Partner</td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>VFA #</td>";
					echo "<td style='color: #0000FF;'>". $rowdata['m_vfa'] ."</td>";
					echo "<td style='color: #0000FF;'>". $rowdata['s_vfa'] ."</td>";
				echo $rowend;
				// test whether member or admin
				if( current_user_can($editor) ) {
					// echo $username;
					echo $rowstart;
						echo "<td class='datalabel'>Person ID</td>";
						echo "<td><input id='m_rin' type='text' name='m_rin' value='". $rowdata['m_rin'] ."'</input></td>";
						echo "<td><input id='s_rin' type='text' name='s_rin' value='". $rowdata['s_rin'] ."'</input></td>";
					echo $rowend;
					echo $rowstart;
						echo "<td class='datalabel'>Status</td>";
						echo "<td><select id='m_status' name='m_status'>";
							echo "<option value='A' ";
								if($rowdata['m_status'] == 'A') echo "selected='SELECTED'";
							echo " >Active</option>";
							echo "<option value='M' ";
								if($rowdata['m_status'] == 'M') echo "selected='SELECTED'";
							echo " >Missing</option>";
							echo "<option value='D' ";
								if($rowdata['m_status'] == 'D') echo "selected='SELECTED'";
							echo " >Deceased</option>";
							echo "<option value='N' ";
								if($rowdata['m_status'] == 'N') echo "selected='SELECTED'";
							echo " >New</option>";
							echo "<option value='U' ";
								if($rowdata['m_status'] == 'U') echo "selected='SELECTED'";
							echo " >Unknown</option>";
						echo "</select></td>";
						echo "<td><select id='s_status' name='s_status'>";
							echo "<option value='' ";
								if($rowdata['s_status'] == '') echo "selected='SELECTED'";
							echo " >-</option>";
							echo "<option value='A' ";
								if($rowdata['s_status'] == 'A') echo "selected='SELECTED'";
							echo " >Active</option>";
							echo "<option value='M' ";
								if($rowdata['s_status'] == 'M') echo "selected='SELECTED'";
							echo " >Missing</option>";
							echo "<option value='D' ";
								if($rowdata['s_status'] == 'D') echo "selected='SELECTED'";
							echo " >Associate</option>";
							echo "<option value='N' ";
								if($rowdata['s_status'] == 'N') echo "selected='SELECTED'";
							echo " >New</option>";
							echo "<option value='U' ";
								if($rowdata['s_status'] == 'U') echo "selected='SELECTED'";
							echo " >Unknown</option>";
						echo "</select></td>";
					echo $rowend;
					echo $rowstart;
						echo "<td class='datalabel'>Classification</td>";
						echo "<td><select id='m_classif' name='m_classif'>";
							echo "<option value='M' ";
								if($rowdata['m_classif'] == 'M') echo "selected='SELECTED'";
							echo " >Member</option>";
							echo "<option value='C' ";
								if($rowdata['m_classif'] == 'C') echo "selected='SELECTED'";
							echo " >Child</option>";
							echo "<option value='A' ";
								if($rowdata['m_classif'] == 'A') echo "selected='SELECTED'";
							echo " >Associate</option>";
						echo "</select></td>";
						echo "<td>-</td>";
					echo $rowend;
				} else {
					// echo $username;
					echo $rowstart;
					echo "<td class='datalabel'>Person ID</td>";
					echo "<td style='color: #0000FF;'>". $rowdata['m_rin'] ."</td>";
					echo "<td style='color: #0000FF;'>". $rowdata['s_rin'] ."</td>";
					echo $rowend;
					echo $rowstart;
						echo "<td class='datalabel'>Status</td>";
						echo "<td style='color: #0000FF;'>". status_name( $rowdata['m_status'] ) ."</td>";
						echo "<td style='color: #0000FF;'>". status_name( $rowdata['s_status'] ) ."</td>";
					echo $rowend;
					echo $rowstart;
						echo "<td class='datalabel'>Classification</td>";
						echo "<td style='color: #0000FF;'>". classif_name( $rowdata['m_classif'] ) ."</td>";
						echo "<td>-</td>";
					echo $rowend;
				}
				// end member/admin test
				echo $rowstart;
					echo "<td class='datalabel'>Rita ref</td>";
					echo "<td><input id='m_rita' type='text' name='m_rita' value='". $rowdata['m_rita'] ."'</input></td>";
					echo "<td><input id='s_rita' type='text' name='s_rita' value='". $rowdata['s_rita'] ."'</input></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Title</td>";
					echo "<td><input id='m_title' type='text' name='m_title' value='". $rowdata['m_title'] ."'</input></td>";
					echo "<td><input id='s_title' type='text' name='s_title' value='". $rowdata['s_title'] ."'</input></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Gender</td>";
					echo "<td><select id='m_gender' name='m_gender'>";
						echo "<option value='M' ";
							if($rowdata['m_gender'] == 'M') echo "selected='SELECTED'";
						echo " >Male</option>";
						echo "<option value='F' ";
							if($rowdata['m_gender'] == 'F') echo "selected='SELECTED'";
						echo " >Female</option>";
					echo "</select></td>";
					echo "<td><select id='s_gender' name='s_gender'>";
						echo "<option value='' ";
							if($rowdata['s_gender'] == '') echo "selected='SELECTED'";
						echo " >-</option>";
						echo "<option value='M' ";
							if($rowdata['s_gender'] == 'M') echo "selected='SELECTED'";
						echo " >Male</option>";
						echo "<option value='F' ";
							if($rowdata['s_gender'] == 'F') echo "selected='SELECTED'";
						echo " >Female</option>";
					echo "</select></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>First name</td>";
					echo "<td><input id='m_first_name' type='text' name='m_first_name' value='". $rowdata['m_first_name'] ."'</input></td>";
					echo "<td><input id='s_first_name' type='text' name='s_first_name' value='". $rowdata['s_first_name'] ."'</input></td>";
				echo $rowend;
					echo "<td class='datalabel'>Middle name</td>";
					echo "<td><input id='m_middle_name' type='text' name='m_middle_name' value='". $rowdata['m_middle_name'] ."'</input></td>";
					echo "<td><input id='s_middle_name' type='text' name='s_middle_name' value='". $rowdata['s_middle_name'] ."'</input></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Last name</td>";
					echo "<td><input id='m_last_name' type='text' name='m_last_name' value='". $rowdata['m_last_name'] ."'</input></td>";
					echo "<td><input id='s_last_name' type='text' name='s_last_name' value='". $rowdata['s_last_name'] ."'</input></td>";
				echo $rowend;
					echo "<td class='datalabel'>Maiden name</td>";
					echo "<td><input id='m_maiden_name' type='text' name='m_maiden_name' value='". $rowdata['m_maiden_name'] ."'</input></td>";
					echo "<td><input id='s_maiden_name' type='text' name='s_maiden_name' value='". $rowdata['s_maiden_name'] ."'</input></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Suffix</td>";
					echo "<td><input id='m_suffix' type='text' name='m_suffix' value='". $rowdata['m_suffix'] ."'</input></td>";
					echo "<td><input id='s_suffix' type='text' name='s_suffix' value='". $rowdata['s_suffix'] ."'</input></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Nickname</td>";
					echo "<td><input id='m_nickname' type='text' name='m_nickname' value='". $rowdata['m_nickname'] ."'</input></td>";
					echo "<td><input id='s_nickname' type='text' name='s_nickname' value='". $rowdata['s_nickname'] ."'</input></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Use nickname?</td>";
					if($rowdata['m_nickname'] == "") {
						$usenick1 = "";
						$usenick0 = "";
						}	else {
							if($rowdata['m_usenickname'] == 1) {
								$usenick1 = "checked";
							}	else {
								$usenick1 = "";
							}
							if($rowdata['m_usenickname'] == 0) {
								$usenick0 = "checked";
							}	else {
								$usenick0 = "";
							}
						}
						echo "<td>";
							echo "<input type='radio' name='m_usenickname' value='1' ". $usenick1 ."/>  Yes";
							echo "<input type='radio' name='m_usenickname' value='0' ". $usenick0 ."/>  No";
						echo "</td>";
					if($rowdata['s_nickname'] == "") {
						$usenick1 = "";
						$usenick0 = "";
						}	else {
							if($rowdata['s_usenickname'] == 1) {
								$usenick1 = "checked";
							}	else {
								$usenick1 = "";
							}
							if($rowdata['s_usenickname'] == 0) {
								$usenick0 = "checked";
							}	else {
								$usenick0 = "";
							}
						}
						echo "<td>";
							echo "<input type='radio' name='s_usenickname' value='1' ". $usenick1 ."/>  Yes";
							echo "<input type='radio' name='s_usenickname' value='0' ". $usenick0 ."/>  No";
					echo "</td>";
				echo $rowend;
				echo $rowstart;
					echo "<td colspan='3'>";
					echo "<input type='submit' name='update1' value='Submit changes'/>";
					echo "</td>";
				echo $rowend;
			echo $tableender;
		echo "</form>";
	}
}
add_shortcode('vfa-personal-edit', 'personal_edit');

/* -------------------------------------------------------------------------
contact_data() displays the contact info such as addresses, phone numbers, and emails of member and spouse
created 180210 by DAV 
---------------------------------------------------------------------------*/
function contact_data() {
	global $current_user, $username, $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $administrator, $editor, $author, $contributor;
			
	// test whether member or admin
	if( current_user_can($editor) ) {
		$username = $_SESSION['mbr'];  // an admin has selected this member
	} else {
		$username = $current_user->user_login;  // it's a member coming in
	}
	// get member's name data
		echo "<h1>Displaying member profile contact info for ". vfa_getmemberID($username) ."</h1>";

	$query = "SELECT m_email, s_email, street_addr_1, street_addr_2, po_address, city, state_prov, postal_code, country, 
		phone_home, m_cellphone, s_cellphone 
		FROM vfa_members 
		WHERE username = %s";
	$result = $wpdb->prepare( $query, $username );
	$rowdata = $wpdb->get_row( $result, ARRAY_A );

	echo $tableheader;
		echo $rowstart;
			echo "<td width='30%' class='datahead'>Data</td>";
			echo "<td width='35%' class='datahead'>Regular</td>";
			echo "<td width='35%' class='datahead'>Seasonal</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>Street address 1</td>";
			echo "<td>". $rowdata['street_addr_1'] ."</td>";
			echo "<td></td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>Street address 2</td>";
			echo "<td>". $rowdata['street_addr_2'] ."</td>";
			echo "<td></td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>PO address</td>";
			echo "<td>". $rowdata['po_address'] ."</td>";
			echo "<td></td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>City</td>";
			echo "<td>". $rowdata['city'] ."</td>";
			echo "<td></td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>State/Province</td>";
			echo "<td>". $rowdata['state_prov'] ."</td>";
			echo "<td></td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>Postal code</td>";
			$postal_code = format_postal_code($rowdata['postal_code']);
			echo "<td>". $postal_code ."</td>";
			echo "<td></td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>Country</td>";
			echo "<td>". $rowdata['country'] ."</td>";
			echo "<td></td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datahead'>Data</td>";
			echo "<td width='35%' class='datahead'>Member</td>";
			echo "<td width='35%' class='datahead'>Spouse/Partner</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Email</td>";
			echo "<td>". $rowdata['m_email'] ."</td>";
			echo "<td>". $rowdata['s_email'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>Home phone</td>";
			$phone = format_phone_number($rowdata['phone_home']);
			echo "<td>". $phone ."</td>";
			echo "<td></td>";
		echo $rowend;
		echo $rowstart;
			echo "<td width='30%' class='datalabel'>Cell phone</td>";
			$phone = format_phone_number($rowdata['m_cellphone']);
			echo "<td>". $phone ."</td>";
			$phone = format_phone_number($rowdata['s_cellphone']);
			echo "<td>". $phone ."</td>";
		echo $rowend;
	echo $tableender;
}
add_shortcode('vfa-contact', 'contact_data');

/* -------------------------------------------------------------------------
contact_edit() allows edit of the contact info such as addresses, phone numbers, and emails of member and spouse
Created 180216 by DAV 
Modified 180304 by DAV to add recursive
---------------------------------------------------------------------------*/
function contact_edit() {
	global $current_user, $username, $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $administrator, $editor, $author, $contributor;
	// declare and clear variables
	$m_email = $s_email = $street_addr_1 = $street_addr_2 = "";
	$po_address = $city = $state_prov = $postal_code = $country = $phone_home = $m_cellphone = $s_cellphone = "";
	
	// test whether member or admin
	if( current_user_can($editor) ) {
		$username = $_SESSION['mbr'];  // an admin has selected this member
	} else {
		$username = $current_user->user_login;  // it's a member coming in
	}

	// is this a postback? If so, update database
	if (isset($_POST['update2'])) {
		$m_email =  $_POST['m_email'];
		$street_addr_1 = $_POST['street_addr_1'];
		$street_addr_2 = $_POST['street_addr_2'];
		$po_address = $_POST['po_address'];
		$city = $_POST['city'];
		$state_prov =  $_POST['state_prov'];
		$postal_code = $_POST['postal_code'];
		$country = $_POST['country'];
		$phone_home = $_POST['phone_home'];
		$m_cellphone = $_POST['m_cellphone'];
		$s_cellphone = $_POST['s_cellphone'];
		if ( $m_email == '' ) $recv_news = 'P';
		$table = 'vfa_members';
		$data = array('m_email' => $m_email, 'street_addr_1' => $street_addr_1, 'street_addr_2' => $street_addr_2, 
			'po_address' => $po_address, 'city' => $city, 'state_prov' => $state_prov, 'postal_code' => $postal_code, 
			'country' => $country, 'phone_home' => $phone_home, 'm_cellphone' => $m_cellphone, 's_cellphone' => $s_cellphone,
		 	'recv_news' => $recv_news);
		$where = array( 'username' => $username );
		$result = $wpdb->update($table, $data, $where);
		unset($_POST['update2']);  // clear variable
		// contact_edit();  // return to form

	} 
	// else {

		// get member's name data
		echo "<h1>Displaying member profile contact info for ". vfa_getmemberID($username) ."</h1>";

		// get member's contact data
		$query = "SELECT m_email, street_addr_1, street_addr_2, po_address, city, state_prov, postal_code, country, 
			phone_home, m_cellphone, s_cellphone 
			FROM vfa_members 
			WHERE username = %s";
		$result = $wpdb->prepare( $query, $username );
		$rowdata = $wpdb->get_row( $result, ARRAY_A );
		// echo "username = " .$username;

		echo "<form method='POST'>";
			echo $tableheader;
				echo $rowstart;
					echo "<td width='30%' class='datahead'>Data</td>";
					echo "<td width='35%' class='datahead'>Regular</td>";
					echo "<td width='35%' class='datahead'>Seasonal</td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Email</td>";
					echo "<td><input id='m_email' type='text' name='m_email' value='". $rowdata['m_email'] ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Street address 1</td>";
					echo "<td><input id='street_addr_1' type='text' name='street_addr_1' value='". $rowdata['street_addr_1'] ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Street address 2</td>";
					echo "<td><input id='street_addr_2' type='text' name='street_addr_2' value='". $rowdata['street_addr_2'] ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>PO address</td>";
					echo "<td><input id='po_address' type='text' name='po_address' value='". $rowdata['po_address'] ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>City</td>";
					echo "<td><input id='city' type='text' name='city' value='". $rowdata['city'] ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>State/Province</td>";
					echo "<td><input id='state_prov' type='text' name='state_prov' value='". $rowdata['state_prov'] ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Postal code</td>";
					$postal_code = format_postal_code($rowdata['postal_code']);
					echo "<td><input id='postal_code' type='text' name='postal_code' value='". $postal_code ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Country</td>";
					echo "<td><input id='country' type='text' name='country' value='". $rowdata['country'] ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td width='30%' class='datahead'>Data</td>";
					echo "<td width='35%' class='datahead'>Member</td>";
					echo "<td width='35%' class='datahead'>Spouse/Partner</td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Home phone</td>";
					$phone = format_phone_number($rowdata['phone_home']);
					echo "<td><input id='phone_home' type='text' name='phone_home' value='". $phone ."'</input></td>";
					echo "<td></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Cell phone</td>";
					$phone = format_phone_number($rowdata['m_cellphone']);
					echo "<td><input id='m_cellphone' type='text' name='m_cellphone' value='". $phone ."'</input></td>";
					$phone = format_phone_number($rowdata['s_cellphone']);
					echo "<td><input id='s_cellphone' type='text' name='s_cellphone' value='". $phone ."'</input></td>";
				echo $rowend;
				echo $rowstart;
					echo "<td colspan='3'>";
					echo "<input type='submit' name='update2' value='Submit changes'/>";
					echo "</td>";
				echo $rowend;
			echo $tableender;
		echo "</form>";
	// }
}
add_shortcode('vfa-contact-edit', 'contact_edit');

/* -------------------------------------------------------------------------
events_edit() displays the events data of member and spouse (cannot be edited)
Note: same function used in both display and edit versions
Created 180215 by DAV 
---------------------------------------------------------------------------*/
function event_data() {
	global $current_user, $username, $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $administrator, $editor, $author, $contributor;
			
	// test whether member or admin
	if( current_user_can($editor) ) {
		$username = $_SESSION['mbr'];  // an admin has selected this member
	} else {
		$username = $current_user->user_login;  // it's a member coming in
	}

	// member's name info
	echo "<h1>Displaying member profile event date info for ". vfa_getmemberID($username) ."</h1>";
	echo "<i><p style='color: #0000FF;'>Blue data is read-only and cannot be edited by the Member</p></i>";

	// get member's genealogical database links
	$query = "SELECT tng_personID, tng_spouseID, tng_familyID 
		FROM vfa_members 
		WHERE username = %s";
	$result = $wpdb->prepare( $query, $username );
	$rowdata = $wpdb->get_row( $result, ARRAY_A );
	$this_personID = $rowdata['tng_personID'];
	$this_spouseID = $rowdata['tng_spouseID'];
	$this_familyID = $rowdata['tng_familyID'];
	
	// get event data for member
	$query = "SELECT birthdate, birthplace, deathdate, deathplace FROM tng_people 
		WHERE personID = %s";
	$result = $wpdb->prepare( $query, $this_personID );
	$rowdata_m = $wpdb->get_row( $result, ARRAY_A );
	// get event data for spouse/partner
	$query = "SELECT birthdate, birthplace, deathdate, deathplace FROM tng_people 
		WHERE personID = %s";
	$result = $wpdb->prepare( $query, $this_spouseID );
	$rowdata_s = $wpdb->get_row( $result, ARRAY_A );

	echo $tableheader;
		echo $rowstart;
			echo "<td width='30%' class='datahead'>Data</td>";
			echo "<td width='35%' class='datahead'>Member</td>";
			echo "<td width='35%' class='datahead'>Spouse/Partner</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Birth date</td>";
			echo "<td style='color: #0000FF;'>". $rowdata_m['birthdate'] ."</td>";
			echo "<td style='color: #0000FF;'>". $rowdata_s['birthdate'] ."</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Birth place</td>";
			echo "<td style='color: #0000FF;'>". $rowdata_m['birthplace'] ."</td>";
			echo "<td style='color: #0000FF;'>". $rowdata_s['birthplace'] ."</td>";
		echo $rowend;
		echo $rowstart;
			if($rowdata_m['deathdate'] == "") {
					$deathdate = "(n/a)";
				}
				else {
					$deathdate = $rowdata_m['deathdate'];
				}
			echo "<td class='datalabel'>Death date</td>";
			echo "<td style='color: #0000FF;'>". $deathdate ."</td>";
			if($rowdata_s['deathdate'] == "") {
					$deathdate = "(n/a)";
				}
				else {
					$deathdate = $rowdata_s['deathdate'];
				}
			echo "<td style='color: #0000FF;'>". $deathdate ."</td>";
		echo $rowend;
		echo $rowstart;
			if($rowdata_m['deathdate'] == "") {
					$deathplace = "(n/a)";
				}
				else {
					$deathplace = $rowdata_m['deathplace'];
				}
			echo "<td class='datalabel'>Death place</td>";
			echo "<td style='color: #0000FF;'>". $deathplace ."</td>";
			if($rowdata_s['deathdate'] == "") {
					$deathplace = "(n/a)";
				}
				else {
					$deathplace = $rowdata_s['deathplace'];
				}
			echo "<td style='color: #0000FF;'>". $deathplace ."</td>";
		echo $rowend;

		// get marriage nfo
		$query = "SELECT marrdate, marrplace FROM tng_families 
			WHERE familyID = %s";
		$result = $wpdb->prepare( $query, $this_familyID );
		$rowdata_f = $wpdb->get_row( $result, ARRAY_A );

		echo $rowstart;
			if($rowdata_f['marrdate'] == "") {
					$marrdate = "(n/a)";
				}
				else {
					$marrdate = $rowdata_f['marrdate'];
				}
			echo "<td class='datalabel'>Marriage date</td>";
			echo "<td colspan='2' align='center' style='color: #0000FF;'>". $marrdate ."</td>";
		echo $rowend;
		echo $rowstart;
			if($rowdata_f['marrdate'] == "") {
					$marrplace = "(n/a)";
				}
				else {
					$marrplace = $rowdata_f['marrplace'];
				}
			echo "<td class='datalabel'>Marriage place</td>";
			echo "<td colspan='2' align='center' style='color: #0000FF;'>". $marrplace ."</td>";
		echo $rowend;
	echo $tableender;
}
add_shortcode('vfa-events', 'event_data');

/* -------------------------------------------------------------------------
preferences() displays the mail receiving preferences of member and spouse
created 180211 by DAV 
---------------------------------------------------------------------------*/
function preferences() {
	global $current_user, $username, $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $administrator, $editor, $author, $contributor;
			
	// test whether member or admin
	if( current_user_can($editor) ) {
		$username = $_SESSION['mbr'];  // an admin has selected this member
	} else {
		$username = $current_user->user_login;  // it's a member coming in
	}

	// member's name info
	echo "<h1>Displaying member profile preferences info for ". vfa_getmemberID($username) ."</h1>";

	$query = "SELECT recv_news, m_enews, s_enews, m_freq, s_freq 
		FROM vfa_members 
		WHERE username = %s";
	$result = $wpdb->prepare( $query, $username );
	$rowdata = $wpdb->get_row( $result, ARRAY_A );

	echo $tableheader;
		echo $rowstart;
			echo "<td width='30%' class='datahead'>Data</td>";
			echo "<td width='35%' class='datahead'>Member</td>";
			echo "<td width='35%' class='datahead'>Spouse/Partner</td>";
		echo $rowend;
		echo $rowstart;
			echo "<td class='datalabel'>Receive news</td>";
			if($rowdata['recv_news'] == "E") {
					$recv = "Electronically";
				}
				elseif($rowdata['recv_news'] == "P") {
					$recv = "Print";
				}
				else {
					$recv = "(None)";
				}
			echo "<td>". $recv ."</td>";
			echo "<td>". "-" ."</td>";
		echo $rowend;
			if($rowdata['recv_news'] == "E") {
				echo $rowstart;
				echo "<td class='datalabel'>Frequency</td>";
				if($rowdata['m_freq'] == "I") {
						$recv = "When issued";
					}
					elseif($rowdata['m_freq'] == "W") {
						$recv = "Weekly";
					}
					elseif($rowdata['m_freq'] == "M") {
						$recv = "Monthly";
					}
					else {
						$recv = "(n/a)";
					}
				echo "<td>". $recv ."</td>";
				if($rowdata['s_freq'] == "I") {
						$recv = "When issued";
					}
					elseif($rowdata['s_freq'] == "W") {
						$recv = "Weekly";
					}
					elseif($rowdata['s_freq'] == "M") {
						$recv = "Monthly";
					}
					else {
						$recv = "(n/a)";
					}
				echo "<td>". $recv ."</td>";
			echo $rowend;
		}
	echo $tableender;
}
add_shortcode('vfa-preferences', 'preferences');

/* -------------------------------------------------------------------------
preferences_edit() edits the mail receiving preferences of member and spouse
Created 180215 by DAV 
Modified 180304 by DAV to add recursive
---------------------------------------------------------------------------*/
function preferences_edit() {
	global $current_user, $username, $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $administrator, $editor, $author, $contributor;
	
	// test whether member or admin
	if( current_user_can($editor) ) {
		$username = $_SESSION['mbr'];  // an admin has selected this member
	} else {
		$username = $current_user->user_login;  // it's a member coming in
	}

	if (isset($_POST['update4'])) {
		$recv_news =  $_POST['recv_news'];
		$m_enews = $_POST['m_enews'];
		$s_enews = $_POST['s_enews'];
		$m_freq = $_POST['m_freq'];
		$s_freq = $_POST['s_freq'];
		$table = 'vfa_members';
		$data = array('recv_news' => $recv_news, 'm_enews' => $m_enews, 's_enews' => $s_enews, 'm_freq' => $m_freq, 's_freq' => $s_freq );
		$where = array( 'username' => $username );
		$result = $wpdb->update($table, $data, $where);
		unset($_POST['update4']);  // clear variable

	} else {

		// get member's name data
		echo "<h1>Displaying member profile preferences info for ". vfa_getmemberID($username) ."</h1>";

		// get member's preferences data
		$query = "SELECT recv_news, m_enews, s_enews, m_freq, s_freq 
			FROM vfa_members 
			WHERE username = %s";
		$result = $wpdb->prepare( $query, $username );
		$rowdata = $wpdb->get_row( $result, ARRAY_A );

		echo "<form method='POST'>";
			echo $tableheader;
				echo $rowstart;
					echo "<td width='30%' class='datahead'>Data</td>";
					echo "<td width='35%' class='datahead'>Member</td>";
					echo "<td width='35%' class='datahead'>Spouse/Partner</td>";
				echo $rowend;
				echo $rowstart;
					echo "<td class='datalabel'>Receive news</td>";
					echo "<td colspan='2'>";
						if($rowdata['recv_news'] == "E") {
								echo "<input type='radio' name='recv_news' value='E' checked>Electronically      ";
							}
							elseif($rowdata['recv_news'] !== "E") {
								echo "<input type='radio' name='recv_news' value='E'>Electronically      ";
							}
						if($rowdata['recv_news'] == "P") {
								echo "<input type='radio' name='recv_news' value='P' checked>Print      ";
							}
							elseif($rowdata['recv_news'] !== "P") {
								echo "<input type='radio' name='recv_news' value='P'>Print      ";
							}
						if($rowdata['recv_news'] == "N") {
								echo "<input type='radio' name='recv_news' value='N' checked>Never";
							}
							elseif($rowdata['recv_news'] !== "N") {
								echo "<input type='radio' name='recv_news' value='N'>Never";
							}
					echo "</td>";
				echo $rowend;
				if($rowdata['recv_news'] == "E") {
					echo $rowstart;
						echo "<td class='datalabel'>Receive eNews?</td>";
						echo "<td>";
							if($rowdata['m_enews'] == "1") {
									echo "<input type='radio' name='m_enews' value='1' checked>Yes";
								}
								elseif($rowdata['m_enews'] !== "1") {
									echo "<input type='radio' name='m_enews' value='1'>Yes";
								}
							if($rowdata['m_enews'] == "0") {
									echo "<input type='radio' name='m_enews' value='0' checked>No";
								}
								elseif($rowdata['m_enews'] !== "0") {
									echo "<input type='radio' name='m_enews' value='0'>No";
								}
						echo "</td>";
						echo "<td>";
							if($rowdata['s_enews'] == "1") {
									echo "<input type='radio' name='s_enews' value='1' checked>Yes";
								}
								elseif($rowdata['s_enews'] !== "1") {
									echo "<input type='radio' name='s_enews' value='1'>Yes";
								}
							if($rowdata['s_enews'] == "0") {
									echo "<input type='radio' name='s_enews' value='0' checked>No";
								}
								elseif($rowdata['s_enews'] !== "0") {
									echo "<input type='radio' name='s_enews' value='0'>No";
								}
						echo "</td>";
					echo $rowend;
					echo $rowstart;
						echo "<td class='datalabel'>Frequency</td>";
						if($rowdata['recv_news'] !== "E") {
							$freqI = "";
							$freqW = "";
							$freqM = "";
							}	else {
								if($rowdata['m_freq'] == "I") {
									$freqI = "checked";
								}	else {
									$freqI = "";
								}
								if($rowdata['m_freq'] == "W") {
									$freqW = "checked";
								}	else {
									$freqW = "";
								}
								if($rowdata['m_freq'] == "M") {
									$freqM = "checked";
								}	else {
									$freqM = "";
								}
							}
						echo "<td>";
							echo "<input type='radio' name='m_freq' value='I' ". $freqI . ">When issued<br/>";
							echo "<input type='radio' name='m_freq' value='W' ". $freqW . ">Weekly<br/>";
							echo "<input type='radio' name='m_freq' value='M' ". $freqM . ">Monthly";
						echo "</td>";
						if($rowdata['recv_news'] !== "E") {
							$freqI = "";
							$freqW = "";
							$freqM = "";
							}	else {
								if($rowdata['s_freq'] == "I") {
									$freqI = "checked";
								}	else {
									$freqI = "";
								}
								if($rowdata['s_freq'] == "W") {
									$freqW = "checked";
								}	else {
									$freqW = "";
								}
								if($rowdata['s_freq'] == "M") {
									$freqM = "checked";
								}	else {
									$freqM = "";
								}
							}
						echo "<td>";
							echo "<input type='radio' name='s_freq' value='I' ". $freqI . ">When issued<br/>";
							echo "<input type='radio' name='s_freq' value='W' ". $freqW . ">Weekly<br/>";
							echo "<input type='radio' name='s_freq' value='M' ". $freqM . ">Monthly";
						echo "</td>";
					echo $rowend;
				}
					echo $rowstart;
						echo "<td colspan='3'>";
							echo "<input type='submit' name='update4' value='Submit changes'/>";
						echo "</td>";
					echo $rowend;
			echo $tableender;
		echo "</form>";
	}
}
add_shortcode('vfa-preferences-edit', 'preferences_edit');
?>
<?php
/* -------------------------------------------------------------------------
member_search() provides for searching for members by parameters by admins
Created 180317 by DAV 
Mod 180812 by DAV to chg RIN to Person ID
---------------------------------------------------------------------------*/
function vfa_adm_search() {
	?>
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script type='text/javascript'>
		// When the document loads do everything inside here ...
		jQuery(document).ready(function(){
			// first time through, select without parameters
			jQuery("#memberlist" ).load( "/wp-content/plugins/vfa-member-admin/member-search.php"); //load initial records
			
			// after, respond to parameter input
			// executes whem btnFirstName button clicked
			jQuery('#btnFirstName').click(function() {
				var val = document.getElementById('txtFirstName').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: "FirstName", txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
						alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // search

			// executes whem btnLastName button clicked
			jQuery('#btnLastName').click(function() {
				var val = document.getElementById('txtLastName').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: "LastName", txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            			alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // search

			// executes whem btnVFA button clicked
			jQuery('#btnVFA').click(function() {
				var val = document.getElementById('txtVFA').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: "VFA", txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
						alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // search
		
			// executes whem btnPID button clicked
			jQuery('#btnPID').click(function() {
				var val = document.getElementById('txtPID').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: "PID", txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
						alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // search

			// executes whem btnCity button clicked
			jQuery('#btnCity').click(function() {
				var val = document.getElementById('txtCity').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: "City", txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // search

			// executes whem btnState button clicked
			jQuery('#btnState').click(function() {
				var val = document.getElementById('txtState').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: "State", txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // search

			// executes whem btnEmail button clicked
			jQuery('#btnEmail').click(function() {
				var val = document.getElementById('txtEmail').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: "Email", txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // search

			// executes whem btnClear button clicked
			jQuery('#btnClear').click(function() {
				jQuery.ajax({
					type: "POST",
					data: {btn: "Clear"},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // clear

			// executes whem btnApplicants button clicked
			jQuery('#btnApplicants').click(function() {
				jQuery.ajax({
					type: "POST",
					data: {btn: "Applicants"},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // applicants

			// executes whem btnAdded button clicked
			jQuery('#btnAdded').click(function() {
				jQuery.ajax({
					type: "POST",
					data: {btn: "Added"},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // added

		})  // document ready
	</script>

<?php
	$returnhtml = "<div id='search'>
		<form action='' method='POST' id='membersearchform'>
			<h1>Violette Registry Member Administration</h1>
			<p>Start by entering search criteria than click the Search button to list the members.</p>
			<div id='search-criteria'>
				<fieldset id='fldSearch' class='edit' visible='true'>
					<legend style='font-size: 12px; background-color: inherit;'>Search Parameters</legend>
					<table>
						<tr>
							<td width='50%' style='text-align:right'>
								<label for='txtFirstName'>First name
								<input type='text' id='txtFirstName' size='35' autofocus />
								<input type='submit' id='btnFirstName' value='Search'/></label>
							</td>
							<td width='50%' style='text-align:right'>
								<label for='txtLastName'>Last name
								<input type='text' id='txtLastName' size='35' />
								<input type='submit' id='btnLastName' value='Search'/></label>
							</td>
						</tr>
						<tr>
							<td width='50%' style='text-align:right'>
								<label for='txtVFA'>VFA
								<input type='text' id='txtVFA' size='10' />
								<input type='submit' id='btnVFA' value='Search'/></label>
							</td>
							<td width='50%' style='text-align:right'>
								<label for='txtPID'>Person ID
								<input type='text' id='txtPID' size='10' />
								<input type='submit' id='btnPID' value='Search'/></label>
							</td>
						</tr>
						<tr>
							<td width='50%' style='text-align:right'>
								<label for='txtCity'>City
								<input type='text' id='txtCity' size='35' />
								<input type='submit' id='btnCity' value='Search'/></label>
							</td>
							<td width='50%' style='text-align:right'>
								<label for='txtState'>State/Prov
								<input type='text' id='txtState' size='5' />
								<input type='submit' id='btnState' value='Search'/></label>
							</td>
						</tr>
						<tr>
							<td width='50%' style='text-align:right'>
								<label for='txtEmail'>Email
								<input type='text' id='txtEmail' size='50' />
								<input type='submit' id='btnEmail' value='Search'/></label>
							</td>
							<td width='50%' style='text-align:right'>
								<input type='submit' id='btnApplicants' value='Show applicants'/>
								<input type='submit' id='btnAdded' value='Show added'/>
								<input type='submit' id='btnClear' value='Clear Results'/>
							</td>
						</tr>
						<tr>
							<td width='100%' colspan='2' align='center'>
								<p style='color: white; font-size: 1.2em; text-align: center;'>Click on the blue link under USER in the table below to edit the info for that member.</p>
							</td>
						</tr>
						<tr>
							<td width='100%' colspan='2' align='center'>
								<p style='font-size: 1.0em; text-align: center;'>CL = Classification. ST = Status. GN = Gender. ST = State.</p>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
		</form>
		<div id='memberlist'></div>
	</div>
	<div id='outcome1'></div>
	<div id='outcome2'></div>";
	return $returnhtml;
}  // vfa_adm_search
add_shortcode('vfa-adm-search', 'vfa_adm_search');
?>
<?php
/* -------------------------------------------------------------------------
vfa_adm_edit() allows editing personal data of member and spouse by admins
Created 180317 by DAV 
Mod 180812 by DAV to use vfa_register_users()
Mod 190616 by DAV to correct $mail->Password
Mod 210502 by DAV to make Genealogist and Membership generic
Mod 210502 by DAV to remove Update, Accept as Member, and Contact Member buttons
Mod 210515 by DAV to use roles instead of capabilities for determining $username to use
Mod 210522 by DAV to use local email client for acceptance
---------------------------------------------------------------------------*/
function vfa_adm_edit() {
	// global variables
	global $wpdb, $current_user;
  global $genealogist_email, $webmaster_email, $president_email, $smtp_host, $smtp_password, $smtp_from_name, $smtp_secure, $smtp_port;
	global $administrator, $editor, $author, $contributor;
	
	// define variables and set to empty values
	// $success_message = $vfa_num = $vfa_id = $m_status = $chars = $password = "";

	// echo "vfa_id = ".$vfa_id;
	// echo ", SESSION mbr =".$_SESSION['mbr'];
	// echo ", GET mbr =".$_GET{'mbr'};
	if ($vfa_id == "") {
		$_SESSION['mbr'] = $_GET{'mbr'}; // coming from member-search.php
	} else {
		$_SESSION['mbr'] = $vfa_id;  // applicant has been processed
	}

	$user_info = get_userdata(get_current_user_id());
	// echo ' Signed-in username: ' . $user_info->user_login . PHP_EOL;
	// echo ' Signed-in user roles: ' . implode(', ', $user_info->roles) . PHP_EOL;
	// echo ' Signed-in user ID: ' . $user_info->ID . PHP_EOL;

	// test whether member or admin
	$capability = implode(', ', $user_info->roles);
	if ( strpos ( $capability, "administrator") > 0 || strpos ( $capability, "membership") > 0 || strpos ( $capability, "board") > 0) {
	//	if( current_user_can( $administrator ) ) {
		$username = $_SESSION['mbr'];  // an admin has selected this member
		// echo " Actual member: ".$username;
	} else {
		$username = $current_user->user_login;  // it's a member coming in
		// echo " Admin member: ".$username;
	}

	// get applicant data
	$query = "SELECT m_rin, m_email, m_first_name, m_last_name FROM vfa_members WHERE username = %s";
	$result = $wpdb->prepare( $query, $_SESSION['mbr'] );
	$rowdata = $wpdb->get_row( $result, ARRAY_A );
	$m_rin = $rowdata['m_rin'];
	$email = $rowdata['m_email'];
	$fname = $rowdata['m_first_name'];
	$mname = $rowdata['m_middle_name'];
	$lname = $rowdata['m_last_name'];
	$infobody = "Dear ".$fname." ".$lname.": We need more info regarding your application to join the Violette Family Association.";
	$infobody .= " (Your temporary ID is ".$_SESSION['mbr'].".) ";

	if(!empty($_POST["btnAccept"])) {

		// get highest VFA number on file
		$query = "SELECT MAX(m_vfa) AS m_vfa FROM vfa_members WHERE applicant = 0";  
		$rowdata = $wpdb->get_row( $query, ARRAY_A );
		$vfa_num = $rowdata['m_vfa'];
		$vfa_num = $vfa_num + 1;  // increase by 1
		$vfa_id = "VFA".strval( $vfa_num ); // convert back to username format

		// generate Password
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
		$password = substr( str_shuffle( $chars ), 0, 8 );

		// update the member data file with new VFA# and status
		$m_display_name = $fname." ".$lname;
		$m_status = "A";
		$m_classif = "M";
		$applicant = 0;
		$table = 'vfa_members';
		$data = array('username' => $vfa_id, 'm_status' => $m_status, 'm_classif' => $m_classif, 'm_vfa' => $vfa_num, 'm_display_name' => $m_display_name, 'applicant' => $applicant );
		$where = array( 'm_rin' => $m_rin );  
		$result = $wpdb->update($table, $data, $where);
		// $_SESSION['mbr'] = $vfa_id;

		$error_message = vfa_register_user( $vfa_id, $fname, $mname, $lname, $vfa_num, $m_rin, $email, $password );		// add to wp_users
		if(!empty($error_message)) { 	?>
			<br/><div class="error-message"><?php if(isset($error_message)) echo $error_message; ?></div><br/>
			<?php 
				$ok_mail = FALSE;
			} else {
			$_SESSION['mbr'] = $vfa_id;  // applicant has been processed
				$ok_mail = TRUE;
			}

	}  // btnAccept

	if(!empty($_POST["btnDelete"])) {

		$table = 'vfa_members';
		$where = array('username' => $_SESSION['mbr'] );  
		$result = $wpdb->delete($table, $where, array ( '%s' ));

		unset($_POST['btnDelete']);  // clear variable

	}  // btnDelete

	$nonce = wp_create_nonce( 'memberedit' );

	// check provided RIN/PID and names against genealogy
	$error_message = $success_message = "";
	$ok_process = TRUE;
	$query = "SELECT firstname, lastname FROM tng_people WHERE personID = %s";
	$result = $wpdb->prepare( $query, "P".$m_rin );
	// echo " m_rin: " . $m_rin;
	$rowdata = $wpdb->get_row( $result, ARRAY_A );
	$num_rows = $wpdb->num_rows;
	if ( $num_rows > 0 ) {  //  found
		$success_message .= "That personID was found in the Family Tree. ";
		$fname_a = $rowdata['firstname']; 
		// tng_people carries both first and middle name in the firstname field,
		// so take whatever is to the left of a space
		if ( strpos($fname_a, " ") > 0 ) {
			$sub = explode( " ", $fname_a );
			$fname_a = $sub[0];
		}
		$lname_a = $rowdata['lastname'];
		// last names can be hyphenated, so take the first part only
		if ( strpos($lname_a, " ") > 0 ) {
			$sub = explode( " ", $lname_a );
			$lname_a = $sub[0];
		}
		if ( strpos($lname_a, "-") > 0 ) {
			$sub = explode( "-", $lname_a );
			$lname_a = $sub[0];
		}
		if ( strpos($lname, "-") > 0 ) {
			$sub = explode( "-", $lname );
			$lname = $sub[0];
		}
		// echo $m_rin." ^ ".$fname_a." & ".$lname_a;
		if ( strcmp ( $lname_a, $lname ) == 0 ) {
			$success_message .= "The last name matches. ";
		} else {
			$error_message .= "Last name does not match - ".$lname_a.". ";
			$ok_process = FALSE;
		}
		if ( strcmp ( $fname_a, $fname ) == 0 ) {
			$success_message .= "The first name matches. ";
		} else {
			$error_message .= "First name does not match - ".$fname_a.".";
			$ok_process = FALSE;
		}
	} else {  // not found
		$error_message .= "The personID provided was not found in the Family Tree. ";
		$ok_process = FALSE;
	}
	// if( substr($_SESSION['mbr'], 0, 3 ) == "APL" ) {  // we don't need these buttons once a member
	?>
		<form method="POST" id="frmMemberAccept">
			<div id='divButtons' style='padding-bottom:5px;'>
			<?php if(empty($_POST["btnAccept"]) AND $ok_process == TRUE AND substr($_SESSION['mbr'], 0, 3 ) == "APL" ) { ?>	
				<span style="padding-right:15px;"><input type="submit" name="btnAccept" value="Accept as Member"/></span>
				or
				<span style="padding-left:15px;padding-right:15px;">
					<a href="mailto:<?php echo $email; ?>?subject=More info requested&body=<?php echo $infobody; ?>">
						<input type="button" name="btnContact" value="Contact Applicant for More Info"/>
					</a>
				</span>
				or
				<span style="padding-left:15px;padding-right:15px;">
					<input type="submit" name="btnDelete" value="Delete Applicant"/>
				</span>
			<?php } ?>
			<?php if ( !empty($email) AND $ok_process == FALSE AND substr($_SESSION['mbr'], 0, 3 ) == "APL" ) { ?>	
				<span style='padding-left:15px;'>
				<a href="mailto:<?php echo $email; ?>?subject=More info requested&body=<?php echo $infobody; ?>">
						<input type="button" name="btnContact2" value="Contact Applicant for More Info"/>
					</a>
				</span>
			<?php } ?>
			<?php if ( $ok_mail == TRUE ) { ?>	
				<span style='padding-left:15px;'>
					<a href="mailto:<?php echo $email;?>?subject=Your Violette Registry and Family Association application
					&body=Hello, <?php echo $fname;?> <?php echo $lname;?>. %0D%0A
					You have been accepted in the Violette Registry and Family Association! Your member number is VFA # <?php echo $vfa_num;?>. %0D%0A%0D%0A
					You should now go to our web site (VioletteRegistry.com) and complete your Member Profile by filling in your contact info and preferences. %0D%0A%0D%0A
					Your login credentials are:%0D%0A Username: <?php echo $vfa_id;?>%0D%0A Password: <?php echo $password;?> %0D%0A%0D%0A
					Once there, select Membership/Member Profile Editing from the menu. You can use this at any time to update your Member Profile. %0D%0A
					Be sure to keep your email address up-to-date so you can receive news and other items! %0D%0A%0D%0A
					Use Genealogy/FamilyTree to see what family tree info we have for you. Contact our Genealogist, for any questions or updates to 
					your family tree. You can reach the Genealogist by email - look for the contact info in the footer of any page at our web site. %0D%0A%0D%0A
					Contact our Membership Secretary if you have any membership questions. Look for the contact info in the footer of any page at our web site.%0D%0A%0D%0A 
					Welcome to the Association!%0D%0A%0D%0A">
					<input type='button' name='btnMail' value='Send Acceptance Email'/>
					</a>
				</span>
			<?php }   // ok to email ?>
			</div>
		</form>
	<?php
	// echo $username;
	// }
	?>
	<?php if(!empty($success_message)) { ?>	
		<br/><div class="success-message"><?php if(isset($success_message)) echo $success_message; ?></div><br/>
	<?php } ?>
	<?php if(!empty($error_message)) { ?>	
		<br/><div class="error-message"><?php if(isset($error_message)) echo $error_message; ?></div><br/>
	<?php } ?>

	<!-- Ref: https://www.jquery-az.com/create-jquery-tabs-ui-plugin-demo-free-code/ -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

  <script>
		$(function() {
			$( "#divTabs" ).tabs();
		});
  </script>
	<div id="divOutcomeA"></div>
	<div id="divOutcomeB"></div>
	<div id="divOutcomeC"></div>
	<?php
		echo "<div id='divButtons' style='padding-bottom:5px;'>";
			// echo "<span style='padding-right:15px;'><input type='submit' name='btnUpdate' value='Update Data''/></span>";
			// echo "or";
			// echo "<span style='padding-left:15px;padding-right:15px;'><input type='submit' name='btnAccept' value='Accept as Member'/></span>";
			// if (!$a_email == "") {
			// 	echo "<span style='padding-left:15px;'>or</span>
			// 	<span style='padding-left:15px;'>
			// 		<a href='mailto:".$a_email."?subject=More info requested&body=".$person_body."'>
			// 			<input type='button' name='btnPerson' value='Contact Person'/>
			// 		</a>
			// 	</span>";
			// }
		echo "</div>";
		?>

	<div id="divTabs">
		<ul>
			<li><a href="#personal">Personal Info</a></li>
			<li><a href="#contact">Contact Info</a></li>
			<li><a href="#events">Events</a></li>
			<li><a href="#preferences">Preferences</a></li>
		</ul>
		<div id="personal">
			<?php
				echo do_shortcode('[vfa-personal-edit]');
			?>
		</div>  
		<div id="contact">
			<?php
				echo do_shortcode('[vfa-contact-edit]');
			?>
		</div>  
		<div id="events">
			<?php
				echo do_shortcode('[vfa-events]');
			?>
		</div>
		<div id="preferences">
			<?php
				echo do_shortcode('[vfa-preferences-edit]');
			?>
		</div>  
	</div>

<?php }  //vfa_adm_edit
add_shortcode('vfa-adm-edit', 'vfa_adm_edit');
?>
<?php
/* -------------------------------------------------------------------------
vfa_adm_added() allows editing personal data from added list by admins
Created 180427 by DAV 
Mod 180812 by DAV to use vfa_register_user()
Mod 180904 by DAV, see below
Mod 181009 by DAV, to fix mailto buttons
Mod 190616 by DAV, to correct $mail->Password
Mod 210512 by DAV, many improvements and fixes
Mod 210527 by DAV, fix unnecessary P when doing search of tng_people and cleanup name handling
Mod 210606 by DAV, improve functions
---------------------------------------------------------------------------*/
function vfa_adm_added() {
	// global variables
	global $wpdb, $current_user, $username, $tableheader, $rowstart, $rowend, $tableender;
	global $president_email, $genealogist_email, $webmaster_email, $smtp_from_name;
	global $smtp_host, $smtp_password, $smtp_secure, $smtp_port;

	$_SESSION['mbr'] = $_GET{'mbr'}; // coming from member-search.php
	$username = $_SESSION['mbr'];
	if ( empty($_POST['btnAccept']) ) { $table_show = TRUE; }
	$ok_mail = "";

	// is this a postback? If so, update database
	if (!empty($_POST['btnUpdate'])) {
		// get updated data
		// echo "addr1b= ".$_POST['txtAddr1']."<br/>";
		$error_message = $success_message = "";
	
		// First Name Validation
		$a_fname = test_input($_POST["txtFirstName"]);
		$error_message .= val_name( $a_fname, "first" );

		// Middle Name Validation
		$a_mname = test_input($_POST["txtMiddleName"]);
		$error_message .= val_name( $a_mname, "middle" );
	
		// Last Name Validation
		$a_lname = test_input($_POST["txtLastName"]);
		$error_message .= val_name( $a_lname, "last" );

		$a_gender = $_POST["radGender"];
	
		// Place of Birth Validation
		$a_pob = test_input($_POST["txtPOB"]);
		$error_message .= val_place( $a_pob, "birthplace" );
		
		// Email Validation
		$a_email = test_input($_POST["txtEmail"]);
		$error_message .= val_email( $a_email, "" );
		
		// home phone validation
		$a_home_phone = $_POST["txtHomePhone"];
		$error_message .= val_phone( $a_home_phone, "home" );
		
		// cell phone validation
		$a_cell_phone = $_POST["txtCellPhone"];
		$error_message .= val_phone( $a_cell_phone, "cell" );
		
		// street address 1 Validation
		$a_addr1 = test_input($_POST["txtAddr1"]);
		$error_message .= val_street( $a_addr1, "street address 1" );
		
		// street address 2 Validation
		$a_addr2 = test_input($_POST["txtAddr2"]);
		$error_message .= val_street( $a_addr2, "street address 2" );
		
		// po address Validation
		$a_po_addr = test_input($_POST["txtPOAddr"]);
		$error_message .= val_pobox( $a_po_addr, "PO box" );
		
		// city Validation
		$a_city = test_input($_POST["txtCity"]);
		$error_message .= val_pobox( $a_city, "city" );
	
		// state province Validation
		$a_state = test_input($_POST["txtState"]);
		$error_message .= val_state( $a_state, "" );
		
		// country Validation
		$a_country = $_POST["txtCountry"];
		$error_message .= val_country( $a_country, "" );
		
		// postal code Validation
		$a_postal = $_POST["txtCode"];
		$error_message .= val_postalcode( $a_postal, $a_country );

		if (empty( $error_message ) ) {
			$a_rin = $_POST['txtRIN'];
			$rin = filter_var($a_rin, FILTER_SANITIZE_NUMBER_INT );  // get rid of any prefix added
			$tng_personID = "P".$a_rin; // chg 180810
			$a_gender = $_POST['radGender'];
			$a_email = $_POST['txtEmail'];
			$a_fname = $_POST['txtFirstName'];
			$a_mname = $_POST['txtMiddleName'];
			$a_lname = $_POST['txtLastName'];
			$a_dob = $_POST['datDOB'];
			$a_pob = $_POST['txtPOB'];
			$a_addr1 = $_POST['txtAddr1'];
			$a_addr2 = $_POST['txtAddr2'];
			$a_po_addr = $_POST['txtPOAddr'];
			$a_city = $_POST['txtCity'];
			$a_state = $_POST['txtState'];
			$a_postal = $_POST['txtPostal'];
			$a_country = $_POST['txtCountry'];
			$a_home_phone = $_POST['txtHomePhone'];
			$a_cell_phone = $_POST['txtCellPhone'];
			$a_display_name = $a_fname." ".$a_lname;
			$table = 'vfa_added';
			$data = array('a_rin' => $a_rin, 'tng_personID' => $tng_personID, 'a_first_name' => $a_fname, 'a_middle_name' => $a_mname, 'a_last_name' => $a_lname, 'a_gender' => $a_gender, 'a_email' => $a_email,
				'a_display_name' => $a_display_name, 'a_dob' => $a_dob, 'a_pob' => $a_pob, 'street_addr_1' => $a_addr1, 'street_addr_2' => $a_addr2, 
				'po_addr' => $a_po_addr, 'city' => $a_city, 'state_prov' => $a_state, 'postal_code' => $a_postal, 
				'country' => $a_country, 'home_phone' => $a_home_phone, 'cell_phone' => $a_cell_phone
			);
			$where = array( 'username' => $username );
			$result = $wpdb->update($table, $data, $where);
			unset($_POST['btnUpdate']);  // clear variable
		}
	} // btnUpdate

	// admin has clicked on btnAccept, so store the added
	if(!empty($_POST["btnAccept"])) {

		$error_message = "";
		$success_message = "";
	
		// get highest VFA number on file
		$query = "SELECT MAX(m_vfa) AS m_vfa FROM vfa_members WHERE applicant = 0";  
		$rowdata = $wpdb->get_row( $query, ARRAY_A );
		$vfa_num = $rowdata['m_vfa'];
		$vfa_num = $vfa_num + 1;  // increase by 1
		$vfa_id = "VFA".strval( $vfa_num ); // convert back to username format

		// generate Password
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
		$password = substr( str_shuffle( $chars ), 0, 8 );

		// try adding to wp_users
		// echo "first= ".$_POST['txtFirstName']."<br/>";
		$a_fname = $_POST['txtFirstName'];
		$a_mname = $_POST['txtMiddleName'];
		$a_lname = $_POST['txtLastName'];
		$a_email = $_POST['txtEmail'];
		$a_rin = $_POST['txtRIN']; // added DAV 180904
		$m_display_name = $a_fname." ".$a_lname;
		$error_message = vfa_register_user( $vfa_id, $a_fname, $a_mname, $a_lname, $vfa_num, $a_rin, $a_email, $password );		// mod for $vfa_id DAV 180904

		if ( $error_message ) {
			// $error_message = $user_id->get_error_message();
			$error_message .= " a - ".$vfa_id;
			$ok_mail = FALSE;
		} else {
			$success_message = "Member created: ".$user_id;
			$success_message .= " - ".$vfa_id."<br/>";
			$ok_mail = TRUE;
	
			// insert the member data in vfa_members
			$m_status = "A";
			$m_classif = "M";
			$applicant = 0;
			if ( $a_rin == "" | $a_rin == 0 ) {
				$tng_personID = "";
			} else {
				$tng_personID = "P".$a_rin; // chg 180810
			}
			$recv_news = "E";
			$m_enews = 1;
			$m_freq = "I";
			$today = date("Y-m-d");
			$table = 'vfa_members';
			$data = array('username' => $vfa_id, 'm_first_name' => $_POST['txtFirstName'], 'm_middle_name' => $_POST['txtMiddleName'], 'm_last_name' => $_POST['txtLastName'], 
				'm_vfa' => $vfa_num, 'm_rin' => $_POST['txtRIN'], 
				'm_email' => $_POST['txtEmail'], 'applicant' => 0, 'm_gender' => $_POST['radGender'], 
				'street_addr_1' => $_POST['txtAddr1'], 'street_addr_2' => $_POST['txtAddr2'], 'po_address' => $_POST['txtPOAddr'], 
				'city' => $_POST['txtCity'], 'state_prov' => $_POST['txtState'], 'postal_code' => $_POST['txtPostal'], 
				'country' => $_POST['txtCountry'], 'phone_home' => $_POST['txtHomePhone'], 'm_cellphone' => $_POST['txtCellPhone'],
				'm_status' => $m_status, 'm_classif' => $m_classif, 'tng_personID' => $tng_personID, 'm_display_name' => $m_display_name, 
				'recv_news' => $recv_news, 'm_enews' => $m_enews, 'm_freq' => $m_freq, 
				'signup_sent' => 1, 'date_joined' => $today
			);
			$format = array( '%s', '%s', '%s', '%s',
				'%d', '%d',
				'%s', '%d', '%s', 
				'%s', '%s', '%s', 
				'%s', '%s', '%s',
				'%s', '%s', '%s',
				'%s', '%s', '%s', '%s', 
				'%s', '%d', '%s',
				'%d', '%s' 
			);
			$result = $wpdb->insert( $table, $data, $format );
			if ( $result !== 1 ) {
			// if ( is_wp_error( $result ) ) {
					// $error_message .= $result->get_error_message();
				$error_message .= $wpdb->last_error;
				$error_message .= " b - ".$vfa_id;
				$ok_mail = FALSE;
			} else {
				$success_message .= "Member added: ".$vfa_id."<br/>";
				$ok_mail = TRUE;

				// remove from vfa_added
				$table = 'vfa_added';
				$data = array( 'username' => $_SESSION['mbr'] );
				$format = array( '%s' );
				$result = $wpdb->delete( $table, $data, $format );
			
				$_SESSION['mbr'] = $vfa_id; 	// preserve new username
			}	// add to vfa_members
		} // add to wp_users
		unset($_POST['btnAccept']);  // clear variable
		if ( $error_message == "" ) { $table_show = FALSE; }
	} // btnAccept

	// admin has clicked on btnDelete, so delete the added
	if(!empty($_POST["btnDelete"])) {

		$error_message = "";
		$success_message = "";
		// remove from vfa_added
		$table = 'vfa_added';
		$data = array( 'username' => $_SESSION['mbr'] );
		$format = array( '%s' );
		$result = $wpdb->delete( $table, $data, $format );
		// echo $_SESSION['mbr']." in btnDelete<br/>";
		if ( $result !== 1 ) {
			// if ( is_wp_error( $result ) ) {
			// $error_message .= $result->get_error_message();
			$error_message .= $wpdb->last_error;
			$error_message .= " - ".$_SESSION['mbr'];
		} else {
			$success_message .= "User deleted: ".$_SESSION['mbr']."<br/>";
			$is_deleted = TRUE;
		} 
		unset($_POST['btnDelete']);  // clear variable
		if ( $error_message == "" ) { $table_show = FALSE; }
	} // btnDelete

	if(empty($ok_mail) AND $is_deleted == "") { // first time thru or error

		// get added member data
		$query = "SELECT * FROM vfa_added WHERE username = '".$username."'";
		$a_rowdata = $wpdb->get_row( $query, ARRAY_A );
		$a_rin = "P".$a_rowdata['a_rin'];
		$a_gender = $a_rowdata['a_gender'];
		$a_email = $a_rowdata['a_email'];
		$a_fname = $a_rowdata['a_first_name'];
		$a_mname = $a_rowdata['a_middle_name'];
		$a_lname = $a_rowdata['a_last_name'];
		$a_dob = $a_rowdata['a_dob'];
		$a_pob = $a_rowdata['a_pob'];
		$a_addr1 = $a_rowdata['street_addr_1'];
		$a_addr2 = $a_rowdata['street_addr_2'];
		$a_po_addr = $a_rowdata['po_addr'];
		$a_city = $a_rowdata['city'];
		$a_state = $a_rowdata['state_prov'];
		$a_postal = $a_rowdata['postal_code'];
		$a_country = $a_rowdata['country'];
		$a_home_phone = $a_rowdata['home_phone'];
		$a_cell_phone = $a_rowdata['cell_phone'];
		$add_date = $a_rowdata['add_date'];
		$referrer = $a_rowdata['referrer'];
		$a_display_name = $a_fname." ".$a_lname;
		// echo $a_rin." ^ ".$a_fname." & ".$a_mname." & ".$a_lname." from vfa_added<br/>";

		// check provided RIN/PID and names against genealogy
		$error_message = $success_message = "";
		$ok_update = TRUE;
		$query = "SELECT firstname, lastname FROM tng_people WHERE personID = %s";
		$result = $wpdb->prepare( $query, $a_rin );
		// $result = $wpdb->prepare( $query, "P".$m_rin );
		$rowdata = $wpdb->get_row( $result, ARRAY_A );
		$num_rows = $wpdb->num_rows;
		if ( $num_rows > 0 ) {  //  found
			error_log("Logging test message");
			$success_message .= "That personID was found in the Family Tree. ";
			$fname_a = $rowdata['firstname']; 
			// tng_people carries both first and middle name in the firstname field,
			// so take whatever is to the left of a space
			if ( strpos($fname_a, " ") > 0 ) {
				$sub = explode( " ", $fname_a );
				$fname_a = $sub[0];
			}
			$lname_a = $rowdata['lastname'];
			// last names can be hyphenated, so take the first part only
			if ( strpos($lname_a, " ") > 0 ) {
				$sub = explode( " ", $lname_a );
				$lname_a = $sub[0];
			}
			if ( strpos($lname_a, "-") > 0 ) {
				$sub = explode( "-", $lname_a );
				$lname_a = $sub[0];
			}
			if ( strpos($lname, "-") > 0 ) {
				$sub = explode( "-", $lname );
				$lname = $sub[0];
			}
			// echo " * ".$a_rin." ^ ".$fname_a." & ".$lname_a." from tng_people<br/>";
			if ( strcmp ( $lname_a, $a_lname ) == 0 ) {
				$success_message .= "The last name matches. ";
				// $ok_mail = TRUE;
			} else {
				$error_message .= "Last name does not match - ".$lname_a.". ";
				$ok_update = FALSE;
				$ok_mail = FALSE;
			}
			if ( strcmp ( $fname_a, $a_fname ) == 0 ) {
				$success_message .= "The first name matches. ";
				// $ok_mail = TRUE;
			} else {
				$error_message .= "First name does not match - ".$fname_a.".";
				$ok_update = FALSE;
				$ok_mail = FALSE;
			}
		} else {  // not found
			$error_message .= "The personID provided was not found in the Family Tree. ";
			$ok_update = FALSE;
			$ok_mail = FALSE;
		}

		// get referrer member data if needed for contacting
		$query = "SELECT username, m_vfa, m_rin, m_display_name, m_email, phone_home, m_cellphone, m_first_name, m_middle_name, m_last_name, m_maiden_name FROM vfa_members WHERE username = '".$referrer."'";
		$rowdata = $wpdb->get_row( $query, ARRAY_A );
		$r_user = $rowdata['username'];
		$r_vfa = $rowdata['m_vfa'];
		$r_rin = $rowdata['m_rin'];
		$r_name = $rowdata['m_display_name'];
		$r_email = $rowdata['m_email'];
		$r_phone = $rowdata['phone_home'];
		$r_cell = $rowdata['m_cellphone'];
		$r_fname = $rowdata['m_first_name'];
		$r_mname = $rowdata['m_middle_name'];
		$r_lname = $rowdata['m_last_name'];
		$r_iname = $rowdata['m_maiden_name'];

		// get admin info
		$ad_user = $current_user->user_login;
		$ad_display_name = $current_user->display_name;
		$ad_email = $current_user->user_email;
		if ($referrer == $ad_user) {
			$r_email = $ad_email;
			$r_name = $ad_display_name;
		}
		$referrer_body = "Dear ".$r_name.": We need more info regarding your suggestion of ".$a_fname. " ".$a_mname." ".$a_lname." to add their info to the Violette Registry and to join the Violette Family Association.";
		$referrer_body .= " (Their temporary ID is ".$_SESSION['mbr'].")";
		$person_body = "Dear ".$a_fname. " ".$a_mname." ".$a_lname.":  ".$r_name." gave us your information and suggested you should be added to the Violette Family Tree. We need more info before adding you in the Violette Registry and enrolling you in the Violette Family Association.";
		$person_body .= " (Your temporary ID is ".$_SESSION['mbr'].")";
		
		$returnhtml = "<p>Make any changes needed to the Person's info then click the <b>Accept as Member</b> button to add them to the VFA member database. They will not
		be added to the Family Tree until the Genealogist does so manually.</p>
		<p>If there are errors displayed you will not be able to Update or Accept as Member!!.</p>
		<p>If you have any questions you may ask the Person or the Referrer by clicking the <b>Contact Person</b> or <b>Contact Referrer</b> button. This will 
		bring up your email client with some data filled in. You can add your question(s) or comments to the email body. Those buttons will appear only 
		if we have an email address for the contact. Otherwise use the phone number or address, if available.</p>";	
	} else { // get info for newly-accepted member
		$vfa_id = $_SESSION['mbr'];
		$query = "SELECT * FROM vfa_members WHERE username = '".$vfa_id."'";
		$rowdata = $wpdb->get_row( $query, ARRAY_A );
		$rin = "P".$rowdata['rin'];
		$email = $rowdata['m_email'];
		$fname = $rowdata['m_first_name'];
		$mname = $rowdata['m_middle_name'];
		$lname = $rowdata['m_last_name'];
		$display_name = $fname." ".$lname;
		$show_mail = TRUE;
		// echo $vfa_id." ^ ".$fname." & ".$mname." & ".$lname." from vfa_members<br/>";
	}

	if ( $table_show == TRUE ) {
		$returnhtml .= "<form method='POST'>";
		$returnhtml .= "<div id='divButtons' style='padding-bottom:5px;'>";
		if ( empty($_POST["btnAccept"]) AND $ok_update == TRUE ) {
			$returnhtml .= "<span style='padding-right:15px;'><input type='submit' name='btnUpdate' value='Update Data''/></span>";
			$returnhtml .= "or";
			$returnhtml .= "<span style='padding-left:15px;padding-right:15px;'><input type='submit' name='btnAccept' value='Accept as Member'/></span>";
			$returnhtml .= "or";
			$returnhtml .= "<span style='padding-left:15px;'><input type='submit' name='btnDelete' value='Delete'/></span>";
		}
		if ( !$a_email == "" AND $ok_update == FALSE ) {
				$returnhtml .= "<span style='padding-left:15px;'>or</span>
				<span style='padding-left:15px;'>
					<a href='mailto:".$a_email."?subject=More info requested&body=".$person_body."'>
						<input type='button' name='btnPerson' value='Contact Person'/>
					</a>
				</span>";
			}
			if ( !$r_email == "" AND $ok_update == FALSE ) { 
				if ( !empty($a_email) ) {
					$returnhtml .= "<span style='padding-left:15px;'>or</span>";
				}
				$returnhtml .= "<span style='padding-left:15px;'>
				<a href='mailto:".$r_email."?subject=More info requested&body=".$referrer_body."'>
				<input type='button' name='btnReferrer' value='Contact Referrer'/>
					</a>
				</span>";
			}
			$returnhtml .= "</div>";
	} // table_show
	if ( $show_mail == TRUE AND !empty($email)) {
		// echo "Show mail ".$show_mail;
		$returnhtml = "<span style='padding-left:15px;'>
			<a href='mailto:".$email."?subject=Your Violette Registry and Family Association application
			&body=Hello, ".$display_name."%0D%0A
			You have been accepted in the Violette Registry and Family Association! Your member number is VFA # ".$vfa_num."%0D%0A%0D%0A
			You should now go to our web site (VioletteRegistry.com) and complete your Member Profile by filling in your contact info and preferences. %0D%0A%0D%0A
			Your login credentials are:%0D%0A Username: ".$vfa_id."0D%0A Password: ".$password."%0D%0A%0D%0A
			Once there, select Membership/Member Profile Editing from the menu. You can use this at any time to update your Member Profile. %0D%0A
			Be sure to keep your email address up-to-date so you can receive news and other items! %0D%0A%0D%0A
			Use Genealogy/FamilyTree to see what family tree info we have for you. Contact our Genealogist, for any questions or updates to 
			your family tree. You can reach the Genealogist by email - look for the contact info in the footer of any page at our web site. %0D%0A%0D%0A
			Contact our Membership Secretary if you have any membership questions. Look for the contact info in the footer of any page at our web site.%0D%0A%0D%0A 
			Welcome to the Association!%0D%0A%0D%0A'>
			<input type='button' name='btnEMail' value='Send Acceptance Email'/>
			</a>
		</span>";
	} // ok to email
	if ( !empty( $success_message )) { 	
		$returnhtml .= "<br/><div class='success-message'>";
		if(isset($success_message)) {
			$returnhtml .= $success_message."</div><br/>";
		}
	}
	if ( !empty( $error_message )) { 	
		$returnhtml .= "<br/><div class='error-message'>";
		if(isset($error_message)) {
			$returnhtml .= $error_message."</div><br/>";
		}
	}
	if ( $table_show == TRUE ) {
		$returnhtml .= $tableheader;
			$returnhtml .= $rowstart;
				$returnhtml .= "<td width='50%' style='vertical-align:top'>";
					$returnhtml .= "<form method='POST' name='frmAddForm'>";
						$returnhtml .= "<fieldset id='fldAdd' class='edit' visible='true'>";
							$returnhtml .= "<legend style='font-size: 12px; background-color: inherit;'>Person's Info</legend>";
							$returnhtml .= "<table width='50%'>";
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label>Username:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$_SESSION['mbr']."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label>Add Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$add_date."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtRIN'>Person ID - P";
										$returnhtml .= "<input type='text' id='txtRIN' name='txtRIN' size='10' value='".$a_rowdata['a_rin']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtFirstName'>Given Name(s)";
										$returnhtml .= "<input type='text' id='txtFirstName' name='txtFirstName' size='75' value='".$a_rowdata['a_first_name']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtMiddleName'>Middle Name";
										$returnhtml .= "<input type='text' id='txtMiddleName' name='txtMiddleName' size='75' value='".$a_rowdata['a_middle_name']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtLastName'>Last or Maiden Name";
										$returnhtml .= "<input type='text' id='txtLastName' name='txtLastName' size='75' value='".$a_rowdata['a_last_name']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='radGender'>Gender";
										if($a_rowdata['a_gender'] == 'F') { 
											$returnhtml .= "<input type='radio' name='radGender' value='M'>Male</>";
											$returnhtml .= "<input type='radio' name='radGender' value='F' checked>Female</>";
										} else {
											$returnhtml .= "<input type='radio' name='radGender' value='M' checked>Male</>";
											$returnhtml .= "<input type='radio' name='radGender' value='F'>Female</>";
										} 
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='datDOB'>Date of Birth";
										$returnhtml .= "<input type='date' id='datDOB' name='datDOB' size='9' value='".$a_rowdata['a_dob']."' /></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtPOB'>Place of Birth";
										$returnhtml .= "<input type='text' id='txtPOB' name='txtPOB' size='75' value='".$a_rowdata['a_pob']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtEmail'>Email";
										$returnhtml .= "<input type='text' id='txtEmail' name='txtEmail' size='75' value='".$a_rowdata['a_email']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtAddr1'>Address 1";
										$returnhtml .= "<input type='text' id='txtAddr1' name='txtAddr1' size='75' value='".$a_rowdata['street_addr_1']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtAddr2'>Address 2";
										$returnhtml .= "<input type='text' id='txtAddr2' name='txtAddr2' size='75' value='".$a_rowdata['street_addr_2']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtPOAddr'>PO Address";
										$returnhtml .= "<input type='text' id='txtPOAddr' name='txtPOAddr' size='75' value='".$a_rowdata['po_addr']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtCity'>City";
										$returnhtml .= "<input type='text' id='txtCity' name='txtCity' size='75' value='".$a_rowdata['city']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtState'>State/Province";
										$returnhtml .= "<input type='text' id='txtState' name='txtState' size='75' value='".$a_rowdata['state_prov']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtPostal'>Postal Code";
										$returnhtml .= "<input type='text' id='txtPostal' name='txtPostal' size='75' value='".$a_rowdata['postal_code']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtCountry'>Country";
										$returnhtml .= "<input type='text' id='txtCountry' name='txtCountry' size='75' value='".$a_rowdata['country']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtHomePhone'>Home Phone";
										$returnhtml .= "<input type='text' id='txtHomePhone' name='txtHomePhone' size='75' value='".$a_rowdata['home_phone']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:right'>";
										$returnhtml .= "<label for='txtCellPhone'>Cell Phone";
										$returnhtml .= "<input type='text' id='txtCellPhone' name='txtCellPhone' size='75' value='".$a_rowdata['cell_phone']."'/></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
							$returnhtml .= "</table>";
						$returnhtml .= "</fieldset>";
					$returnhtml .= "</form>";
				$returnhtml .= "</td>";
				$returnhtml .= "<td style='vertical-align:top'>";
					$returnhtml .= "<fieldset id='fldRef' class='edit' visible='true'>";
						$returnhtml .= "<legend style='font-size: 12px; background-color: inherit;'>Referrer's Info</legend>";
						$returnhtml .= "<table width='50%'>";
							$returnhtml .= $rowstart;
								$returnhtml .= "<td style='text-align:left'>";
									$returnhtml .= "<label>Username:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$referrer."</i></label>";
								$returnhtml .= "</td>";
							$returnhtml .= $rowend;
							$returnhtml .= $rowstart;
								$returnhtml .= "<td style='text-align:left'>";
									$returnhtml .= "<label for='lblEmail'>Email:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$r_email."</i></label>";
								$returnhtml .= "</td>";
							$returnhtml .= $rowend;
							if (!$r_vfa == '') { 
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label for='lblVFA'>VFA:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$r_vfa."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
							} 
							if (!$r_rin == '') { 
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label for='lblRIN'>RIN:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$r_rin."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
							} 
							if (!$r_fname == '') { 
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label for='lblFirstName'>Given Name(s):&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$r_fname."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
							} 
							if (!$r_mname == '') { 
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label for='lblMiddleName'>Middle Name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$r_mname."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
							} 
							if (!$r_lname == '') {
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label for='lblLastName'>Last or Maiden Name:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$r_lname."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
							} 
							if (!$r_phone == '') {
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label for='lblPhone'>Phone:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$r_phone."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
							} 
							if (!$r_cell == '') { 
								$returnhtml .= $rowstart;
									$returnhtml .= "<td style='text-align:left'>";
										$returnhtml .= "<label for='lblCell'>Cell Phone:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>".$r_cell."</i></label>";
									$returnhtml .= "</td>";
								$returnhtml .= $rowend;
							} 
						$returnhtml .= "</table>";
					$returnhtml .= "</fieldset>";
				$returnhtml .= "</td>";
			$returnhtml .= $rowend;
		$returnhtml .= $tableender;
		$returnhtml .= "</form>";
	} // table_show

	return $returnhtml;
	
}  //vfa_adm_added
add_shortcode('vfa-adm-added', 'vfa_adm_added');
?>
<?php
/* -------------------------------------------------------------------------
vfa_adm_signup() allows admins to send signup to members
Created 180511 by DAV 
Mod 180704 by DAV
Mod 181113 by DAV to intercept process if error msg from vfa_register_user
---------------------------------------------------------------------------*/
function vfa_adm_signup() {
	global $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	
	// work on array of numbers
	if (!empty($_POST["btnNumbers"])) {
		$message = "";
		$list = explode(',', $_POST['txtNumbers'] );
		$list_length = count($list);
		$blank = "";
		// start the table output and process the list
		$message .= $tableheader;
		$message .= $rowstart;
			$message .= "<th width='5%' class='datahead'>VFA</th>";
			$message .= "<th width='20%' class='datahead'>Name</th>";
			$message .= "<th width='35%' class='datahead'>Email</th>";
			$message .= "<th width='40%' class='datahead'>Outcome</th>";
		$message .= $rowend;

		// get the records to display, if any
		for($x = 0; $x < $list_length; $x++) {
			// $message .= $list[$x];
			// $message .= "<br>";
			$query = "SELECT username, m_vfa, m_rin, m_first_name, m_middle_name, m_last_name, m_email, m_display_name FROM vfa_members WHERE m_vfa=".$list[$x];
			$rowdata = $wpdb->get_row( $query, ARRAY_A );
			// $result->execute();
			// $result->store_result();
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have any records?
				// $result->bind_result($usernm, $vfa, $rin, $fname, $lname, $email, $dname); //bind variables to selected fields
				// while ($result->fetch()) {
				$outcome = "found, ";
				if ( empty( $rowdata['m_email'] ) ) {
					$outcome .= "no email";
				} else {
					// generate password
					$password = vfa_generate_password();
					// register user
					$error_message = vfa_register_user( $rowdata['username'], $rowdata['m_first_name'], $rowdata['m_middle_name'], $rowdata['m_last_name'], $rowdata['m_vfa'], $rowdata['m_rin'], $rowdata['m_email'], $password );
					// now send signup email if no error
					if (!$error_message) {
						$outcome .= adm_signup_sent( $rowdata['m_first_name'], $rowdata['m_last_name'], $rowdata['username'], $rowdata['m_email'], $password, $m_display_name, $rowdata['m_vfa'] );
					} else {
					}
				}
				$message .= $rowstart;
					$message .= "<td>". $rowdata['m_vfa']."</a></td>";
					$message .= "<td>". $m_display_name ."</td>";
					$message .= "<td>". $rowdata['m_email'] ."</td>";
					$message .= "<td>". $outcome ."</td>";
				$message .= $rowend;
			} else {  // no, report no records
				$outcome = "not found";
				$message .= $rowstart;
					$message .= "<td>". $list[$x] ."</a></td>";
					$message .= "<td>". $blank ."</td>";
					$message .= "<td>". $blank ."</td>";
					$message .= "<td>". $outcome ."</td>";
				$message .= $rowend;
			}
		} // for
		$message .= $tableender;
		$success_message = $message;
	}

	// work on range
	if (!empty($_POST["btnRange"])) {
		$message = "";
		$blank = "";

		// start the table output and process the list
		$message .= $tableheader;
		$message .= $rowstart;
			$message .= "<th width='5%' class='datahead'>VFA</th>";
			$message .= "<th width='20%' class='datahead'>Name</th>";
			$message .= "<th width='35%' class='datahead'>Email</th>";
			$message .= "<th width='40%' class='datahead'>Outcome</th>";
		$message .= $rowend;

		// get the records to display, if any
		$range = explode('-', $_POST['txtNumberRange'] );
		$list_start = $range[0];
		$list_end = $range[1];
		for($x = $list_start; $x <= $list_end; $x++) {
			$query = "SELECT username, m_vfa, m_rin, m_first_name, m_middle_name, m_last_name, m_email, m_display_name, signup_sent FROM vfa_members WHERE m_vfa=".$x;
			$rowdata = $wpdb->get_row( $query, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have any records?
				// $message .= $rowdata['signup_sent'];
				$message .= "<br>";
				if ( $rowdata['signup_sent'] == 0 ) {
					$outcome = "found, ";
					if ( empty( $rowdata['m_email'] ) ) {
						$outcome .= "no email";
					} else {
						// generate password
						$password = vfa_generate_password();
						// register user
						$error_message = vfa_register_user( $rowdata['username'], $rowdata['m_first_name'], $rowdata['m_middle_name'], $rowdata['m_last_name'], $rowdata['m_vfa'], $rowdata['m_rin'], $rowdata['m_email'], $password );
						// now send signup email
						$outcome .= adm_signup_sent( $rowdata['m_first_name'], $rowdata['m_last_name'], $rowdata['username'], $rowdata['m_email'], $password, $rowdata['m_display_name'], $rowdata['m_vfa'] );
					}
					$message .= $rowstart;
						$message .= "<td>". $rowdata['m_vfa']."</a></td>";
						$message .= "<td>". $m_display_name ."</td>";
						$message .= "<td>". $rowdata['m_email'] ."</td>";
						$message .= "<td>". $outcome ."</td>";
					$message .= $rowend;
				} // signup_sent
			} else {  // no, report no records
				$outcome = "not found";
				$message .= $rowstart;
					$message .= "<td>". $x ."</a></td>";
					$message .= "<td>". $blank ."</td>";
					$message .= "<td>". $blank ."</td>";
					$message .= "<td>". $outcome ."</td>";
				$message .= $rowend;
			} // num_rows
		} // for
		$message .= $tableender;
		$success_message = $message;
	}
	
	// work on list of 100
	if (!empty($_POST['btnFirst100'])) {
		$message = "";
		$blank = "";

		// start the table output, then process the list
		$message .= $tableheader;
		$message .= $rowstart;
			$message .= "<th width='5%' class='datahead'>VFA</th>";
			$message .= "<th width='20%' class='datahead'>Name</th>";
			$message .= "<th width='35%' class='datahead'>Email</th>";
			$message .= "<th width='40%' class='datahead'>Outcome</th>";
		$message .= $rowend;

		// get the records to display, if any
		$query = "SELECT username, m_vfa, m_rin, m_first_name, m_middle_name, m_last_name, m_email, m_status, m_classif, signup_sent 
		FROM vfa_members WHERE (m_email <> '' AND m_status = 'A' AND m_classif = 'M' AND signup_sent = 0) ORDER BY m_vfa LIMIT 100";
		$rowdata = $wpdb->get_results( $query );
		foreach ( $rowdata as $member ) {
			$username = $member->username;
			$first_name = $member->m_first_name;
			$middle_name = $member->m_middle_name;
			$last_name = $member->m_last_name;
			$display_name = $first_name." ".$last_name;
			$vfa = $member->m_vfa;
			$rin = $member->m_rin;
			$email = $member->m_email;
			// generate password
			$password = vfa_generate_password();
			// register user
			$error_message = vfa_register_user( $username, $first_name, $middle_name, $last_name, $vfa, $rin, $email, $password );
			// now send signup email
			$outcome .= adm_signup_sent( $first_name, $last_name, $username, $email, $password, $display_name, $vfa );
			$message .= $rowstart;
			$message .= "<td>". $vfa."</a></td>";
			$message .= "<td>". $display_name ."</td>";
			$message .= "<td>". $email ."</td>";
			$message .= "<td>". $outcome ."</td>";
			$message .= $rowend;
			
		} // foreach
		$message .= $tableender;
		$success_message = $message;

	}
	?>
	<?php if(!empty($error_message)) { ?>	
		<div class="error-message"><?php if(isset($error_message)) echo $error_message; ?></div>
	<?php } ?>
	<?php if(!empty($success_message)) { ?>	
		<div class="success-message"><?php if(isset($success_message)) echo $success_message; ?></div>
	<?php } ?>
	<form method="POST" id="frmMemberSignup">
		<div id='divUsers' style='padding-bottom:5px;'>
			<table>
				<tbody>
					<tr>
						<td width="50%">
							<fieldset id="fldSignup" class="edit" visible="true">
								<legend style="font-size: 12px; background-color: inherit;">Member List</legend>
								<table width="50%">
									<tr>
										<td style="text-align:right">
											<label for="txtNumbers">VFA number(s), comma-separated list
											<input type="text" id="txtNumbers" name="txtNumbers" size="35" autofocus value="<?php if(isset($_POST['txtNumbers'])) echo $_POST['txtNumbers']; ?>"/></label>
											<div id='divSignup' style='padding-bottom:5px;'>
												<span style="padding-right:15px;"><input type="submit" name="btnNumbers" value="Send Signup Email to List"/></span>
											</div>
										</td>
									</tr>
									<tr>
										<td style="text-align:center">
										OR
										</td>
									</tr>
									<tr>
										<td style="text-align:right">
											<label for="txtNumberRange">VFA number(s), range with hyphen
											<input type="text" id="txtNumberRange" name="txtNumberRange" size="35" autofocus value="<?php if(isset($_POST['txtNumberRange'])) echo $_POST['txtNumberRange']; ?>"/></label>
											<div id='divRange' style='padding-bottom:5px;'>
												<span style="padding-right:15px;"><input type="submit" name="btnRange" value="Send Signup Email to Range"/></span>
											</div>
										</td>
									</tr>
									<tr>
										<td style="text-align:center">
										OR
										</td>
									</tr>
									<tr>
										<td style="text-align:right">
											<div id='divFirst' style='padding-bottom:5px;'>
												<span style="padding-right:15px;"><input type="submit" name="btnFirst100" value="Send Signup Email to First 100"/></span>
											</div>
										</td>
									</tr>
								</table>
							</fieldset>
						</td>
						<td>
							<p>Use this area to create sign-in credentials (or to send password update) for a Member and send it to them by email (if available).</p>
							<p>Enter a single VFA number, a group of VFA numbers separated by commas, and/or a range of numbers separated by a hyphen.</p>
							<p>Selecting First 100 will send credentials to a group of 100 members who have email, selected from the list in VFA number order, where the signup_sent field is No (0).</p>
						</td>
					</tr>
				</tbody>
			</table>
			<div id='memberlist'></div>
		</div>
	</form>
	<div id='outcome1'></div>
	<div id='outcome2'></div>
	<?php
	}  //vfa_adm_signup
add_shortcode('vfa-adm-signup', 'vfa_adm_signup');
?>
<?php
/* -------------------------------------------------------------------------
vfa_vote() provides a routine for member voting on officers
Created 200906 by DAV 
---------------------------------------------------------------------------*/
function vfa_vote() {
	global $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	$showBallot = "hidden";

	// check UserID to see if found and valid
	if (isset($_POST["btnUserID"])) {
		$member = strtoupper( $_POST['txtUserID'] );

		// in proper format? does it contain VFA?
		if (substr($member, 0, 3)!="VFA") {
			$error_message = 'Must start with VFA';
		}

		// has member voted already?
		$query = 'SELECT username, voted_on FROM vfa_voting WHERE username="'. $member . '"';
		$rowdata = $wpdb->get_row( $query, ARRAY_A );
		$num_rows = $wpdb->num_rows;
		if ($num_rows > 0) {  // do we have any records?
			$error_message = 'Member has already voted on '. $rowdata['voted_on'];
		}
	
		// no error, ok to proceed
		if (empty($error_message)) {
			// strip VFA from username to allow spouses with a VFA number to vote
			$memberorspouse = ltrim($member, "VFA");
			// is that member valid?
			// $query = 'SELECT username, m_display_name FROM vfa_members WHERE username="'. $member . '" AND m_status="A"';
			$query = 'SELECT m_vfa, m_display_name FROM vfa_members WHERE m_vfa="'. $memberorspouse . '" AND m_status="A"';
			$rowdata = $wpdb->get_row( $query, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have any records?
				$display_name = $rowdata['m_display_name'];
				$success_message = 'That is valid and is the number for '. $display_name;
				$showBallot = "";
				$_SESSION["member"] = $member;
			} else {
				$query = 'SELECT s_vfa, s_display_name FROM vfa_members WHERE s_vfa="'. $memberorspouse . '" AND s_status="A"';
				$rowdata = $wpdb->get_row( $query, ARRAY_A );
				$num_rows = $wpdb->num_rows;
				if ($num_rows > 0) {  // do we have any records?
					$display_name = $rowdata['s_display_name'];
					$success_message = 'That is valid and is the number for '. $display_name;
					$showBallot = "";
					$_SESSION["member"] = $member;
				}
			}
		}
		if (empty($success_message)) { 
			$error_message = 'Not found or not an Active member'; 
		}
	} // btnUserID

	// process voting
	if (isset($_POST["btnVote"])) {
		$pres = $_POST["radPres"];
		if ($pres=="Writein") {
			$pres = $_POST["txtPres2"];
			if (empty($pres)) {$pres="abstained";}
		} else {
			$pres="David A. Violette, VFA621";
		}
		$vpres = $_POST["radVPres"];
		if ($vpres=="Writein") {
			$vpres = $_POST["txtVPres2"];
			if (empty($vpres)) {$vpres="abstained";}
		} else {
			$vpres="Richard G. Violette, VFA #1812";
		}
		$secy = $_POST["radSecy"];
		if ($secy=="Writein") {
			$secy = $_POST["txtSecy2"];
			if (empty($secy)) {$secy="abstained";}
		} else {
			$secy="Peter R. Violette, VFA #1793";
		}
		$treas = $_POST["radTreas"];
		if ($treas=="Writein") {
			$treas = $_POST["txtTreas2"];
			if (empty($treas)) {$treas="abstained";}
		} else {
			$treas="Paul L. Violette, VFA #1589";
		}

		// have the info, now store the results
		$success_message = "";
		$data = array('username' => $_SESSION["member"], 'president' => $pres, 'vpresident' => $vpres, 'secretary' => $secy, 'treasurer' => $treas);
		$format = array( '%s', '%s', '%s', '%s', '%s' );
		$result = $wpdb->insert( 'vfa_voting', $data, $format );
		if($result == 1) {
			$thanks_message = "Thank you for voting!";
			unset($success_message);
			unset($error_message);
		} else {
			$error_message .= "Problem in voting. Try Again!   ";	
			unset($thanks_message);	
			unset($success_message);
		}

	} // btnVote

	$returnhtml .= '
		<div id="divID">
			<form method="POST" name="frmIDForm">
			<fieldset id="fldVFAID" class="edit" visible="true">
				<legend style="font-size: 12px; background-color: inherit;">Member Info</legend>
				<table width="50%">
					<tr>
						<td style="text-align:left" width="30%">
							<label for="txtUserID">VFA ID
							<input type="text" id="txtUserID" name="txtUserID" size="15" autofocus value="' . $member . '"/></label>
						</td>
						<td>
							<p>Your VFA ID consists of the letters VFA followed by yout VFA number. Use your VFA number, not your family tree P number.</p>
							<p>If you do not know your VFA #, you can find it by going to Membership/Am I A Member.</p>
						</td>
					</tr>
					<tr>
						<td style="text-align:center" colspan="2" width="100%">
							<input type="submit" name="btnUserID" value="Check ID"/>
						</td>
					</tr>
				</table>';
				if(!empty($error_message)) {   
					$returnhtml .= '<div class="error-message">' . $error_message . '</div>';
				}
				if(!empty($success_message)) {
					$returnhtml .= '<div class="success-message">' . $success_message . '</div>';
				}
	$returnhtml .= '</fieldset>
		</form>
		</div>';
		if(isset($thanks_message)) {   
			$returnhtml .= '<div class="success-message">' . $thanks_message . '</div>';
		}
	$returnhtml .= '<div id="divBallot"' . $showBallot .'>';
	$returnhtml .= '
			<form method="POST" name="frmVoteForm">
				<fieldset id="fldBallot" class="edit" visible="true">
					<legend style="font-size: 12px; background-color: inherit;">The Ballot</legend>
					<table width="100%">
						<tr>
							<td class="datalabel" style="text-align:left" width="20%">
								President:
							</td>
							<td width="50%">
								<input type="radio" id="radPres1" name="radPres" value="Dave" checked="true">David A. Violette, VFA #621</input><br/>
								<input type="radio" id="radPres2" name="radPres" value="Writein">Writein:<input type="text" id="txtPres2" name="txtPres2" size="50"/></input>
							</td>
							<td rowspan="4">
								Instructions:<br/>
								Click the button to choose a candidate.<br/><br/>
								If you select "Writein", enter the name of the person and their VFA number, if you know it.<br/><br/>
								To not vote for an office, select "Writein" and leave the name blank.
							</td>
						</tr>
						<tr>
							<td class="datalabel" style="text-align:left" width="20%">
								Vice President:
							</td>
							<td>
								<input type="radio" id="radVPres1" name="radVPres" value="Rich" checked="true">Richard G.. Violette, VFA #1812</input><br/>
								<input type="radio" id="radVPres2" name="radVPres" value="Writein">Writein:<input type="text" id="txtVPres2" name="txtVPres2" size="50"/></input>
							</td>
						</tr>
						<tr>
							<td class="datalabel" style="text-align:left" width="20%">
								Secretary:
							</td>
							<td>
								<input type="radio" id="radSecy1" name="radSecy" value="Pete" checked="true">Peter R. Violette, VFA #1793</input><br/>
								<input type="radio" id="radSecy2" name="radSecy" value="Writein">Writein:<input type="text" id="txtSecy2" name="txtSecy2" size="50"/></input>
							</td>
						</tr>
						<tr>
							<td class="datalabel" style="text-align:left" width="20%">
								Treasurer:
							</td>
							<td>
								<input type="radio" id="radTreas1" name="radTreas" value="Rich" checked="true">Paul L. Violette, VFA #1589</input><br/>
								<input type="radio" id="radTreas2" name="radTreas" value="Writein">Writein:<input type="text" id="txtTreas2" name="txtTreas2" size="50"/></input>
							</td>
						</tr>
						<tr>
							<td style="text-align:center" colspan="2" width="100%">
								<input type="submit" name="btnVote" value="Vote"/>
							</td>
						</tr>
					</table>
					</fieldset>
				</form>
			</div>';
	
	return $returnhtml;

} // vfa_vote
add_shortcode('vfa-vote', 'vfa_vote');
?>
<?php
/* -------------------------------------------------------------------------
vfa_adm_missing() provides a routine to contact relatives of a missing member
Created 180906 by DAV 
Mod 210607 by DAV to convert from echo to return
---------------------------------------------------------------------------*/
function vfa_adm_missing() {
	global $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	$returnhtml = "";

	// work on array of numbers
	if (!empty($_POST["btnNumbers"])) {
		$message = "";
		$list = explode(',', $_POST['txtNumbers'] );
		$list_length = count($list);
		$blank = "";
		// start the table output and process the list
		$message .= $tableheader;
		$message .= $rowstart;
			$message .= "<th width='5%' class='datahead'>VFA</th>";
			$message .= "<th width='30%' class='datahead'>Name</th>";
			$message .= "<th width='65%' class='datahead'>Outcome</th>";
		$message .= $rowend;

		// get the records to display, if any
		for($x = 0; $x < $list_length; $x++) {
			// $message .= $list[$x];
			// $message .= "<br>";
			$query = "SELECT username, m_vfa, m_rin, m_first_name, m_middle_name, m_last_name, m_email, m_display_name FROM vfa_members WHERE m_vfa=".$list[$x];
			$rowdata = $wpdb->get_row( $query, ARRAY_A );
			// $result->execute();
			// $result->store_result();
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have any records?
				$outcome = "found, ";
				// process member group
				$result = vfa_family_list( $list[$x] );
				$outcome .= "emails sent";
				$message .= $rowstart;
					$message .= "<td>". $rowdata['m_vfa']."</a></td>";
					$message .= "<td>". $rowdata['m_display_name']."</td>";
					$message .= "<td>". $outcome ."</td>";
				$message .= $rowend;
			} else {  // no, report no records
				$outcome = "not found";
				$message .= $rowstart;
					$message .= "<td>". $list[$x] ."</a></td>";
					$message .= "<td>". $blank ."</td>";
					$message .= "<td>". $outcome ."</td>";
				$message .= $rowend;
			}
		} // for
		$message .= $tableender;
		$success_message = $message;
	}

	// work on range
	if (!empty($_POST["btnRange"])) {
		$message = "";
		$blank = "";

		// start the table output and process the list
		$message .= $tableheader;
		$message .= $rowstart;
			$message .= "<th width='5%' class='datahead'>VFA</th>";
			$message .= "<th width='20%' class='datahead'>Name</th>";
			$message .= "<th width='35%' class='datahead'>Email</th>";
			$message .= "<th width='40%' class='datahead'>Outcome</th>";
		$message .= $rowend;

		// get the records to display, if any
		$range = explode('-', $_POST['txtNumberRange'] );
		$list_start = $range[0];
		$list_end = $range[1];
		for($x = $list_start; $x <= $list_end; $x++) {
			$query = "SELECT username, m_vfa, m_rin, m_first_name, m_middle_name, m_last_name, m_email, m_display_name, signup_sent FROM vfa_members WHERE m_vfa=".$x;
			$rowdata = $wpdb->get_row( $query, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have any records?
				// $message .= $rowdata['signup_sent'];
				$message .= "<br>";
				if ( $rowdata['signup_sent'] == 0 ) {
					$outcome = "found, ";
					if ( empty( $rowdata['m_email'] ) ) {
						$outcome .= "no email";
					} else {
						// generate password
						$password = vfa_generate_password();
						// register user
						$error_message = vfa_register_user( $rowdata['username'], $rowdata['m_first_name'], $rowdata['m_middle_name'], $rowdata['m_last_name'], $rowdata['m_vfa'], $rowdata['m_rin'], $rowdata['m_email'], $password );
						// now send signup email
						$outcome .= adm_signup_sent( $rowdata['m_first_name'], $rowdata['m_last_name'], $rowdata['username'], $rowdata['m_email'], $password, $rowdata['m_display_name'], $rowdata['m_vfa'] );
					}
					$message .= $rowstart;
						$message .= "<td>". $rowdata['m_vfa']."</a></td>";
						$message .= "<td>". $m_display_name ."</td>";
						$message .= "<td>". $rowdata['m_email'] ."</td>";
						$message .= "<td>". $outcome ."</td>";
					$message .= $rowend;
				} // signup_sent
			} else {  // no, report no records
				$outcome = "not found";
				$message .= $rowstart;
					$message .= "<td>". $x ."</a></td>";
					$message .= "<td>". $blank ."</td>";
					$message .= "<td>". $blank ."</td>";
					$message .= "<td>". $outcome ."</td>";
				$message .= $rowend;
			} // num_rows
		} // for
		$message .= $tableender;
		$success_message = $message;
	}
	
	// work on list of 100
	if (!empty($_POST['btnFirst100'])) {
		$message = "";
		$blank = "";

		// start the table output, then process the list
		$message .= $tableheader;
		$message .= $rowstart;
			$message .= "<th width='5%' class='datahead'>VFA</th>";
			$message .= "<th width='20%' class='datahead'>Name</th>";
			$message .= "<th width='35%' class='datahead'>Email</th>";
			$message .= "<th width='40%' class='datahead'>Outcome</th>";
		$message .= $rowend;

		// get the records to display, if any
		$query = "SELECT username, m_vfa, m_rin, m_first_name, m_middle_name, m_last_name, m_email, m_status, m_classif, signup_sent 
		FROM vfa_members WHERE (m_email <> '' AND m_status = 'A' AND m_classif = 'M' AND signup_sent = 0) ORDER BY m_vfa LIMIT 100";
		$rowdata = $wpdb->get_results( $query );
		foreach ( $rowdata as $member ) {
			$username = $member->username;
			$first_name = $member->m_first_name;
			$middle_name = $member->m_middle_name;
			$last_name = $member->m_last_name;
			$display_name = $first_name." ".$last_name;
			$vfa = $member->m_vfa;
			$rin = $member->m_rin;
			$email = $member->m_email;
			// generate password
			$password = vfa_generate_password();
			// register user
			$error_message = vfa_register_user( $username, $first_name, $middle_name, $last_name, $vfa, $rin, $email, $password );
			// now send signup email
			$outcome .= adm_signup_sent( $first_name, $last_name, $username, $email, $password, $display_name, $vfa );
			$message .= $rowstart;
			$message .= "<td>". $vfa."</a></td>";
			$message .= "<td>". $display_name ."</td>";
			$message .= "<td>". $email ."</td>";
			$message .= "<td>". $outcome ."</td>";
			$message .= $rowend;
			
		} // foreach
		$message .= $tableender;
		$success_message = $message;

	}
	
	if ( !empty( $success_message )) { 	
		$returnhtml .= "<br/><div class='success-message'>";
		if(isset($success_message)) {
			$returnhtml .= $success_message."</div><br/>";
		}
	}
	if ( !empty( $error_message )) { 	
		$returnhtml .= "<br/><div class='error-message'>";
		if(isset($error_message)) {
			$returnhtml .= $error_message."</div><br/>";
		}
	}

	$returnhtml .= "<form method='POST' id='frmMemberSignup'>
		<div id='divUsers' style='padding-bottom:5px;'>
			<table>
				<tbody>
					<tr>
						<td width='50%' style='vertical-align:top'>
							<fieldset id='fldSignup' class='edit' visible='true'>
								<legend style='font-size: 12px; background-color: inherit;'>Member List</legend>
								<table width='50%'>
									<tr>
										<td style='text-align:right'>
											<label for='txtNumbers'>Missing VFA number(s), comma-separated list
											<input type='text' id='txtNumbers' name='txtNumbers' size='35' autofocus value='";
											if(isset($_POST['txtNumbers'])) { $returnhtml .= $_POST['txtNumbers']; }
											$returnhtml .= "'/></label>
											<div id='divSignup' style='padding-bottom:5px;'>
												<span style='padding-right:15px;'><input type='submit' name='btnNumbers' value='Send Missing Email to List'/></span>
											</div>
										</td>
									</tr>
									<tr>
										<td style='text-align:center'>
										OR
										</td>
									</tr>
									<tr>
										<td style='text-align:right'>
											<label for='txtNumberRange'>VFA number(s), range with hyphen
											<input type='text' id='txtNumberRange' name='txtNumberRange' size='35' autofocus value='";
											if(isset($_POST['txtNumberRange'])) { $returnhtml .= $_POST['txtNumberRange']; }
											$returnhtml .= "'/></label>
											<div id='divRange' style='padding-bottom:5px;'>
												<span style='padding-right:15px;'><input type='submit' name='btnRange' value='Send Missing Email to Range'/></span>
											</div>
										</td>
									</tr>
									<tr>
										<td style='text-align:center'>
										OR
										</td>
									</tr>
									<tr>
										<td style='text-align:right'>
											<div id='divFirst' style='padding-bottom:5px;'>
												<span style='padding-right:15px;'><input type='submit' name='btnFirst100' value='Send Missing Email to First 100'/></span>
											</div>
										</td>
									</tr>
								</table>
							</fieldset>
						</td>
						<td style='vertical-align:top'>
							<p>Use this area to send emails to relatives of a Missing Member.</p>
							<p>Enter a single VFA number, a group of VFA numbers separated by commas, and/or a range of numbers separated by a hyphen.</p>
							<p>Selecting First 100 will send credentials to a group of 100 missing members who have email, selected from the list in VFA number order, where the m_status field is M.</p>
						</td>
					</tr>
				</tbody>
			</table>
			<div id='memberlist'></div>
		</div>
	</form>
	<div id='outcome1'></div>
	<div id='outcome2'></div>";
	return $returnhtml;
	}  //vfa_adm_missing
add_shortcode('vfa-adm-missing', 'vfa_adm_missing');
?>
<?php
/* -------------------------------------------------------------------------
vfa_adm_reports() provides for creating various reports for admins
Created 180712 by DAV 
---------------------------------------------------------------------------*/
function vfa_adm_reports() {
	?>
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script type='text/javascript'>
		// When the document loads do everything inside here ...
		jQuery(document).ready(function(){
			// first time through, select without parameters
			// jQuery("#memberlist" ).load( "/wp-content/plugins/vfa-member-admin/member-search.php"); //load initial records
				// jQuery("#outcome1").html( "after init load" ); //show we got here
			
			// after, respond to parameter input
			jQuery('#btnMembers').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				var val = document.getElementById('btnMembers').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-reports.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				jQuery('#divMembersDownload').show();
				jQuery('#divActiveDownload').hide();
				jQuery('#divChildDownload').hide();
				jQuery('#divAssociatesDownload').hide();
				jQuery('#divMissingDownload').hide();
				jQuery('#divMailUSADownload').hide();
				jQuery('#divMailCANDownload').hide();
				jQuery('#divMailChimpDownload').hide();
				jQuery('#divLinkLegend').show();
				jQuery('#divDefaultLegend').show();
				jQuery('#divMailChimpLegend').hide();
				return false;
			})  // search

			jQuery('#btnMissing').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				var val = document.getElementById('btnMissing').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-reports.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				jQuery('#divMissingDownload').show();
				jQuery('#divActiveDownload').hide();
				jQuery('#divChildDownload').hide();
				jQuery('#divAssociatesDownload').hide();
				jQuery('#divMailUSADownload').hide();
				jQuery('#divMailCANDownload').hide();
				return false;
			})  // search

			// executes whem btnLastName button clicked
			jQuery('#btnActive').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				var val = document.getElementById('btnActive').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-reports.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				jQuery('#divMembersDownload').hide();
				jQuery('#divActiveDownload').show();
				jQuery('#divChildDownload').hide();
				jQuery('#divAssociatesDownload').hide();
				jQuery('#divMissingDownload').hide();
				jQuery('#divMailUSADownload').hide();
				jQuery('#divMailCANDownload').hide();
				jQuery('#divMailChimpDownload').hide();
				jQuery('#divLinkLegend').show();
				jQuery('#divDefaultLegend').show();
				jQuery('#divMailChimpLegend').hide();
				return false;
			})  // search

			// executes whem btnVFA button clicked
			jQuery('#btnChild').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				var val = document.getElementById('btnChild').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-reports.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				jQuery('#divMembersDownload').hide();
				jQuery('#divActiveDownload').hide();
				jQuery('#divChildDownload').show();
				jQuery('#divAssociatesDownload').hide();
				jQuery('#divMissingDownload').hide();
				jQuery('#divMailUSADownload').hide();
				jQuery('#divMailCANDownload').hide();
				jQuery('#divMailChimpDownload').hide();
				jQuery('#divLinkLegend').show();
				jQuery('#divDefaultLegend').show();
				jQuery('#divMailChimpLegend').hide();
				return false;
			})  // search
		
			// executes whem btnRIN button clicked
			jQuery('#btnAssociates').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				var val = document.getElementById('btnAssociates').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-reports.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				jQuery('#divMembersDownload').hide();
				jQuery('#divActiveDownload').hide();
				jQuery('#divChildDownload').hide();
				jQuery('#divAssociatesDownload').show();
				jQuery('#divMissingDownload').hide();
				jQuery('#divMailUSADownload').hide();
				jQuery('#divMailCANDownload').hide();
				jQuery('#divMailChimpDownload').hide();
				jQuery('#divLinkLegend').show();
				jQuery('#divDefaultLegend').show();
				jQuery('#divMailChimpLegend').hide();
				return false;
			})  // search

			// executes whem btnCity button clicked
			jQuery('#btnMailUSA').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				var val = document.getElementById('btnMailUSA').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-reports.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            			alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				jQuery('#divMembersDownload').hide();
				jQuery('#divActiveDownload').hide();
				jQuery('#divChildDownload').hide();
				jQuery('#divAssociatesDownload').hide();
				jQuery('#divMissingDownload').hide();
				jQuery('#divMailUSADownload').show();
				jQuery('#divMailCANDownload').hide();
				jQuery('#divMailChimpDownload').hide();
				jQuery('#divLinkLegend').hide();
				jQuery('#divDefaultLegend').hide();
				jQuery('#divMailChimpLegend').hide();
				return false;
			})  // search

			// executes whem btnState button clicked
			jQuery('#btnMailCAN').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				var val = document.getElementById('btnMailCAN').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-reports.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            			alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				jQuery('#divMembersDownload').hide();
				jQuery('#divActiveDownload').hide();
				jQuery('#divChildDownload').hide();
				jQuery('#divAssociatesDownload').hide();
				jQuery('#divMissingDownload').hide();
				jQuery('#divMailUSADownload').hide();
				jQuery('#divMailCANDownload').show();
				jQuery('#divMailChimpDownload').hide();
				jQuery('#divLinkLegend').hide();
				jQuery('#divDefaultLegend').hide();
				jQuery('#divMailChimpLegend').hide();
				return false;
			})  // search

			// executes whem btnEmail button clicked
			jQuery('#btnMailChimp').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				var val = document.getElementById('btnMailChimp').value;
				jQuery.ajax({
					type: "POST",
					data: {btn: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-reports.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            			alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				jQuery('#divMembersDownload').hide();
				jQuery('#divActiveDownload').hide();
				jQuery('#divChildDownload').hide();
				jQuery('#divAssociatesDownload').hide();
				jQuery('#divMissingDownload').hide();
				jQuery('#divMailUSADownload').hide();
				jQuery('#divMailCANDownload').hide();
				jQuery('#divMailChimpDownload').show();
				jQuery('#divLinkLegend').hide();
				jQuery('#divDefaultLegend').hide();
				jQuery('#divMailChimpLegend').show();
				return false;
			})  // search

			// executes whem btnClear button clicked
			jQuery('#btnClear').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				jQuery.ajax({
					type: "POST",
					data: {btn: "Clear"},
					// data: {txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            			alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // clear

			// executes whem btnApplicants button clicked
			jQuery('#btnApplicants').click(function() {
				// jQuery("#outcome1").html( "trying" ); //show we got here
				jQuery.ajax({
					type: "POST",
					data: {btn: "Applicants"},
					// data: {txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            			alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // applicants

			// executes whem btnAdded button clicked
			jQuery('#btnAdded').click(function() {
				// jQuery("#outcome1").html( "added" ); //show we got here
				jQuery.ajax({
					type: "POST",
					data: {btn: "Added"},
					// data: {txt: val},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/member-search.php",
					beforeSend: function() {jQuery("#loading").fadeIn('fast');jQuery("#memberlist").fadeOut("fast");}, //fadeIn loading just when link is clicked
					success: function(tabledata){ //so, if data is retrieved, store it in html
						// jQuery("#loading").fadeOut('slow');
						jQuery("#memberlist").html( tabledata ); //show the html inside memberlist div
						// jQuery("#outcome2").html( "added success" ); //show we got here
						jQuery("#memberlist").fadeIn("fast"); //animation
					},
					error: function(xhr){
            			alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // added

		})  // document ready
	</script>

	<div id="reports">
		<form action='' method='POST' id='memberreportform'>
			<div id="report-options">
				<fieldset id="fldReports" class="edit" visible="true">
					<legend style="font-size: 12px; background-color: inherit;">Select Report Name Below</legend>
					<table>
						<tr>
							<td width="25%" style="text-align:right">
								<label for="btnMembers">All Members
								<input type="submit" id="btnMembers" value="Members"/></label>
								<div id="divMembersDownload" hidden="TRUE">
									<a href="http://www.violetteregistry.com/wp-content/files/VFA-Members.csv" class="button" target="_blank">Download File</a>
								</div>
							</td>
							<td width="25%" style="text-align:right">
								<label for="btnActive">Active Members
								<input type="submit" id="btnActive" value="Active"/></label>
								<div id="divActiveDownload" hidden="TRUE">
									<a href="http://www.violetteregistry.com/wp-content/files/VFA-Active.csv" class="button" target="_blank">Download File</a>
								</div>
							</td>
							<td width="25%" style="text-align:right">
								<label for="btnChild">Child Members
								<input type="submit" id="btnChild" value="Child"/></label>
								<div id="divChildDownload" hidden="TRUE">
									<a href="http://www.violetteregistry.com/wp-content/files/VFA-Child.csv" class="button" target="_blank">Download File</a>
								</div>
							</td>
							<td width="25%" style="text-align:right">
								<label for="btnAssociates">Associates
								<input type="submit" id="btnAssociates" value="Associates"/></label>
								<div id="divAssociatesDownload" hidden="TRUE">
									<a href="http://www.violetteregistry.com/wp-content/files/VFA-Associates.csv" class="button" target="_blank">Download File</a>
								</div>
							</td>
						</tr>
						<tr>
							<td width="25%" style="text-align:right">
								<label for="btnMissing">Missing Members
								<input type="submit" id="btnMissing" value="Missing"/></label>
								<div id="divMissingDownload" hidden="TRUE">
									<a href="http://www.violetteregistry.com/wp-content/files/VFA-Missing.csv" class="button" target="_blank">Download File</a>
								</div>
							</td>
							<td width="25%" style="text-align:right">
								<label for="btnMissing">Mail List - USA
								<input type="submit" id="btnMailUSA" value="MailUSA"/></label>
								<div id="divMailUSADownload" hidden="TRUE">
									<a href="http://www.violetteregistry.com/wp-content/files/VFA-Mail-USA.csv" class="button" target="_blank">Download File</a>
								</div>
							</td>
							<td width="25%" style="text-align:right">
								<label for="btnActive">Mail List - CAN
								<input type="submit" id="btnMailCAN" value="MailCAN"/></label>
								<div id="divMailCANDownload" hidden="TRUE">
									<a href="http://www.violetteregistry.com/wp-content/files/VFA-Mail-CAN.csv" class="button" target="_blank">Download File</a>
								</div>
							</td>
							<td width="25%" style="text-align:right">
								<label for="btnMailChimp">MailChimp Updater
								<input type="submit" id="btnMailChimp" value="MailChimp"/></label>
								<div id="divMailChimpDownload" hidden="TRUE">
									<a href="http://www.violetteregistry.com/wp-content/files/VFA-MailChimp.csv" class="button" target="_blank">Download File</a>
								</div>
							</td>
						</tr>
						<!-- <tr>
							<td width="50%" style="text-align:right">
								<label for="txtCity">City
								<input type="text" id="txtCity" size="35" />
								<input type="submit" id="btnCity" value="Search"/></label>
							</td>
							<td width="50%" style="text-align:right">
								<label for="txtState">State/Prov
								<input type="text" id="txtState" size="5" />
								<input type="submit" id="btnState" value="Search"/></label>
							</td>
						</tr> -->
						<!-- <tr>
							<td width="50%" style="text-align:right">
								<label for="txtEmail">Email
								<input type="text" id="txtEmail" size="50" />
								<input type="submit" id="btnEmail" value="Search"/></label>
							</td>
							<td width="50%" style="text-align:right">
								<input type="submit" id="btnApplicants" value="Show applicants"/>
								<input type="submit" id="btnAdded" value="Show added"/>
								<input type="submit" id="btnClear" value="Clear Results"/>
							</td>
						</tr> -->
						<tr>
							<td width="100%" colspan="4" align="center">
								<div id="divLinkLegend" hidden="TRUE">
									<p style="color: #0000FF; font-size: 1.2em; text-align: center;">Click on the blue link under USER in the table below to edit the info for that member.</p>
								</div>
								<div id="divMailChimpLegend" hidden="TRUE">
									<p style="font-size: 1.0em; text-align: center;">Email Preference: I=When Issued, Email Type: html or text, LEID = List/Entry ID, EUID = Entry ID.</p>
								</div>
								<div id="divDefaultLegend" hidden="TRUE">
									<p style="font-size: 1.0em; text-align: center;">CL = Classification, ST = Status, GN = Gender, ST = State.</p>
								</div>
							</td>
						</tr>
						<!-- <tr>
							<td width="100%" colspan="4" align="center">
							</td>
						</tr> -->
					</table>
				</fieldset>
			</div>
		</form>
		<div id='memberlist'></div>
	</div>
	<div id='outcome1'></div>
	<div id='outcome2'></div>
<?php }  // vfa_adm_reports
add_shortcode('vfa-adm-reports', 'vfa_adm_reports');
?>
<?php
/* -------------------------------------------------------------------------
vfa_mbr_edit() allows editing personal data of member and spouse by member
Created 180325 by DAV 
---------------------------------------------------------------------------*/
function vfa_mbr_edit() {

		$_SESSION['mbr'] = $_GET{'mbr'};
			$username = $current_user->user_login;  // it's a member coming in
			// echo $username;
	?>
	<!-- Ref: https://www.jquery-az.com/create-jquery-tabs-ui-plugin-demo-free-code/ -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

  <script>
		$(function() {
			$( "#divTabs" ).tabs();
		});
  </script>

	<div id="divTabs">
		<ul>
			<li><a href="#personal">Personal Info</a></li>
			<li><a href="#contact">Contact Info</a></li>
			<li><a href="#events">Events</a></li>
			<li><a href="#preferences">Preferences</a></li>
		</ul>
		<div id="personal">
			<?php
				echo do_shortcode('[vfa-personal-edit]');
			?>
		</div>  
		<div id="contact">
			<?php
				echo do_shortcode('[vfa-contact-edit]');
			?>
		</div>  
		<div id="events">
			<?php
				echo do_shortcode('[vfa-events]');
			?>
		</div>
		<div id="preferences">
			<?php
				echo do_shortcode('[vfa-preferences-edit]');
			?>
		</div>  
	</div>
	<div id='outcomeA'></div>
	<div id='outcomeB'></div>

<?php }  //vfa_mbr_edit
add_shortcode('vfa-mbr-edit', 'vfa_adm_edit');
?>
<?php
/* -------------------------------------------------------------------------
vfa_mbr_edit() allows showing personal data of member and spouse by member
Created 180325 by DAV 
---------------------------------------------------------------------------*/
function vfa_mbr_view() {

		$_SESSION['mbr'] = $_GET{'mbr'};
			$username = $current_user->user_login;  // it's a member coming in
			// echo $username;
	?>
	<!-- Ref: https://www.jquery-az.com/create-jquery-tabs-ui-plugin-demo-free-code/ -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

  <script>
		$(function() {
			$( "#divTabs" ).tabs();
		});
  </script>

	<div id="divTabs">
		<ul>
			<li><a href="#personal">Personal Info</a></li>
			<li><a href="#contact">Contact Info</a></li>
			<li><a href="#events">Events</a></li>
			<li><a href="#preferences">Preferences</a></li>
		</ul>
		<div id="personal">
			<?php
				echo do_shortcode('[vfa-personal]');
			?>
		</div>  
		<div id="contact">
			<?php
				echo do_shortcode('[vfa-contact]');
			?>
		</div>  
		<div id="events">
			<?php
				echo do_shortcode('[vfa-events]');
			?>
		</div>
		<div id="preferences">
			<?php
				echo do_shortcode('[vfa-preferences]');
			?>
		</div>  
	</div>
	<div id='outcomeA'></div>
	<div id='outcomeB'></div>

<?php }  //vfa_mbr_view
add_shortcode('vfa-mbr-view', 'vfa_mbr_view');
?>
<?php
/* -------------------------------------------------------------------------
vfa_apply() allows potential members to start the application process
Created 180408 by DAV 
Mod 180812 by DAV to chg RIN to Person ID
---------------------------------------------------------------------------*/
function vfa_apply() {
	// global variables
	global $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $president_email, $genealogist_email, $webmaster_email, $smtp_from_name;
	global $smtp_host, $smtp_password, $smtp_secure, $smtp_port;

	// define variables and set to empty values
	$fnameErr = $lnameErr = $rinErr = $emailErr = $error_message = $success_message = "";
	$fname = $lname = $rin = $email = $num_rows = $memberlist = "";

	if(!empty($_POST["btnApply"])) {
		/* Form Required Field Validation */
		foreach($_POST as $key=>$value) {
			if(empty($_POST[$key])) {
				$error_message .= "All fields are required.<br/>";
				break;
			}
		}
	
		// First Name Validation
		$fname = test_input($_POST["txtFirstName"]);
		$error_message .= val_name( $fname, "first" );
	
		// Last Name Validation
		$lname = test_input($_POST["txtLastName"]);
		$error_message .= val_name( $lname, "last" );
	
		$rin = test_input($_POST["txtRIN"]);
		$rin = filter_var($rin, FILTER_SANITIZE_NUMBER_INT );  // get rid of any prefix added
		if (empty($rin)) {
			$error_message .= "RIN must not be empty<br/>";
		} else {
			// RIN Validation
			$rin = test_input($_POST["txtRIN"]);
			$rin = filter_var($rin, FILTER_SANITIZE_NUMBER_INT );  // get rid of any prefix added
			$error_message = val_rin( $rin, "");
		
			// check for RIN already a member
			$query = "SELECT m_rin FROM vfa_members WHERE m_rin = %d";
			$result = $wpdb->prepare( $query, $rin );
			$rowdata = $wpdb->get_row( $result, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have a match?
				$error_message .= "A person with that Person ID is already a member - contact Membership<br/>";
			}

			// check for RIN in genealogical database
			$irin = "P".$rin;  // reformat RIN for TNG file chg 180810
			$query = "SELECT personID FROM tng_people WHERE personID = %s";
			$result = $wpdb->prepare( $query, $irin );
			$rowdata = $wpdb->get_row( $result, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have a match?
				$success_message .= "OK. That Person ID is in our genealogical database and we can proceed<br/>";
			} else {
				$error_message .= "We don't have that Person ID in our genealogical database - contact Genealogist<br/>";
			}
		}
	
		if (empty($_POST["txtEmail"])) {
			$error_message .= "Email must not be empty<br/>";
		} else {
			// Email Validation
			$email = test_input($_POST["txtEmail"]);
			$error_message .= val_email( $email, "" );
		
			// check for email in member database
			$query = "SELECT COUNT(*), username FROM vfa_members WHERE m_email = '".$_POST["txtEmail"]."'";
			$rowcount = $wpdb->get_var( $query );
			if ($rowcount > 0) {  // do we have a match?
					$error_message .= "We have a member with the email ".$email." already - contact Membership.<br/>";
			}
		}

		// No errors, so store result
		// echo "no errors";
		if($error_message == "") {
			$query = "SELECT MAX(username) AS username FROM vfa_members WHERE applicant = 1";  // get highest APL number on file
			$rowdata = $wpdb->get_row( $query, ARRAY_A );
			$username = $rowdata['username'];
			$num_rows = $wpdb->num_rows;
			// echo "username1= ".$rowdata['username'];
			// echo "num rows=".$num_rows."<br/>";
			if (!empty($username)) {  // we have at least one applicant
				// $username = $rowdata['username'];
				// echo "username2=".$username."<br/>";
				$apl_num = filter_var($username, FILTER_SANITIZE_NUMBER_INT );  // get rid of APL
				// echo "apl_num=".$apl_num."<br/>";
				$apl_num = $apl_num + 1;  // increase by 1
				$apl_num = "APL".strval( $apl_num ); // convert back to username format
				// echo " APL= ".$apl_num ."<br/>";
			} else {  // no applicants currently on file
				$apl_num = "APL101"; // a beginning number
			}
			$m_status = "N";
			$s_status = "U";
			$tng_personID = $irin;
			$disname = $fname." ".$lname;
			$recv_news = "E";
			$m_enews = 1;
			$m_freq = "I";
			$data = array('username' => $apl_num, 'm_first_name' => $fname, 'm_last_name' => $lname, 'm_rin' => $rin, 'm_email' => $email, 'applicant' => 1, 'm_status' => $m_status, 's_status' => $s_status, 'tng_personID' => $tng_personID, 'm_display_name' => $disname, 'recv_news' => $recv_news, 'm_enews' => $m_enews, 'm_freq' => $m_freq);
			$format = array( '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s' );
			$result = $wpdb->insert( 'vfa_members', $data, $format );
			// echo $result;
			if($result == 1) {
				$error_message = "";
				$success_message = "You have registered successfully! Watch for a welcoming email; contact WebMaster if not received.";	
				// https://www.sitepoint.com/sending-emails-php-phpmailer/
				// https://github.com/PHPMailer/PHPMailer/wiki/SMTP-Debugging
				// now prepare and send email to applicant with copy to Genealogist and President
				include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 
				$mail = new PHPMailer;
				$mail->SMTPDebug = 0;                               
				$mail->isSMTP();            
				$mail->Host = $smtp_host;
				$mail->SMTPAuth = true;                          
				$mail->Username = $president_email;                 
				$mail->Password = $smtp_password;                           
				$mail->SMTPSecure = $smtp_secure;                           
				$mail->Port = $smtp_port;                                   
				$mail->From = $president_email;
				$mail->FromName = $smtp_from_name;
				$mail->addAddress($email, $fname." ".$lname);
				$mail->addCC($webmaster_email);
				$mail->addCC($genealogist_email);
				$mail->isHTML(true);
				$mail->Subject = "Your Violette Family Association application";
				$body = "<i>Thank you for applying for membership in the Violette Family Association!</i><br/><br/>";
				$body .= "Your application is on file and will be reviewd to see if any further information is needed.<br/><br/>";
				$body .= "When approved, you will receive an email with your VFA # and your web site login instructions. At that time ";
				$body .= "you will be able to complete your Member Profile.<br/><br/>";
				$body .= "In the meantime, feel free to browse our web site VioletteFamily.org to learn more about us.<br/><br/>";
				$body .= "Your temporary indentification number: ".$apl_num."<br/>";
				$body .= "Your name: ".$fname." ".$lname."<br/>";
				$body .= "Your Person ID: ".$rin."<br/><br/>";
				$body .="Contact me if any questions.";
				$mail->Body = $body;
				$mail->AltBody = $body;
				if(!$mail->send()) 
				{
					$error_message .= "Mailer Error: " . $mail->ErrorInfo;
				} 
				else 
				{
					$success_message = "Message has been sent successfully";
				}
			} else {
				$error_message .= "Problem in registration. Try Again!";	
				$success_message = "";	
			}
		}
	} elseif (!empty($_POST["btnFindRIN"])) {  // find RIN
		// echo "start find RIN<br/>";
		$memberlist = "<div>";
		$memberlist .= $tableheader;
		$memberlist .= $rowstart;
			$memberlist .= "<th width='20%' class='datahead'>Given name(s)</th>";
			$memberlist .= "<th width='20%' class='datahead'>Last name</th>";
			$memberlist .= "<th width='20%' class='datahead'>ID</th>";
			$memberlist .= "<th width='20%' class='datahead'>Birthdate</th>";
			$memberlist .= "<th width='20%' class='datahead'>Location</th>";
		$memberlist .= $rowend;
		
		$fname = $_POST['txtFirstName'];
		$lname = $_POST['txtLastName'];
		// echo $fname;
		$query = "SELECT firstname, lastname, personID, birthdate, birthplace FROM tng_people WHERE firstname LIKE '".$fname."%' && lastname = '".$lname."' ORDER BY firstname";
		// $result = $wpdb->prepare( $query );
		// $result = $wpdb->prepare( $query, array( $fname, $lname ) );
		$rowdata = $wpdb->get_results( $query, ARRAY_A );
		if ( is_array($rowdata) ) {  // do we have a match?
			foreach ( $rowdata as $person ) {
				$memberlist .= $rowstart;
					$memberlist .= "<td>".$person['firstname']."</td>";
					$memberlist .= "<td>".$person['lastname']."</td>";
					$memberlist .= "<td>".filter_var($person['personID'], FILTER_SANITIZE_NUMBER_INT )."</td>";
					$memberlist .= "<td>".$person['birthdate']."</td>";
					$memberlist .= "<td>".$person['birthplace']."</td>";
				$memberlist .= $rowend;
			}
		} else {
			$memberlist .= $rowstart;
			$memberlist .= "<td rowspan='5'>None found</td>";
			$memberlist .= $rowend;
		}  // num_rows
		$memberlist .= $tableender;
		$memberlist .= "</div>";
		// echo "end find RIN<br/>";
	}
	?>

	<div id="divApply">
		<?php if(!empty($success_message)) { ?>	
			<p>Complete all the fields below then click the Apply button to start your application. All fields are required.</p>
		<?php } ?>
			<?php if(!empty($success_message)) { ?>	
      	<div class="success-message"><?php if(isset($success_message)) echo $success_message; ?></div>
      <?php } ?>
      <?php if(!empty($error_message)) { ?>	
      	<div class="error-message"><?php if(isset($error_message)) echo $error_message; ?></div>
      <?php } ?>
		<table>
			<tbody>
				<tr>
					<td width="50%">
						<form method="POST" name="frmApplyForm">
							<fieldset id="fldApply" class="edit" visible="true">
								<legend style="font-size: 12px; background-color: inherit;">Application Info</legend>
								<table width="50%">
									<tr>
										<td style="text-align:right">
											<label for="txtFirstName">First name
											<input type="text" id="txtFirstName" name="txtFirstName" size="35" autofocus value="<?php if(isset($_POST['txtFirstName'])) echo $_POST['txtFirstName']; ?>"/></label>
										</td>
									</tr>
									<tr>
										<td style="text-align:right">
											<label for="txtLastName">Last name
											<input type="text" id="txtLastName" name="txtLastName" size="35" value="<?php if(isset($_POST['txtLastName'])) echo $_POST['txtLastName']; ?>"/></label>
										</td>
									</tr>
									<tr>
										<td style="text-align:right">
											<label for="txtRIN">Person ID
											<input type="text" id="txtRIN" name="txtRIN" size="10" value="<?php if(isset($_POST['txtRIN'])) echo $_POST['txtRIN']; ?>"/></label>
										</td>
									</tr>
									<tr>
										<td style="text-align:right">
											<label for="txtEmail">Email
											<input type="text" id="txtEmail" name="txtEmail" size="50" value="<?php if(isset($_POST['txtEmail'])) echo $_POST['txtEmail']; ?>"/></label>
										</td>
									</tr>
									<tr>
									<?php if(empty($success_message)) { ?>	
										<td style="text-align:center">
											<span style="padding-right:15px;"><input type='submit' name='btnFindRIN' value='Find My Person ID'/></span>
											<input type='submit' name='btnApply' value='Apply'/>
										</td>
									<?php } ?>
									</tr>
								</table>
							</fieldset>
						</form>
					</td>
					<td>
					<p>Membership in the Violette Family Association is open to anyone who is descendant of Francois Violet, even if your surname is not Violette. Spouses and children are also eligible for membership. You can see a brief history of François on our <b><a href="https://violetteregistry.com/">Welcome Page</a></b>.</p>
					<p>You will need your <b>Person ID (from Family Tree) or RIN (the numeric portion of the Person ID)</b> to apply for membership. If you do not know your Person ID, enter your first and last name and click the <b>Find My Person ID</b> button or go to <b><a href="https://violetteregistry.com/familytree/">Genealogy/FamilyTree</a></b> and do a search for your name. In either case, use your maiden name if you have one. Once you have your Person ID come back here to complete the application form. If you do not find your Person ID contact Peter R. Violette (VFA #1793), our Genealogist, and provide him info so he can add you to the family tree. His contact info is in the footer of this and every page.</p>
					<p>You will receive an email with your temporary application number. Your submitted application will be reviewed and you will be notified by email of your status. When approved, you will receive your actual VFA # - your membership number in the Association. The email notifiying you of approval will also include a link for you to add information to your Member Profile. You will be given a Username and Password at that time to use for future logins.</p>					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php echo $memberlist; ?>
	<div id='outcomeA'></div>
	<div id='outcomeB'></div>
<?php
}
add_shortcode('vfa-apply', 'vfa_apply');
?>
<?php
/* -------------------------------------------------------------------------
vfa_amimember() allows people to check if they are already a member of VFA
Created 180413 by DAV 
---------------------------------------------------------------------------*/
function vfa_amimember() {
	// global variables
	global $wpdb;

	// define variables and set to empty values
	$fnameErr = $lnameErr = $rinErr = $emailErr = $error_message = $success_message = $success = "";
	$fname = $lname = $rin = $email = $num_rows = "";

	if(!empty($_POST["btnCheck"])) {
		$error_message = $success_message = "";

		// check for blank fields
		if ( empty($_POST["txtRIN"]) && empty($_POST["txtEmail"]) ) {
			$error_message .= "You must enter either a Person ID or an email.<br/>";
		}

		// RIN Validation
		if (!empty($_POST["txtRIN"])) {

			$rin = test_input($_POST["txtRIN"]);
			$rin = filter_var($rin, FILTER_SANITIZE_NUMBER_INT );  // get rid of any prefix added
			if (is_nan($rin) || $rin < 1 || $rin > 90000) {
				$error_message .= "RIN should be a number lower than 90000<br/>";
			}
		
			// check for RIN already a member
			$query = "SELECT m_email, m_rin, m_vfa, username FROM vfa_members WHERE m_rin = %d";
			$result = $wpdb->prepare( $query, $rin );
			$rowdata = $wpdb->get_row( $result, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have a match?
					$success_message .= "A person with that RIN is a member<br/>";
					$success = "The username with that Person ID is ".$rowdata['username'];
			}	else {
					$error_message .= "No person with that Person ID is a member<br/>";
			}
		
			// check for RIN in genealogical database
			$irin = "P".$rin;  // reformat RIN for TNG file chg 180810
			$query = "SELECT personID FROM tng_people WHERE personID = %s";
			$result = $wpdb->prepare( $query, $irin );
			$rowdata = $wpdb->get_row( $result, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have a match?
				$success_message .= "That Person ID is in our genealogical database<br/>";
			} else {
				$error_message .= "We don't have that Person ID in our genealogical database - contact Genealogist<br/>";
			}
			
		} // RIN provided
	
		// Email Validation
		if (!empty($_POST["txtEmail"])) {
			// Email Validation
			if (!filter_var($_POST["txtEmail"], FILTER_VALIDATE_EMAIL)) {
				$error_message .= $email." is an invalid email address<br/>";
			} else {
				$email = $_POST["txtEmail"];
			}
		
			// check for email in member database
			$query = "SELECT COUNT(*), username FROM vfa_members WHERE m_email = '".$_POST["txtEmail"]."'";
			$rowcount = $wpdb->get_var( $query );
			if ($rowcount > 0) {  // do we have a match?
				if ($rowcount > 1) {
					$error_message .= "More than one person with the email ".$email." is a member<br/>";
				} elseif ($rowcount == 1) {
					$query = "SELECT username FROM vfa_members WHERE m_email = '".$_POST["txtEmail"]."'";
					$rowdata = $wpdb->get_row( $query, ARRAY_A );
					$success_message .= "A person with the email ".$email." is a member<br/>";
					$success = "The username with that email is ".$rowdata['username'];
				}
			} else {
				$error_message .= "No person with the email ".$email." is a member<br/>";
			}
		} // Email provided

	} // validate and check
	?>
	<div id="divCheck">
			<p>Complete any of the fields below then click the Check button to check on your status. All fields are not required.</p>
			<?php if(!empty($success_message)) { ?>	
      	<div class="success-message"><?php if(isset($success_message)) echo $success_message; ?></div>
      <?php } ?>
      <?php if(!empty($error_message)) { ?>	
      	<div class="error-message"><?php if(isset($error_message)) echo $error_message; ?></div>
      <?php } ?>
		<table>
			<tbody>
				<tr>
					<td width="50%">
						<form method="POST" name="frmCheckForm">
							<fieldset id="fldApply" class="edit" visible="true">
								<legend style="font-size: 12px; background-color: inherit;">Your Info</legend>
								<table width="50%">
									<tr>
										<td style="text-align:right">
											<label for="txtRIN">RIN
											<input type="text" id="txtRIN" name="txtRIN" size="10" value="<?php if(isset($_POST['txtRIN'])) echo $_POST['txtRIN']; ?>"/></label>
										</td>
									</tr>
									<tr>
										<td style="text-align:right">
											<label for="txtEmail">Email
											<input type="text" id="txtEmail" name="txtEmail" size="75" value="<?php if(isset($_POST['txtEmail'])) echo $_POST['txtEmail']; ?>"/></label>
										</td>
									</tr>
									<tr>
										<td style="text-align:center">
											<input type='submit' name='btnCheck' value='Check Status'/>
										</td>
									</tr>
									<?php if(!empty($success)) { ?>	
										<tr>
											<td style="text-align:center">
											<div class="success-message"><?php echo $success; ?></div>
											</td>
										</tr>
									<?php }?>
								</table>
							</fieldset>
						</form>
					</td>
					<td>
						<p>If you think you are already a Violette Family Association member but cannot remember your VFA #, we can check for you.</p>
						<p>Checking against your <b>Person ID (from Family Tree) or RIN (Genealogical Record Identification Number - the numeric part of the Person ID)</b> is the best way since that is a unique entry. If you do not know your RIN, go to <b>Genealogy, FamilyTree</b> and do a search for your name. If you do not find your info there contact Rod Violette (VFA #12), our Genealogist, and provide him info so he can add you to the family tree. His contact info is in the footer of this and every page.</p>
						<p>We can check against your email but there might be more than one member with your email if you have Child members registered.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id='outcomeA'></div>
	<div id='outcomeB'></div>
<?php
}
add_shortcode('vfa-amimember', 'vfa_amimember');
?>
<?php
/* -------------------------------------------------------------------------
vfa_addfamily() allows people to provide info about family members for addition to Registry
Created 180420 by DAV 
Mod 180812 by DAV to chg RIN to Person ID
Mod 210512 by DAV to align to column top
---------------------------------------------------------------------------*/
function vfa_addfamily() {
	// global variables
	global $wpdb, $current_user;
	$referrer = $current_user->user_login;

	// define variables and set to empty values
	$fnameErr = $lnameErr = $rinErr = $emailErr = $error_message = $success_message = $success = $notfound = "";
	$fname = $lname = $mname = $rin = $email = $dob = $pob = $ffather = $lfather = $mfather = $fmother = $mmother = $lmother = "";
	$frin = $mrin = $num_rows = "";

	if(!empty($_POST["btnCheckRIN"])) {
		$error_message = $success_message = $success = $notfound = "";

		// check for blank RIN field
		if ( empty($_POST["txtRIN"]) ) {
			$notfound = TRUE;
		}

		// RIN Validation
		if (!empty($_POST["txtRIN"])) {

			$rin = test_input($_POST["txtRIN"]);
			$rin = filter_var($rin, FILTER_SANITIZE_NUMBER_INT );  // get rid of any prefix added
			if (is_nan($rin) || $rin < 1 || $rin > 90000) {
				$error_message .= "RIN should be a number lower than 90000<br/>";
			}
		
			// check for RIN already a member
			$query = "SELECT m_email, m_rin, m_vfa, username FROM vfa_members WHERE m_rin = %d";
			$result = $wpdb->prepare( $query, $rin );
			$rowdata = $wpdb->get_row( $result, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have a match?
					$success_message .= "A person with that Person ID is already a member<br/>";
					$success = "The username with that Person ID is ".$rowdata['username'];
			}	else {
					// $error_message .= "No person with that Person ID is a member yet<br/>";
					$notfound = TRUE;
			}
		
			// check for RIN in genealogical database
			$irin = "P".$rin;  // reformat RIN for TNG file chg 180810
			$query = "SELECT personID, lastname, firstname, sex, birthdatetr, birthplace FROM tng_people WHERE personID = %s";
			$result = $wpdb->prepare( $query, $irin );
			$rowdata = $wpdb->get_row( $result, ARRAY_A );
			$num_rows = $wpdb->num_rows;
			if ($num_rows > 0) {  // do we have a match?
				$fname = $rowdata['firstname'];
				$lname = $rowdata['lastname'];
				$gender = $rowdata['sex'];
				$dob = $rowdata['birthdatetr'];
				$pob = $rowdata['birthplace'];
				$success_message .= "That Person ID is in our genealogical database<br/>";
				// echo $rowdata['birthdatetr'];
				// split first name into first and middle if space between
				if ( strpos($fname, " ") > 0 ) {
					$sub = explode( " ", $fname );
					$fname = $sub[0];
					$mname = $sub[1];
				}
		
			} else {
				$success_message .= "We'll have to add that person as a member'<br/>";
				$notfound = TRUE;
			}
			
			if($error_message == "") { 
				// store: we've got the info we need
				// create username ID
				$query = "SELECT MAX(username) AS username FROM vfa_added WHERE addition = 1";  // get highest ADD number on file
				$rowdata = $wpdb->get_row( $query, ARRAY_A );
				$username = $rowdata['username'];
				$num_rows = $wpdb->num_rows;
				if (!empty($username)) {  // we have at least one applicant
					$add_num = filter_var($username, FILTER_SANITIZE_NUMBER_INT );  // get rid of ADD
					$add_num = $add_num + 1;  // increase by 1
					$add_num = "ADD".strval( $add_num ); // convert back to username format
				} else {  // no applicants currently on file
					$add_num = "ADD101"; // a beginning number
				}
				// store added person data
				$tng_personID = $irin;
				$disname = $fname." ".$lname;
				$data = array('username' => $add_num, 'a_first_name' => $fname, 'a_middle_name' => $mname, 'a_last_name' => $lname, 'a_rin' => $rin, 
					'a_gender' => $gender, 'a_email' => $email, 'a_dob' => $dob, 'a_pob' => $pob, 'tng_personID' => $irin, 'a_display_name' => $disname, 
					'referrer' => $referrer, 'addition' => 1 
				);
				$success_message .= $add_num."<br/>";
				$format = array( '%s', '%s', '%s', '%s', '%d',
					'%s', '%s', '%s', '%s', '%s', '%s',
					'%s', '%d'
				 );
				$result = $wpdb->insert( 'vfa_added', $data, $format );
				if ( empty( $wpdb->last_error ) ) {
					$success_message .= "The person's info has been stored. <br/>";
				} else {
					$success_message .= $wpdb->last_error."<br/>";
				}

				$notfound = "";
			} // store

		} // RIN provided
	} // Check RIN

	if ( empty($_POST["btnSubmit"]) && empty($_POST["btnUpdate"]) ) { $_SESSION["user"] = NULL; }
	// echo "user1=".$_SESSION["user"]."<br/>";

	// no RIN given, use info provided
	if(!empty($_POST["btnSubmit"]) | !empty($_POST["btnUpdate"]) ) {
		// echo "user2=".$_SESSION["user"]."<br/>";
		$error_message = $success_message = "";
	
		// First Name Validation
		$fname = test_input($_POST["txtFirstName"]);
		$error_message .= val_name( $fname, "first" );

		// Middle Name Validation
		$mname = test_input($_POST["txtMiddleName"]);
		$error_message .= val_name( $mname, "middle" );
	
		// Last Name Validation
		$lname = test_input($_POST["txtLastName"]);
		$error_message .= val_name( $lname, "last" );

		$gender = $_POST["radGender"];
	
		// Place of Birth Validation
		$a_pob = test_input($_POST["txtPOB"]);
		$error_message .= val_place( $a_pob, "birthplace" );
		
		// Email Validation
		$email = test_input($_POST["txtEmail"]);
		$error_message .= val_email( $email, "" );
		
		// home phone validation
		$a_home_phone = $_POST["txtHomePhone"];
		$error_message .= val_phone( $a_home_phone, "home" );
		
		// cell phone validation
		$a_cell_phone = $_POST["txtCellPhone"];
		$error_message .= val_phone( $a_cell_phone, "cell" );
		
		// street address 1 Validation
		$a_addr1 = test_input($_POST["txtAddr1"]);
		$error_message .= val_street( $a_addr1, "street address 1" );
		
		// street address 2 Validation
		$a_addr2 = test_input($_POST["txtAddr2"]);
		$error_message .= val_street( $a_addr2, "street address 2" );
		
		// po address Validation
		$a_po_addr = test_input($_POST["txtPOAddr"]);
		$error_message .= val_pobox( $a_po_addr, "PO box" );
		
		// city Validation
		$a_city = test_input($_POST["txtCity"]);
		$error_message .= val_pobox( $a_city, "city" );
	
		// state province Validation
		$a_state = test_input($_POST["txtState"]);
		$error_message .= val_state( $a_state, "" );
		
		// country Validation
		$a_country = $_POST["txtCountry"];
		$error_message .= val_country( $a_country, "" );
		
		// postal code Validation
		$a_postal = $_POST["txtCode"];
		$error_message .= val_postalcode( $a_postal, $a_country );
		
		// Father First Name Validation
		$ffather = test_input($_POST["txtFFirstName"]);
		$error_message .= val_name( $ffather, "father's first" );
		
		// Father Last Name Validation
		$mfather = test_input($_POST["txtFMiddleName"]);
		$error_message .= val_name( $mfather, "father's middle" );
		
		// Father Last Name Validation
		$lfather = test_input($_POST["txtFLastName"]);
		$error_message .= val_name( $lfather, "father's last" );

		// Father RIN
		$frin = test_input($_POST["txtFRIN"]);
		$error_message .= val_rin( $frin, "Father's" );
	
		// Mother First Name Validation
		$fmother = test_input($_POST["txtMFirstName"]);
		$error_message .= val_name( $fmother, "mother's first" );
	
		// Mother Middle Name Validation
		$mmother = test_input($_POST["txtMMiddleName"]);
		$error_message .= val_name( $mmother, "mother's middle" );
	
		// Mother Last Name Validation
		$lmother = test_input($_POST["txtMLastName"]);
		$error_message .= val_name( $lmother, "mother's last" );

		$mrin = test_input($_POST["txtMRIN"]);
		$error_message .= val_rin( $mrin, "Mother's" );

		// end validations
		// start insert/update
		if(isset($_POST['btnSubmit'])) {
				// store: we've got the info we need
			// create username ID
			$query = "SELECT MAX(username) AS username FROM vfa_added WHERE addition = 1";  // get highest ADD number on file
			$rowdata = $wpdb->get_row( $query, ARRAY_A );
			$username = $rowdata['username'];
			$num_rows = $wpdb->num_rows;
			if (!empty($username)) {  // we have at least one applicant
				$add_num = filter_var($username, FILTER_SANITIZE_NUMBER_INT );  // get rid of ADD
				$add_num = $add_num + 1;  // increase by 1
				$add_num = "ADD".strval( $add_num ); // convert back to username format
			} else {  // no applicants currently on file
				$add_num = "ADD101"; // a beginning number
			}
			// store added person data
			// $tng_personID = $irin;
			$disname = $fname." ".$lname;
			$a_dob = $_POST["datDOB"];
			$phone_home = deformat_phone_number($a_home_phone);
			$phone_cell = deformat_phone_number($a_cell_phone);
			$postal_code = deformat_postal_code($a_postal);
			$data = array('username' => $add_num, 'a_first_name' => $fname, 'a_middle_name' => $mname, 'a_last_name' => $lname, 'a_rin' => $rin, 
				'a_gender' => $gender, 'a_email' => $email, 'a_dob' => $a_dob, 'a_pob' => $a_pob, 'a_display_name' => $disname, 
				'street_addr_1' => $a_addr1, 'street_addr_2' => $a_addr2, 'po_addr' => $a_po_addr, 'city' => $a_city, 
				'state_prov' => $a_state, 'postal_code' => $postal_code, 'country' => $a_country, 'home_phone' => $phone_home, 'cell_phone' => $phone_cell, 
				'f_first_name' => $ffather, 'f_middle_name' => $mfather, 'f_last_name' => $lfather, 'f_rin' => $frin,
				'm_first_name' => $fmother, 'm_middle_name' => $mmother, 'm_last_name' => $lmother, 'm_rin' => $mrin,
				'referrer' => $referrer, 'addition' => 1 
			);
			$format = array( '%s', '%s', '%s', '%s', '%d',
				'%s', '%s', '%s', '%s', '%s',
				'%s', '%s', '%s', '%s',
				'%s', '%s', '%s', '%s', '%s',
				'%s', '%s', '%s', '%d',
				'%s', '%s', '%s', '%d',
				'%s', '%d'
			);
			$result = $wpdb->insert( 'vfa_added', $data, $format );
			if ( empty( $wpdb->last_error ) ) {
				$success_message .= "The person's info has been stored. <br/>";
				$_SESSION["user"] = $add_num;
			} else {
				$error_message .= $wpdb->last_error."<br/>";
			}
		} else {
			// was stored already, so update
			// echo "user4=".$_SESSION["user"]."<br/>";
			$disname = $fname." ".$lname;
			$a_dob = $_POST["datDOB"];
			$phone_home = deformat_phone_number($a_home_phone);
			$phone_cell = deformat_phone_number($a_cell_phone);
			$postal_code = deformat_postal_code($a_postal);
			$table = 'vfa_added';
			$data = array('a_first_name' => $fname, 'a_middle_name' => $mname, 'a_last_name' => $lname, 'a_rin' => $rin, 
				'a_gender' => $gender, 'a_email' => $email, 'a_dob' => $a_dob, 'a_pob' => $a_pob, 'a_display_name' => $disname, 
				'street_addr_1' => $a_addr1, 'street_addr_2' => $a_addr2, 'po_addr' => $a_po_addr, 'city' => $a_city, 
				'state_prov' => $a_state, 'postal_code' => $postal_code, 'country' => $a_country, 'home_phone' => $phone_home, 'cell_phone' => $phone_cell, 
				'f_first_name' => $ffather, 'f_middle_name' => $mfather, 'f_last_name' => $lfather, 'f_rin' => $frin,
				'm_first_name' => $fmother, 'm_middle_name' => $mmother, 'm_last_name' => $lmother, 'm_rin' => $mrin
			);
			$where = array( 'username' => $_SESSION["user"] );
			$result = $wpdb->update($table, $data, $where);
			if ( empty( $wpdb->last_error ) ) {
				$success_message .= "The person's info has been updated. <br/>";
			} else {
				$error_message .= $wpdb->last_error."<br/>";
			}
		} // update
		$notfound = "x";
		// } // store

	} // More info

	?>
	<div id="divCheck">
		<p>Use this form to suggest a family member or Violette relative for addition to the Registry. Start by entering the Person ID for the person you are adding, if you know it. Leave it blank if you do not.</p>
		<?php if(!empty($success_message)) { ?>	
			<div class="success-message"><?php if(isset($success_message)) echo $success_message; ?></div>
		<?php } ?>
		<?php if(!empty($error_message)) { ?>	
			<div class="error-message"><?php if(isset($error_message)) echo $error_message; ?></div>
		<?php } ?>
		<table>
			<tbody>
				<tr>
					<td width="50%" style='vertical-align:top'>
						<form method="POST" name="frmAddForm">
							<fieldset id="fldAdd" class="edit" visible="true">
								<legend style="font-size: 12px; background-color: inherit;">Person's Info</legend>
							  <?php if(empty($notfound)) { ?>
                <table width="50%">
									<tr>
										<td style="text-align:right">
											<label for="txtRIN">Person ID
											<input type="text" id="txtRIN" name="txtRIN" size="10" value="<?php if(isset($_POST['txtRIN'])) echo $_POST['txtRIN']; ?>"/></label>
										</td>
									</tr>
									<tr>
										<td style="text-align:center">
											<input type='submit' name='btnCheckRIN' value='Check Person ID'/>
										</td>
									</tr>
                </table>
								<?php }?>
								<?php if(!empty($notfound)) { ?>
                <table width="50%">
                  <tr>
                    <td style="text-align:center">
                    <div class="success-message">We need more info. Provide as much of the following as you can.</div>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:center">
                      <?php if ($_SESSION["user"] == "") { ?>
                        <input type='submit' name='btnSubmit' value='Submit Info'/>
                      <?php } ?>
                      <?php if ($_SESSION["user"] != "") { ?>
                        <input type='submit' name='btnUpdate' value='Update Info'/>
                      <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtFirstName">Person's First Name
                      <input type="text" id="txtFirstName" name="txtFirstName" size="75" value="<?php if(isset($_POST['txtFirstName'])) echo $fname; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtMiddleName">Person's Middle Name
                      <input type="text" id="txtMiddleName" name="txtMiddleName" size="75" value="<?php if(isset($_POST['txtMiddleName'])) echo $mname; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtLastName">Person's Last or Maiden Name
                      <input type="text" id="txtLastName" name="txtLastName" size="75" value="<?php if(isset($_POST['txtLastName'])) echo $lname; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="radGender">Person's Gender
                      <input type="radio" name="radGender" value="M" checked>Male</>
                      <input type="radio" name="radGender" value="F">Female</>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="datDOB">Person's Date of Birth
                      <input type="date" id="datDOB" name="datDOB" size='9' value="<?php if(isset($_POST['datDOB'])) echo $_POST['datDOB']; ?>" /></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtPOB">Person's Place of Birth
                      <input type="text" id="txtPOB" name="txtPOB" size="75" value="<?php if(isset($_POST['txtPOB'])) echo $_POST['txtPOB']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtEmail">Person's Email
                      <input type="text" id="txtEmail" name="txtEmail" size="75" value="<?php if(isset($_POST['txtEmail'])) echo $_POST['txtEmail']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtHomePhone">Person's Home Phone
                      <input type="text" id="txtHomePhone" name="txtHomePhone" size="75" value="<?php if(isset($_POST['txtHomePhone'])) echo $_POST['txtHomePhone']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtCellPhone">Person's Cell Phone
                      <input type="text" id="txtCellPhone" name="txtCellPhone" size="75" value="<?php if(isset($_POST['txtCellPhone'])) echo $_POST['txtCellPhone']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtAddr1">Person's Street Address 1
                      <input type="text" id="txtAddr1" name="txtAddr1" size="75" value="<?php if(isset($_POST['txtAddr1'])) echo $_POST['txtAddr1']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtAddr2">Person's Street Address 2
                      <input type="text" id="txtAddr2" name="txtAddr2" size="75" value="<?php if(isset($_POST['txtAddr2'])) echo $_POST['txtAddr2']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtPOAddr">Person's PO Address 1
                      <input type="text" id="txtPOAddr" name="txtPOAddr" size="75" value="<?php if(isset($_POST['txtPOAddr'])) echo $_POST['txtPOAddr']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtCity">Person's City
                      <input type="text" id="txtCity" name="txtCity" size="75" value="<?php if(isset($_POST['txtCity'])) echo $_POST['txtCity']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtState">Person's State or Province
                      <input type="text" id="txtState" name="txtState" size="75" value="<?php if(isset($_POST['txtState'])) echo $_POST['txtState']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtCode">Person's Postal Code
                      <input type="text" id="txtCode" name="txtCode" size="75" value="<?php if(isset($_POST['txtCode'])) echo $_POST['txtCode']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtCountry">Person's Country
                      <input type="text" id="txtCountry" name="txtCountry" size="75" value="<?php if(isset($_POST['txtCountry'])) echo $_POST['txtCountry']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtFRIN">Father's Person ID
                      <input type="text" id="txtFRIN" name="txtFRIN" size="75" value="<?php if(isset($_POST['txtFRIN'])) echo $_POST['txtFRIN']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtFFirstName">Father's First Name
                      <input type="text" id="txtFFirstName" name="txtFFirstName" size="75" value="<?php if(isset($_POST['txtFFirstName'])) echo $_POST['txtFFirstName']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtFMiddleName">Father's Middle Name
                      <input type="text" id="txtFMiddleName" name="txtFMiddleName" size="75" value="<?php if(isset($_POST['txtFMiddleName'])) echo $_POST['txtFMiddleName']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtFLastName">Father's Last Name
                      <input type="text" id="txtFLastName" name="txtFLastName" size="75" value="<?php if(isset($_POST['txtFLastName'])) echo $_POST['txtFLastName']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtMRIN">Mother's Person ID
                      <input type="text" id="txtMRIN" name="txtMRIN" size="75" value="<?php if(isset($_POST['txtMRIN'])) echo $_POST['txtMRIN']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtMFirstName">Mother's First Name
                      <input type="text" id="txtMFirstName" name="txtMFirstName" size="75" value="<?php if(isset($_POST['txtMFirstName'])) echo $_POST['txtMFirstName']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtMMiddleName">Mother's Middle Name
                      <input type="text" id="txtMMiddleName" name="txtMMiddleName" size="75" value="<?php if(isset($_POST['txtMMiddleName'])) echo $_POST['txtMMiddleName']; ?>"/></label>
                    </td>
                  </tr>
                  <tr>
                    <td style="text-align:right">
                      <label for="txtMLastName">Mother's Last or Maiden Name
                      <input type="text" id="txtMLastName" name="txtMLastName" size="75" value="<?php if(isset($_POST['txtMLastName'])) echo $_POST['txtMLastName']; ?>"/></label>
                    </td>
                  </tr>
								</table>
               <?php }?>
							</fieldset>
						</form>
					</td>
					<td style='vertical-align:top'>
						<p>Start by entering a <b>Person ID (from our Family Tree) or RIN (the numeric part of the Person ID)</b> for the person if you know it, since that is the best way to find a unique entry.</p>
						<p>If you do not know your Person ID, go to <b>Genealogy, FamilyTree</b> and do a search for the name. If you do not find the person's info there leave the Person ID field blank and click the Check Person ID button. You will then be asked to provide more information about the person.</p>
						<p>The info you submit will be sent to Pete Violette, our Genealogist, and he will work with you to add the family member.</p>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id='outcomeA'></div>
	<div id='outcomeB'></div>
<?php
}
add_shortcode('vfa-addfamily', 'vfa_addfamily');
?>
<?php
/* -------------------------------------------------------------------------
Function custom_logout() 
Shortcode comes from Plugin URI: https://om4.com.au/plugins/log-out-shortcode/
Version: 1.0.5
Author: OM4
Edited/added 180419 by DAV 
---------------------------------------------------------------------------*/
function custom_logout() {
	$defaults = array(
		'text'     => __( 'Log out' ), // This is the default log out text from wp-includes/general-template.php
		'redirect' => '', // path/URL to redirect to after logging out
		'class'    => 'logout', // CSS Class(es) to use in link.
	);

	if ( ! is_user_logged_in() ) {
		return '';
	}

	$atts = shortcode_atts( $defaults, $atts );

	if ( 'logout_to_home' == $shortcode_name ) {
		$atts['redirect'] = 'home';
	} else if ( 'logout_to_current' == $shortcode_name ) {
		$atts['redirect'] = 'current';
	}

	if ( 'home' == $atts['redirect'] ) {
			$atts['redirect'] = home_url();
	} else if ( 'current' == $atts['redirect'] ) {
		$atts['redirect'] = get_the_permalink();
	}

	$class_html = '';
	if ( '' != $atts['class'] ) {
		// Multiple classes are separated by a space
		$classes = explode( ' ', $atts['class'] );
		$classes = array_map( 'sanitize_html_class', $classes );
		$class_html = ' class="' . implode( $classes, ' ' ) . '"';
	}
	return '<a href="' . esc_url( wp_logout_url( $atts['redirect'] ) ) . '"' . $class_html . '>'  . '<input type="button" name="btnLogout" value="Sign Out"/>' . '</a>';
	// return '<a href="' . esc_url( wp_logout_url( $atts['redirect'] ) ) . '"' . $class_html . '>'  . esc_html( $atts['text'] ) . '</a>';
}
	add_shortcode( 'custom-logout', 'custom_logout' );
	add_shortcode( 'custom-logout-to-current', 'custom_logout' );
	add_shortcode( 'custom-logout-to-home', 'custom_logout' );
?>
<?php
/* -------------------------------------------------------------------------
vfa_birthday_test() provides a routine to find who has a birthday today
Created 180708 by DAV 
---------------------------------------------------------------------------*/
function vfa_birthday_test() {
	$outcome .= vfa_birthdays();
	echo $outcome;
}
add_shortcode( 'vfa-birthday-test', 'vfa_birthday_test' );
?>
<?php
/* -------------------------------------------------------------------------
vfa_family_list_test() provides a routine to contact relatives of a missing member
Created 180902 by DAV 
---------------------------------------------------------------------------*/
function vfa_family_list_test() {
	// $person = "P24064";
	// $person = "P5968";
	$person = 2987;
	$outcome .= vfa_family_list( $person );
	echo $outcome;
}
add_shortcode( 'vfa-family-list-test', 'vfa_family_list_test' );
?>
<?php
/* -------------------------------------------------------------------------
vfa_child_classif_update() provides a routine to update Child to Member when reach 18
Created 180711 by DAV 
---------------------------------------------------------------------------*/
function vfa_child_classif_update() {
	$outcome .= vfa_child_update();
	echo $outcome;
}
add_shortcode( 'vfa-child-classif-update', 'vfa_child_classif_update' );
?>
<?php
/* -------------------------------------------------------------------------
mailchimp_process() reads from vfa_mc_subscribed, vfa_mc_unsubscribed, and vfa_mc_cleaned to update vfa_members
Created 180715 by DAV 
---------------------------------------------------------------------------*/
function vfa_mailchimp_process() {

	// echo "path is '" . __DIR__ . "'.<br/>";
	// echo "path and file name is '" . __FILE__ . "'.<br/>";
	// echo "plugin dir path is '". plugin_dir_path( __DIR__ ) . "'.<br/>";
	// echo "content dir is '". content_url("/files/VFA-Missing.csv") . "'.<br/>";

	global $wpdb;
	// vfa_mc_unsubscribed contains members that have unsubscribed at MailChimp in response to a mailing
	// remove the email from vfa_members and wp_users and add LEID and EUID to vfa_members
	// the email may be used for either a member or a spouse/partner so check for both
	// delete the record from vfa_mc_unsubscribed once done

	if(!empty($_POST["btnProcess"])) {
			$error_message = $success_message = "";

		$query = "SELECT EmailAddress, FirstName, LEID, EUID FROM vfa_mc_unsubscribed";
		echo "<br/>Starting Unsubscribed:<br/>";
		$result = $wpdb->get_results( $query, ARRAY_A );
		foreach ( $result AS $rowdata ) {
			$email = $rowdata['EmailAddress'];
			$first_name = $rowdata['FirstName'];

			// echo $email;
			$vfa = filter_var($first_name, FILTER_SANITIZE_NUMBER_INT);
			echo "<br/>VFA=".$vfa."  ";
			// update m_email, LEID, EUID and set recv_news to P print
			$outcome = $wpdb->update( 'vfa_members', array( 'm_email' => '', 'LEID' => $rowdata['LEID'], 'EUID' => $rowdata['EUID'], 'recv_news' => 'P' ), array( 'm_email' => $email ), array( '%s', '%d', '%s', '%s') );
			$outcome = $wpdb->update( 'vfa_members', array( 's_email' => ''), array( 's_email' => $email ), array( '%s') );

			// get member info from vfa_members
			$member = $wpdb->get_row( "SELECT username, m_vfa, m_first_name, m_last_name, m_email, m_status FROM vfa_members WHERE m_vfa = ".$vfa, ARRAY_A );
			// set recv_news to N none if member is deceased or M missing
			if ( $member['m_status'] == 'D' OR $member['m_status'] == 'M' ) {
				$outcome = $wpdb->update( 'vfa_members', array(  'recv_news' => 'N' ), array( 'm_email' => $email ), array( '%s' ) );
			}
			echo ", username= ".$member['username'];
			// try updating wp_users
			$user = get_user_by( 'login', $member['username'] );
			echo ", user_id=".$user->id;
			if ( $user ) {
				$user_info = get_userdata( $user->id );
				echo ", login= ".$user_info->user_login;
				$data = array(
					'ID' => $user->id,
					'user_login' => $user_info->user_login, 
					'user_pass' => $user_info->user_pass, 
					'user_nicename' => $user_info->user_nicename, 
					'user_email' => '',
					'first_name' => $user_info->first_name,
					'last_name' => $user_info->last_name,
					'display_name' => $user_info->display_name
				);
				$user_id = wp_update_user( $data ); 
				if ( ! is_wp_error( $user_id ) ) {
					echo ", User updated : ". $user_id;
				} else {
					$error_string = $user_id->get_error_message();
					echo ", error:".$error_string;
				}
				echo ", id from update:".$user_id;
			}
			$outcome = $wpdb->delete( 'vfa_mc_unsubscribed', array( 'EmailAddress' => $email ), array( '%s') );
		} // unsubscribed
		echo "<br/>Unsubscribes processed<br/>";

		// vfa_mc_cleaned contains members whose email addresses have had hard bounces so are no longer valid
		// remove the email from vfa_members and wp_users and add LEID and EUID to vfa_members
		// the email may be used for either a member or a spouse/partner so check for both
		// delete the record from vfa_mc_cleaned once done

		echo "<br/>Starting Cleaned:<br/>";
		$query = "SELECT EmailAddress, FirstName, LEID, EUID FROM vfa_mc_cleaned";
		$result = $wpdb->get_results( $query, ARRAY_A );
		foreach ( $result AS $rowdata ) {
			$email = $rowdata['EmailAddress'];
			$first_name = $rowdata['FirstName'];
			// echo $email;
			$vfa = filter_var($first_name, FILTER_SANITIZE_NUMBER_INT);
			echo "<br/>VFA=".$vfa."  ";
			// update m_email, LEID, EUID and set recv_news to P print
			$outcome = $wpdb->update( 'vfa_members', array( 'm_email' => '', 'LEID' => $rowdata['LEID'], 'EUID' => $rowdata['EUID'], 'recv_news' => 'P' ), array( 'm_email' => $email ), array( '%s', '%d', '%s', '%s') );
			$outcome = $wpdb->update( 'vfa_members', array( 's_email' => ''), array( 's_email' => $email ), array( '%s') );

			// get member info from vfa_members
			$member = $wpdb->get_row( "SELECT username, m_vfa, m_first_name, m_last_name, m_email, m_status FROM vfa_members WHERE m_vfa = ".$vfa, ARRAY_A );
			// set recv_news to N none if member is deceased or M missing
			if ( $member['m_status'] == 'D' OR $member['m_status'] == 'M' ) {
				$outcome = $wpdb->update( 'vfa_members', array(  'recv_news' => 'N' ), array( 'm_email' => $email ), array( '%s' ) );
			}
			echo ", username= ".$member['username'];
			// try updating wp_users
			$user = get_user_by( 'login', $member['username'] );
			echo ", user_id=".$user->id;
			if ( $user ) {
				$user_info = get_userdata( $user->id );
				echo ", login= ".$user_info->user_login;
				$data = array(
					'ID' => $user->id,
					'user_login' => $user_info->user_login, 
					'user_pass' => $user_info->user_pass, 
					'user_nicename' => $user_info->user_nicename, 
					'user_email' => '',
					'first_name' => $user_info->first_name,
					'last_name' => $user_info->last_name,
					'display_name' => $user_info->display_name
				);
				$user_id = wp_update_user( $data ); 
				if ( ! is_wp_error( $user_id ) ) {
					echo ", User updated : ". $user_id;
				} else {
					$error_string = $user_id->get_error_message();
					echo ", error:".$error_string;
				}
				echo ", id from update:".$user_id;
			}
			$outcome = $wpdb->delete( 'vfa_mc_cleaned', array( 'EmailAddress' => $email ), array( '%s') );
		} // cleaned
		echo "<br/>Cleaned processed<br/>";

		// vfa_mc_subscribed contains members whose data remains valid at MailChimp
		// update the LEID and EUID fields in vfa_members, no changes needed to wp_users
		// delete the record from vfa_mc_subscribed once done

		echo "<br/>Starting Subscribed:<br/>";
		$query = "SELECT EmailAddress, FirstName, LEID, EUID FROM vfa_mc_subscribed";
		$result = $wpdb->get_results( $query, ARRAY_A );
		foreach ( $result AS $rowdata ) {
			$email = $rowdata['EmailAddress'];
			$first_name = $rowdata['FirstName'];
			// echo $email;
			$vfa = filter_var($first_name, FILTER_SANITIZE_NUMBER_INT);
			echo "<br/>VFA=".$vfa.", LEID= ".$rowdata['LEID'].", EUID=".$rowdata['EUID'];
			// update LEID, EUID
			$outcome = $wpdb->update( 'vfa_members', array( 'LEID' => $rowdata['LEID'], 'EUID' => $rowdata['EUID'] ), array( 'm_email' => $email ), array( '%d', '%s') );
			// $outcome = $wpdb->update( 'vfa_members', array( 's_email' => ''), array( 's_email' => $email ), array( '%s') );

			// get member info from vfa_members
			$member = $wpdb->get_row( "SELECT username, m_vfa, m_first_name, m_last_name, m_email FROM vfa_members WHERE m_vfa = ".$vfa, ARRAY_A );
			// set recv_news to N none if member is deceased or M missing
			if ( $member['m_status'] == 'D' OR $member['m_status'] == 'M' ) {
				$outcome = $wpdb->update( 'vfa_members', array(  'recv_news' => 'N' ), array( 'm_email' => $email ), array( '%s' ) );
			}
			echo ", username= ".$member['username'];
			$outcome = $wpdb->delete( 'vfa_mc_subscribed', array( 'EmailAddress' => $email ), array( '%s') );
		} // subscribed
		echo "<br/>Subscribed processed<br/>";

	} // btnProcess
	?>
	<p>This page provides the ability to update the vfa_members database with the latest info from the MaiiChimp VFA eNewsletters list.</p>
	<p>Start by signing in to the MailChimp account and requesting an Export from that list. You will receive a ZIP file. Extract the files in the ZIP and you will get three CSV files with names including cleaned_members_export, unsubscribed_members_export, and subscribed_members_export. The latter two names are descriptive of the file content; "cleaned" means those email addresses can no longer be used.</p>
	<p>Open phpMyAdmin at the server and go to vfa_mc_cleaned and make sure there are no records. Use Operations, Truncate to clear if needed. Go to Import, select the cleaned_members_export CSV file and do an Import. Go to Browse and delete the first record, the one that has the column labels. Then repeat the process with vfa_mc_unsubscribed and vfa_mc_subscribed.</p>
	<p>Once that setup is completed click the Process button below to start the updating process.</p>
	<form id="frmProcess" method="POST" action="">
		<input type='submit' name='btnProcess' value='Process'/>
	</form>
<?php
}
add_shortcode( 'vfa-mailchimp-process', 'vfa_mailchimp_process' );
?>
<?php
/* -------------------------------------------------------------------------
vfa_mc_get() provides a routine to be get a subscriber from MailChimp
Created 180715 by DAV 
---------------------------------------------------------------------------*/
function vfa_mc_get() {

	session_start();
	// if(isset($_POST['submit'])){
			// $fname = $_POST['fname'];
			// $lname = $_POST['lname'];
			// $email = $_POST['email'];
			$fname = "David (VFA #621)";
			$lname = "Violette";
			$email = "David@Violette.com";
			if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL) === false){
					// MailChimp API credentials
					$apiKey = "6cf21a268ce7550556dbbc559e8b9fbc-us4";
					$listID = "5a9b003581";
					// $apiKey = $mc_vfa_api;
					// $listID = $mc_vfa_list_id;
					
					// MailChimp API URL
					$memberID = md5(strtolower($email));
					$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
					$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;
					
					// member information
					$json = json_encode([
							'email_address' => $email,
							'status'        => 'subscribed',
							'merge_fields'  => [
									'FNAME'     => $fname,
									'LNAME'     => $lname
							]
					]);
					
					// send a HTTP POST request with curl
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
					curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_TIMEOUT, 10);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
					$result = curl_exec($ch);
					$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					curl_close($ch);
					
					// store the status message based on response code
					if ($httpCode == 200) {
							// $_SESSION['msg'] = '<p style="color: #34A853">You have successfully subscribed to CodexWorld.</p>';
							echo '<p style="color: #34A853">You have successfully subscribed to CodexWorld.</p>';
					} else {
							switch ($httpCode) {
									case 214:
											$msg = 'You are already subscribed.';
											break;
									default:
											$msg = 'Some problem occurred, please try again.'.$httpCode;
											break;
							}
							// $_SESSION['msg'] = '<p style="color: #EA4335">'.$msg.'</p>';
							echo '<p style="color: #EA4335">'.$msg.'</p>';
					}
			} else {
					// $_SESSION['msg'] = '<p style="color: #EA4335">Please enter valid email address.</p>';
					echo '<p style="color: #EA4335">Please enter valid email address.</p>';
			}
	// }
	// redirect to homepage
	// header('location:index.php');
}
add_shortcode( 'vfa-mailchimp-get', 'vfa_mc_get' );
?>
<?php
function vfa_mc_test() {
	?>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script type='text/javascript'>
		// When the document loads do everything inside here ...
		jQuery(document).ready(function(){

			// executes whem btnAdded button clicked
			jQuery('#btnExecute').click(function() {
				var action = document.getElementById('action').value; 
				var email = document.getElementById('email').value; 
				var fname = document.getElementById('fname').value; 
				var lname = document.getElementById('lname').value; 
				var debug = document.getElementById('debug').value; 
				jQuery.ajax({
					type: "POST",
					data: {action: action, email: email, fname: fname, lname: lname, debug: debug},
					dataType: "html",
					url: "/wp-content/plugins/vfa-member-admin/vfa-mc-functions.php",
					success: function(outcome){ // if data is retrieved, store it in html
						jQuery("#outcome1").html( outcome ); // show the html inside outcome1 div
					},
					error: function(xhr){
            			alert("An error occured: " + xhr.status + " " + xhr.statusText);
					}
				}); //close jQuery.ajax
				return false;
			})  // execute

		})  // document ready
	</script>

	<form action="" method="post">
		<select id="action" name="action">
			<option value="subscribe">subscribe</option>
			<option value="unsubscribe">unsubscribe</option>
			<!-- <option value="addinterest">addinterest</option>
			<option value="reminterest">reminterest</option> -->
			<option value="changename">changename</option>
			<option value="checklist">checklist</option>
		</select>
		<input type="text" id="email" name="email" placeholder="email" />
		<input type="text" id="fname" name="fname" placeholder="fname" />
		<input type="text" id="lname" name="lname" placeholder="lname" />
		<!-- <input type="text" name="interest" placeholder="interest" /> -->
		<!-- <input type="text" name="listid" placeholder="list ID" /> -->
		<select id="debug" name="debug">
			<option value="0">false</option>
			<option value="1">true</option>
		</select>
		<input type="submit" id="btnExecute" name="btnExecute" value="Execute query" />
	</form>
	<div id='outcome1'>Outcome1=</div>
	<div id='outcome2'></div>

<?php
}
add_shortcode( 'vfa-mc-test', 'vfa_mc_test' );
?>
<?php
/* -------------------------------------------------------------------------
mem_members() used one time to correct email problem in vfa_members
Created 180716 by DAV 
---------------------------------------------------------------------------*/
function mem_members() {
	global $wpdb;

	$query_in = "SELECT username, m_vfa, m_email, s_email FROM vfa_mems ORDER BY m_vfa";
	$result = $wpdb->get_results( $query_in, ARRAY_A );
	foreach ( $result AS $rowdata ) {
		$m_email = $rowdata['m_email'];
		$s_email = $rowdata['s_email'];
		$outcome = $wpdb->update( 'vfa_members', array( 'm_email' => $m_email, 's_email' => $s_email ), array( 'username' => $rowdata['username'] ), array( '%s', '%s' ) );
	}

}
add_shortcode( 'mem-members', 'mem_members' );
?>
<?php
/* -------------------------------------------------------------------------
users_fix() used one time to correct email problem in wp_users
Created 180716 by DAV 
---------------------------------------------------------------------------*/
function users_fix() {
	global $wpdb;

	$query_in = "SELECT username, m_vfa, m_email FROM vfa_members ORDER BY m_vfa";
	$result = $wpdb->get_results( $query_in, ARRAY_A );
	foreach ( $result AS $rowdata ) {
		$m_email = $rowdata['m_email'];
		$user = get_user_by( 'login', $rowdata['username'] );
		echo "<br/>user_id=".$rowdata['username'];
		if ( $user ) {
			$user_info = get_userdata( $user->id );
			echo ", login= ".$user_info->user_login;
			$data = array(
				'ID' => $user->id,
				'user_login' => $user_info->user_login, 
				'user_pass' => $user_info->user_pass, 
				'user_nicename' => $user_info->user_nicename, 
				'user_email' => $m_email,
				'first_name' => $user_info->first_name,
				'last_name' => $user_info->last_name,
				'display_name' => $user_info->display_name
			);
			$user_id = wp_update_user( $data ); 
			if ( ! is_wp_error( $user_id ) ) {
				echo ", User updated : ". $user_id;
			} else {
				$error_string = $user_id->get_error_message();
				echo ", error:".$error_string;
			}
		}
	}

}
add_shortcode( 'users-fix', 'users_fix' );
?>
<?php
/* -------------------------------------------------------------------------
vfa_email_test() used for testing email sending
Created 180903 by DAV 
---------------------------------------------------------------------------*/
function vfa_email_test() {
	global $wpdb, $tableheader, $rowstart, $rowend, $tableender;
	global $president_email, $webmaster_email, $webmaster_password, $webmaster_from_name;
	global $noreply_email, $noreply_password, $noreply_from_name;
	global $smtp_host, $smtp_password, $smtp_secure, $smtp_port, $smtp_auth;

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
  $subject = "Email test";
  $message = "<p>The Violette Family Association has lost contact with the member below and hope you can help us. If you can provide current contact info, please fill in the contact data you have and reply to this email. You may also forward this to the person in question so they can provide their contact info themselves.</p>";
  $message .= $tableheader;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "Name";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "David Violette";
			$message .= "</td>";
		$message .= $rowend;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "Street address 1";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "";
			$message .= "</td>";
		$message .= $rowend;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "Street address 2";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "";
			$message .= "</td>";
		$message .= $rowend;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "PO address";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "";
			$message .= "</td>";
		$message .= $rowend;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "City";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "";
			$message .= "</td>";
		$message .= $rowend;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "State/Province";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "";
			$message .= "</td>";
		$message .= $rowend;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "Postal code";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "";
			$message .= "</td>";
		$message .= $rowend;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "Phone";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "";
			$message .= "</td>";
		$message .= $rowend;
		$message .= $rowstart;
			$message .= "<td width='30%'>";
				$message .= "Email";
			$message .= "</td>";
			$message .= "<td width='70%'>";
				$message .= "";
			$message .= "</td>";
		$message .= $rowend;
  $message .= $tableender;

	$mail->Subject = $subject;
	$mail->Body = $message;
	$mail->AltBody = $message;

  // now send
	$mail->addAddress("David@Violette.com", "David Violette");
	if(!$mail->send()) {
		$outcome = "Mailer Error: " . $mail->ErrorInfo;
	} else {
		$outcome = "Message has been sent successfully";
	}
	$mail->clearAddresses();
  // if ( $sms_contact ) {
  // 	$mail->addAddress($sms_contact, $member->first_name." ".$member->last_name);
  //   if(!$mail->send()) {
  //     $outcome = "Mailer Error: " . $mail->ErrorInfo;
  //   } else {
  //     $outcome = "Message has been sent successfully";
  //   }
  // 	$mail->clearAddresses();
  // }
  echo $outcome;
}  //vfa_email_test
add_shortcode( 'vfa-email-test', 'vfa_email_test' );
?>
<?php
/* -------------------------------------------------------------------------
cron_test() provides a routine to be called from cron for testing
Created 180708 by DAV 
---------------------------------------------------------------------------*/
function cron_test() {
	$outcome .= cron_check();
	echo $outcome;
}
add_shortcode( 'cron-test', 'cron_test' );
?>
<?php
/* -------------------------------------------------------------------------
Function member_database was only used ONE TIME to merge vfa_contact into vfa_members, which replaces vfa_personal and vfa_contact
Created 180328 by DAV 
Used again on 180707 to merge state_prov into vfa_members by DAV
Run from admin, page = Member Database
---------------------------------------------------------------------------*/
function member_database() {
	global $username,	$host, $database,	$user, $password, $tableheader, $rowstart, $rowend, $tableender, $wpdb;
	$cxn = mysqli_connect($host, $user, $password, $database);
	if ($cxn->connect_error) {
		die ('Error : ('. $cxn->connect_errno .') '. $cxn->connect_error);
	}
	$query = "SELECT username, state_prov FROM vfa_mems";
	$result = mysqli_query($cxn, $query);
	echo mysqli_num_rows($result);
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$table = 'vfa_members';
			$data = array('state_prov' => $row['state_prov'] );
			// $data = array('street_addr_1' => $row['street_addr_1'], 'street_addr_2' => $row['street_addr_2'], 'po_address' => $row['po_address'], 'city' => $row['city'], 'postal_code' => $row['postal_code'], 'country' => $row['country'], 'phone_home' => $row['phone_home'], 'm_cellphone' => $row['m_cellphone'], 's_cellphone' => $row['s_cellphone'] );
			$where = array( 'username' => $row['username'] );
			$update = $wpdb->update($table, $data, $where);
			echo $row['username'];
		}
	}
	mysqli_close($cxn);
}
add_shortcode('member-database', 'member_database');


/* -------------------------------------------------------------------------
Filters for Fluent Forms
Created 230204 by MAA 
---------------------------------------------------------------------------*/

add_filter('fluentform_insert_response_data', 'vfa_custom_response_data_filter_function', 10, 3);

function vfa_custom_response_data_filter_function($formData, $formId, $inputConfigs) {

if($formId == 3) {
	$message = "";

	$formResponse = '';
	$rin = $formData['vfa_rin'];
	$email = $formData['vfa_email'];

	// check for RIN already a member
	// $memberid = GetMemberIDFromRIN($rin);

	$memberID =  GetMemberIDFromRIN( $rin );

	vfaLog("Member ID = " . $memberID);

	if ( $memberID != NULL) {
		$message .= "A person with that RIN is member " . $memberID;
	}
	else {
		$message .= "No person with that Person ID is a member";

	}

	// Update form response to user
	$formData['vfa_is_member_response'] = $message;	 
	return $formData;
 
   }



    return $formData;
}

/* -------------------------------------------------------------------------
Utility Functions for code added in 2023
Created 230204 by MAA 
---------------------------------------------------------------------------*/

// Log to VFA log (for debugging mostly)
function vfaLog($message) {
	$myfile = fopen(ABSPATH . "vfalog.txt", "a");
	fwrite($myfile, $message . "\n");
	fclose($myfile);
}

// check if that TNG person ID is associated with a member
function GetMemberIDFromRIN($rin){
    global $wpdb;

	$rinClean = preg_replace('/[^0-9]/', '', $rin);

	vfaLog("Getting member ID from " . $rinClean);

    $memberResults = $wpdb->get_results($wpdb->prepare("SELECT m_email, m_rin, m_vfa, username FROM vfa_members WHERE m_rin = %d", $rinClean));

    if ($wpdb->num_rows){
        return($memberResults[0]->username);
    }
    else {
        return NULL;
    }

}

/* -------------------------------------------------------------------------
Test shortcode - Runs through all of the WordPress users to verify that it 
corresponds to a member and, if so, a living member.
Added 2-18-23
---------------------------------------------------------------------------*/
function check_user_accounts_fn() {
	global $wpdb;
	
	$htmlOutput = '' ;

	// get all of the users from the wp_users table
    $userResults = $wpdb->get_results($wpdb->prepare("SELECT `ID`, `user_login`, `user_nicename`, `user_email` FROM `wp_users`"));
	foreach ($userResults as $user) {
		// is this user in the member table, check if they seem to need the account
		$memberResults = $wpdb->get_results($wpdb->prepare("SELECT `username`, `m_first_name`, `m_middle_name`, `m_last_name`, `m_maiden_name`, `m_email`, `m_status` FROM `vfa_members` WHERE `username` = %s", $user->user_login));
		if ($wpdb->num_rows){
			if ($memberResults[0]->m_status == 'D'){
				$htmlOutput .= "<p>User " . $user->user_login . " is marked as deceased</p>";
			}
			else if ($memberResults[0]->m_status == 'M') {
				$htmlOutput .= "<p>User " . $user->user_login . " is marked as missing</p>";
			}
		}
		else {
			$htmlOutput .= "<p>Did not find member that corresponds with user " . $user->user_login . "</p>";
		}


	}

	return($htmlOutput);

}
add_shortcode('vfa_check_wp_users', 'check_user_accounts_fn');


?>