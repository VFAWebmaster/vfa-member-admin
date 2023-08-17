<?php

/* -------------------------------------------------------------------------
Global variables for all functions in this file
Created 180315 by DAV 
---------------------------------------------------------------------------*/
// Global variables
// require_once( ABSPATH . '/wp-includes/pluggable.php' );
// $current_user = wp_get_current_user();
// $username = $current_user->user_login;
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
  // echo $btn."<br/>";
  switch ($btn) {
    case "FirstName":
      $where = "WHERE m_first_name LIKE '%". $_POST['txt']."%'";
      $range = "";
      break;
    case "LastName":
      $where = "WHERE m_last_name LIKE '%". $_POST['txt']."%'";
      $range = "";
      break;
    case "VFA":
      $where = "WHERE m_vfa =". $_POST['txt'];
      $range = "";
      break;
    case "PID":
      $where = "WHERE m_rin =". $_POST['txt'];
      $range = "";
      break;
    case "City":
      $where = "WHERE city LIKE '%". $_POST['txt']."%'";
      $range = "";
      break;
    case "State":
      $where = "WHERE state_prov LIKE '%". $_POST['txt']."%'";
      $range = "";
      break;
    case "Email":
      $where = "WHERE m_email LIKE '%". $_POST['txt']."%'";
      $range = "";
      break;
    case "Clear":
      $where = "";
      $range = "LIMIT $page_position, $items_per_page";
      break;
    case "Applicants":
      $where = "WHERE applicant = 1";
      $range = "";
      break;
    case "Added":
      $where = "WHERE addition = 1";
      $range = "";
      break;
    default:
      $where = "";
      $range = "LIMIT $page_position, $items_per_page";
  }

  // start the table output
  echo $tableheader;
  switch ($btn) {
    case "Added":
      // echo $btn."<br/>";
      echo $rowstart;
        echo "<th width='5%' class='datahead'>User</th>";
        echo "<th width='3%' class='datahead'>ID</th>";
        echo "<th width='2%' class='datahead'>Gn</th>";
        echo "<th width='45%' class='datahead'>Name</th>";
        echo "<th width='45%' class='datahead'>Email</th>";
      echo $rowend;

      // get the records to display, if any
      $query = "SELECT username, a_rin, a_gender, a_display_name, a_email 
        FROM vfa_added ".$where." ORDER BY username DESC";
      // echo $query."</br>";
      $result = $cxn->prepare( $query );
      $result->execute();
      $result->store_result();
      $num_rows = $result->num_rows();
      if ($num_rows > 0) {  // do we have any records?
        $result->bind_result($usernm, $rin, $gender, $display_name, $email); //bind variables to selected fields
        while ($result->fetch()) {
          echo $rowstart;
            echo "<td><a href='../added-edit?mbr=".$usernm."'>".$usernm."</a></td>";
            echo "<td>". $rin ."</td>";
            echo "<td>". $gender ."</td>";
            echo "<td>". $display_name ."</td>";
            echo "<td>". $email ."</td>";
          echo $rowend;
        }
      } else {  // no, report no records
        echo $rowstart;
          echo "<td colspan='5'>No records found</td>";
        echo $rowend;
      }
      break;
      
    default:
      // echo $btn."<br/>";
      echo $rowstart;
        echo "<th width='5%' class='datahead'>User</th>";
        echo "<th width='3%' class='datahead'>VFA</th>";
        echo "<th width='3%' class='datahead'>ID</th>";
        echo "<th width='2%' class='datahead'>Cl</th>";
        echo "<th width='2%' class='datahead'>St</th>";
        echo "<th width='2%' class='datahead'>Gn</th>";
        echo "<th width='15%' class='datahead'>Name</th>";
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
            echo "<td>". $vfa ."</td>";
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
          echo "<td colspan='12'>No records found</td>";
        echo $rowend;
      } // if
      break;
    } // switch
  echo $tableender;
  $result->close();
  $cxn->close();
  exit;
?>
