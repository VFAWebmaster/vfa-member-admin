<?php

/* -------------------------------------------------------------------------
Global variables for all functions in this file
Created 180712 by DAV 
---------------------------------------------------------------------------*/
// Global variables
// require_once( ABSPATH . '/wp-includes/pluggable.php' );
// $current_user = wp_get_current_user();
// $username = $current_user->user_login;
include_once("functions.php");
$host = "localhost";
$user = "dmj7jvgp_wrdp";
$password = "Ek[=xEL-@xQx";
$database = "dmj7jvgp_wrdp";
$tableheader = "<table class='datatable'><tbody>";
$rowstart = "<tr>";
$rowend = "</tr>";
$tableender = "</tbody></table>";
$page_position = 0;
$items_per_page = 20;

global $wpdb, $host, $database, $user, $password, $tableheader, $rowstart, $rowend, $tableender;

  // make the connection to the database
  $cxn = new mysqli($host, $user, $password, $database);
  if ($cxn->connect_error) {
    die ('Error : ('. $cxn->connect_errno .') '. $cxn->connect_error);
  }
  // some testing code - can be commented out
  // $btn = $_POST['btn'];
  // $search = $_POST['txt'];
  // echo "search=".$search. "</br>";
  // var_dump($_POST);
  // echo "</br>";

  // clear any previous session mbr
  unset($_SESSION['mbr']);

  // build the WHERE and LIMITs based on which button was clicked
  // when not doing a search limit output to 20 records, otherwise output all
  $btn = $_POST['btn'];
  // echo "btn: ".$btn."<br/>";
  switch ($btn) {
    case "Members":
      $where = "";
      $range = "";
      $filename = "../../files/VFA-Members.csv";
      break;
    case "Missing":
      $where = "WHERE m_status = 'M'";
      $range = "";
      $filename = "../../files/VFA-Missing.csv";
      break;
    case "Child":
      $where = "WHERE m_classif = 'C'";
      $range = "";
      $filename = "../../files/VFA-Child.csv";
      break;
    case "Associates":
      $where = "WHERE m_classif = 'A'";
      $range = "";
      $filename = "../../files/VFA-Associates.csv";
      break;
    case "MailUSA":
      $where = "WHERE recv_news = 'P' AND country = 'USA'";
      $range = "";
      $filename = "../../files/VFA-Mail-USA.csv";
      break;
    case "MailCAN":
      $where = "WHERE recv_news = 'P' AND country = 'CAN'";
      $range = "";
      $filename = "../../files/VFA-Mail-CAN.csv";
      break;
    case "MailChimp":
      $where = "WHERE recv_news = 'E' AND m_status = 'A'";
      $range = "";
      $filename = "../../files/VFA-MailChimp.csv";
      break;
    case "Active":
      $where = "WHERE m_status = 'A'";
      $range = "";
      $filename = "../../files/VFA-Active.csv";
      break;
    default:
      $where = "";
      $range = "LIMIT $page_position, $items_per_page";
  }

  // start the table output
  echo $tableheader;
    // echo $btn."<br/>";
    switch ($btn) {
      case "MailChimp":
        echo $rowstart;
          echo "<th width='15%' class='datahead'>EmailAddress</th>";
          echo "<th width='10%' class='datahead'>FirstName</th>";
          echo "<th width='15%' class='datahead'>LastName</th>";
          echo "<th width='10%' class='datahead'>VFA</th>";
          echo "<th width='10%' class='datahead'>Username</th>";
          echo "<th width='10%' class='datahead'>EmailPreference</th>";
          echo "<th width='10%' class='datahead'>EMAIL_TYPE</th>";
          echo "<th width='10%' class='datahead'>LEID</th>";
          echo "<th width='10%' class='datahead'>EUID</th>";
        echo $rowend;

        // get the records to display, if any
        $query = "SELECT m_email, m_first_name, m_last_name, m_vfa, username, m_freq, m_type, LEID, EUID 
          FROM vfa_members ".$where." ORDER BY m_email";
        $result = $cxn->prepare( $query );
        $result->execute();
        $result->store_result();
        $num_rows = $result->num_rows();
        if ($num_rows > 0) {  // do we have any records?
          $result->bind_result($email, $firstname, $lastname, $vfa, $username, $freq, $type, $LEID, $EUID); //bind variables to selected fields
          while ($result->fetch()) {
            echo $rowstart;
              echo "<td>". $email ."</td>";
              echo "<td>". $firstname ."</td>";
              echo "<td>". $lastname ."</td>";
              echo "<td>". $vfa ."</td>";
              echo "<td>". $username ."</td>";
              echo "<td>". $freq ."</td>";
              echo "<td>". $type ."</td>";
              echo "<td>". $LEID ."</td>";
              echo "<td>". $EUID ."</td>";
            echo $rowend;
          }
        } else {  // no, report no records
          echo $rowstart;
            echo "<td colspan='9'>No records found</td>";
          echo $rowend;
        }
        break;

      case "MailUSA" :
      case "MailCAN" :
        echo $rowstart;
          echo "<th width='15%' class='datahead'>Name</th>";
          echo "<th width='20%' class='datahead'>Address1</th>";
          echo "<th width='15%' class='datahead'>Address2</th>";
          echo "<th width='15%' class='datahead'>POAddress</th>";
          echo "<th width='25%' class='datahead'>City</th>";
          echo "<th width='5%' class='datahead'>State</th>";
          echo "<th width='5%' class='datahead'>PostalCode</th>";
        echo $rowend;

        // get the records to display, if any
        $query = "SELECT m_display_name, street_addr_1, street_addr_2, po_address, city, state_prov, postal_code 
          FROM vfa_members ".$where." ORDER BY postal_code";
        $result = $cxn->prepare( $query );
        $result->execute();
        $result->store_result();
        $num_rows = $result->num_rows();
        if ($num_rows > 0) {  // do we have any records?
          $result->bind_result($name, $street1, $street2, $pobox, $city, $state, $zip); //bind variables to selected fields
          while ($result->fetch()) {
            echo $rowstart;
              echo "<td>". $name ."</td>";
              echo "<td>". $street1 ."</td>";
              echo "<td>". $street2 ."</td>";
              echo "<td>". $pobox ."</td>";
              echo "<td>". $city ."</td>";
              echo "<td>". $state ."</td>";
              echo "<td>". $zip ."</td>";
            echo $rowend;
          }
        } else {  // no, report no records
          echo $rowstart;
            echo "<td colspan='7'>No records found</td>";
          echo $rowend;
        }
        break;
        
      default :
        // echo $btn."<br/>";
        echo $rowstart;
          echo "<th width='5%' class='datahead'>User</th>";
          echo "<th width='3%' class='datahead'>RIN</th>";
          echo "<th width='2%' class='datahead'>Cl</th>";
          echo "<th width='2%' class='datahead'>St</th>";
          echo "<th width='2%' class='datahead'>Gn</th>";
          echo "<th width='18%' class='datahead'>Name</th>";
          echo "<th width='2%' class='datahead'>St</th>";
          echo "<th width='15%' class='datahead'>Spouse Name</th>";
          echo "<th width='20%' class='datahead'>City</th>";
          echo "<th width='3%' class='datahead'>St</th>";
          echo "<th width='33%' class='datahead'>Email</th>";
        echo $rowend;

        // get the records to display, if any
        $query = "SELECT username, m_vfa, m_rin, m_classif, m_status, m_gender, 
          m_first_name, m_last_name, m_display_name, s_rin, s_status, s_first_name, city, state_prov,
          m_email 
          FROM vfa_members ".
          $where 
          ." ORDER BY m_vfa DESC ".
          $range; 
        // echo $query."</br>";
        $result = $cxn->prepare( $query );
        $result->execute();
        $result->store_result();
        $num_rows = $result->num_rows();
        if ($num_rows > 0) {  // do we have any records?
          $result->bind_result($usernm, $vfa, $rin, $classif, $status, $gender, $firstname, $lastname, $display_name, $srin, $sstatus, $sfirstname, $city, $state_prov, $email); //bind variables to selected fields
          while ($result->fetch()) {
            echo $rowstart;
              echo "<td><a href='../member-edit?mbr=".$usernm."'>".$usernm."</a></td>";
              // echo "<td>". $vfa ."</td>";
              echo "<td>". $rin ."</td>";
              echo "<td>". $classif ."</td>";
              echo "<td>". $status ."</td>";
              echo "<td>". $gender ."</td>";
              echo "<td>". $display_name ."</td>";
              echo "<td>". $sstatus ."</td>";
              echo "<td>". $sfirstname ."</td>";
              echo "<td>". $city ."</td>";
              echo "<td>". $state_prov ."</td>";
              echo "<td>". $email ."</td>";
            echo $rowend;
          }
        } else {  // no, report no records
          echo $rowstart;
            echo "<td colspan='11'>No records found</td>";
          echo $rowend;
        } // if
        break;
    } // switch
  echo $tableender;
  if ( $btn == 'Members' ) $query = "SELECT * FROM vfa_members ORDER BY m_vfa";
  if ( $btn == 'MailChimp' ) $query = "SELECT m_email, m_first_name, m_last_name, m_vfa, username, m_freq, m_type, LEID, EUID FROM vfa_members ".$where. "ORDER BY m_vfa";
  if ( $btn == 'MailUSA' ) $query = "SELECT CONCAT(m_display_name, ' (', username, ')') AS mbr_name, street_addr_1, street_addr_2, po_address, city, state_prov, postal_code FROM vfa_members ".$where." ORDER BY postal_code";
  if ( $btn == 'MailCAN' ) $query = "SELECT CONCAT(m_display_name, ' (', username, ')') AS mbr_name, street_addr_1, street_addr_2, po_address, city, state_prov, postal_code FROM vfa_members ".$where." ORDER BY postal_code";
  $attachment = TRUE;
	$headers = FALSE;
	query_to_csv($query, $filename, FALSE);
  $result->close();
  $cxn->close();
  exit;
?>
