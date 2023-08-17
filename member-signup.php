<?php

/* -------------------------------------------------------------------------
Global variables for all functions in this file
Created 180512 by DAV 
---------------------------------------------------------------------------*/
// Global variables
// require_once( ABSPATH . '/wp-includes/pluggable.php' );
// $current_user = wp_get_current_user();
// $username = $current_user->user_login;
$host = "localhost";
$user = "o3i4q5u6_wrdp5";
$password = "Ek[=xEL-@xQx";
$database = "o3i4q5u6_wrdp5";
$tableheader = "<table class='datatable'><tbody>";
$rowstart = "<tr>";
$rowend = "</tr>";
$tableender = "</tbody></table>";
$page_position = 0;
$items_per_page = 20;
include_once("functions.php");
// $scriptPath = dirname(__FILE__);
// $path = realpath($scriptPath . '/./');
// $filepath = explode("wp-content",$path);
// // print_r($filepath);
// define('WP_USE_THEMES', false);
// require(''.$filepath[0].'/wp-blog-header.php');
// require_once("../../../wp-blog-header.php");

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

  // build the list of members to process
  // when not doing a search limit output to 20 records, otherwise output all
  $list = explode(',', $_POST['list'] );
  $list_length = count($list);
  $blank = "";
  echo $list_length;
  // start the table output and process the list
  echo $tableheader;
  // echo $btn."<br/>";
  echo $rowstart;
    echo "<th width='5%' class='datahead'>VFA</th>";
    echo "<th width='20%' class='datahead'>Name</th>";
    echo "<th width='35%' class='datahead'>Email</th>";
    echo "<th width='40%' class='datahead'>Outcome</th>";
    echo $rowend;

  // get the records to display, if any
  for($x = 0; $x < $list_length; $x++) {
    echo $list[$x];
    // echo "<br>";
    $query = "SELECT username, m_vfa, m_rin, m_first_name, m_last_name, m_email, m_display_name FROM vfa_members WHERE m_vfa=".$list[$x];
    $result = $cxn->prepare( $query );
    $result->execute();
    $result->store_result();
    $num_rows = $result->num_rows();
    if ($num_rows > 0) {  // do we have any records?
      $result->bind_result($usernm, $vfa, $rin, $fname, $lname, $email, $dname); //bind variables to selected fields
      while ($result->fetch()) {
        $outcome = "found, ";
        if ( empty( $email ) ) {
          $outcome .= "no email";
        } else {
          // generate Password
          $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
          $password = substr( str_shuffle( $chars ), 0, 8 );

          // check if already a user
          // $user_id = get_user_by( 'user_login', $username );

          // try adding to wp_users
          $m_display_name = $fname." ".$lname;
          $data = array(
            'user_login' => $username, 
            'user_pass' => $password, 
            'user_nicename' => $fname.$lname, 
            'user_email' => $email,
            'first_name' => $fname,
            'last_name' => $lname,
            'display_name' => $m_display_name
          );
          // if ( $user_id ) {
          //   $user_id = wp_insert_user( $user_id, $data ); // will update user
          // } else {
            $user_id = wp_insert_user( $data ); // will insert user
          // }
          // $outcome .= adm_send_signup( $usernm, $vfa, $rin, $fname, $lname, $email );
        }
        echo $rowstart;
          echo "<td>". $vfa."</a></td>";
          echo "<td>". $dname ."</td>";
          echo "<td>". $email ."</td>";
          echo "<td>". $outcome ."</td>";
        echo $rowend;
      }
    } else {  // no, report no records
      $outcome = "not found";
      echo $rowstart;
        echo "<td>". $list[$x] ."</a></td>";
        echo "<td>". $blank ."</td>";
        echo "<td>". $blank ."</td>";
        echo "<td>". $outcome ."</td>";
      echo $rowend;
    }
  } // for
  echo $tableender;
  $result->close();
  $cxn->close();
  exit;
  ?>
