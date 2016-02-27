<?php require_once('../Connections/PGC.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

error_reporting(0);
if (!isset($_SESSION)) {
  session_start();
}
require_once('pgc_check_login.php'); 
$currentPage = $_SERVER["PHP_SELF"];
?>
<?php function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
$entry_ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
       $entry_ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
       $entry_ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
       $entry_ip=$_SERVER['REMOTE_ADDR'];
    }
	$_POST['entry_ip'] = $entry_ip;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {

 
 $updateSQL = sprintf("UPDATE pgc_flightlog_lastpilot SET `Date`=%s, `LastPilot`=%s, `TowPlane`=%s",
                       GetSQLValueString($_SESSION['$Logdate'], "text"),
                       GetSQLValueString($_POST['Tow_Pilot'], "text"),
					    GetSQLValueString($_POST['Tow_Plane'], "text"));

  mysql_select_db($database_PGC, $PGC);
  $Result2 = mysql_query($updateSQL, $PGC) or die(mysql_error());
  
  
 $updateSQL = sprintf("UPDATE pgc_flightsheet SET `Date`=%s, Glider=%s, Flight_Type=%s, Pilot1=%s, Pilot2=%s, Takeoff=%s, Landing=%s, `Tow Altitude`=%s, `Tow Plane`=%s, `Tow Pilot`=%s, `Tow Charge`=%s, Notes=%s ,`ip`=%s WHERE `Key`=%s",
                       GetSQLValueString($_SESSION['$Logdate'], "text"),
                       GetSQLValueString($_POST['Glider'], "text"),
					   GetSQLValueString($_POST['Flight_Type'], "text"),					   
                       GetSQLValueString($_POST['Pilot1'], "text"),
                       GetSQLValueString($_POST['Pilot2'], "text"),
                       GetSQLValueString($_POST['Takeoff'], "date"),
                       GetSQLValueString($_POST['Landing'], "date"),
                       GetSQLValueString($_POST['Tow_Altitude'], "text"),
                       GetSQLValueString($_POST['Tow_Plane'], "text"),
                       GetSQLValueString($_POST['Tow_Pilot'], "text"),
                       GetSQLValueString($_POST['Tow_Charge'], "double"),
                       GetSQLValueString($_POST['Notes'], "text"),
					   GetSQLValueString($_POST['entry_ip'], "text"),
                       GetSQLValueString($_POST['Key'], "int"));

  mysql_select_db($database_PGC, $PGC);
  $Result1 = mysql_query($updateSQL, $PGC) or die(mysql_error());

  /*== INSERT AUDIT RECORD == 
$_POST[entry_system] = 'History';
$insertSQL = sprintf("INSERT INTO pgc_flightsheet_audit (`Date`, Glider, Flight_Type, Pilot1, Pilot2, Takeoff, Landing, `Tow Altitude`, `Tow Plane`, `Tow Pilot`, `Tow Charge`, Notes,`ip`,`Key`, `EntrySystem`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s )",
                       GetSQLValueString($_SESSION['$Logdate'], "text"),
                       GetSQLValueString($_POST['Glider'], "text"),
					   GetSQLValueString($_POST['Flight_Type'], "text"),					   
                       GetSQLValueString($_POST['Pilot1'], "text"),
                       GetSQLValueString($_POST['Pilot2'], "text"),
                       GetSQLValueString($_POST['Takeoff'], "date"),
                       GetSQLValueString($_POST['Landing'], "date"),
                       GetSQLValueString($_POST['Tow_Altitude'], "text"),
                       GetSQLValueString($_POST['Tow_Plane'], "text"),
                       GetSQLValueString($_POST['Tow_Pilot'], "text"),
                       GetSQLValueString($_POST['Tow_Charge'], "double"),
                       GetSQLValueString($_POST['Notes'], "text"),
					   GetSQLValueString($_POST['entry_ip'], "text"),
                       GetSQLValueString($_POST['Key'], "int"),
					   GetSQLValueString($_POST['entry_system'], "text"));

 mysql_select_db($database_PGC, $PGC);
 $Result1 = mysql_query($insertSQL, $PGC) or die(mysql_error()); 
 /*==END INSERT AUDIT====*/ 
       
  $insertGoTo = "pgc_flightlog_list_history_edit.php?recordDATE=".$_SESSION['$Logdate'];
  /*$insertGoTo = $_SESSION['last_log_list_page']; */
  if (isset($_SERVER['xQUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));

 }
$colname_Flightlog = "-1";
if (isset($_GET['recordID'])) {
  $colname_Flightlog = (get_magic_quotes_gpc()) ? $_GET['recordID'] : addslashes($_GET['recordID']);
}
mysql_select_db($database_PGC, $PGC);
$query_Flightlog = sprintf("SELECT * FROM pgc_flightsheet WHERE `Key` = %s", $colname_Flightlog);
$Flightlog = mysql_query($query_Flightlog, $PGC) or die(mysql_error());
$row_Flightlog = mysql_fetch_assoc($Flightlog);
$totalRows_Flightlog = mysql_num_rows($Flightlog);
$FlightStart = $row_Flightlog[Takeoff];
$FlightEnd = $row_Flightlog[Landing];


   $updateTime = sprintf("UPDATE pgc_flightsheet SET Time = '' WHERE `Key`=%s",
                         GetSQLValueString($_GET['recordID'], "int"));

  mysql_select_db($database_PGC, $PGC);
  $Result2 = mysql_query($updateTime, $PGC) or die(mysql_error());
  

 $updateTime = sprintf("UPDATE pgc_flightsheet SET Time = TIME_TO_SEC(TIMEDIFF(Landing,Takeoff))/3600 WHERE Landing > Takeoff AND `Key`=%s",
                         GetSQLValueString($_GET['recordID'], "int"));

  mysql_select_db($database_PGC, $PGC);
  $Result2 = mysql_query($updateTime, $PGC) or die(mysql_error());
  
 $updateTime = sprintf("UPDATE pgc_flightsheet SET Time = (TIME_TO_SEC(TIMEDIFF(Landing,Takeoff)) + 60*60*12)/3600 WHERE Landing < Takeoff AND `Key`=%s",
                         GetSQLValueString($_GET['recordID'], "int"));

  mysql_select_db($database_PGC, $PGC);
  $Result2 = mysql_query($updateTime, $PGC) or die(mysql_error());
 
 $updateTime = sprintf("UPDATE pgc_flightsheet SET Time = (TIME_TO_SEC(TIMEDIFF(Landing,Takeoff)) + 60*60*12)/3600 WHERE Landing < Takeoff AND `Key`=%s",
                         GetSQLValueString($_GET['recordID'], "int"));

  mysql_select_db($database_PGC, $PGC);
  $Result2 = mysql_query($updateTime, $PGC) or die(mysql_error());
  
  
  $updateTime = sprintf("UPDATE pgc_flightsheet A, pgc_flightlog_charges B SET  A.`Tow Charge` = B.`charge` WHERE A.`Tow Altitude` = B.`altitude` AND `Key`=%s",
                         GetSQLValueString($_GET['recordID'], "int"));

  mysql_select_db($database_PGC, $PGC);
  $Result2 = mysql_query($updateTime, $PGC) or die(mysql_error());
  
    $updateTime = sprintf("UPDATE pgc_flightsheet A, pgc_flightlog_charges B SET  A.`Tow Charge` = B.`charge` * A.Time WHERE A.`Tow Altitude` = B.`altitude` AND  A.`Tow Altitude` =  'AERO' AND`Key`=%s",
                         GetSQLValueString($_GET['recordID'], "int"));

  mysql_select_db($database_PGC, $PGC);
  $Result2 = mysql_query($updateTime, $PGC) or die(mysql_error());
 
   
mysql_select_db($database_PGC, $PGC);
$query_rsMembers = "SELECT * FROM pgc_members ORDER BY NAME ASC";
$rsMembers = mysql_query($query_rsMembers, $PGC) or die(mysql_error());
$row_rsMembers = mysql_fetch_assoc($rsMembers);
$totalRows_rsMembers = mysql_num_rows($rsMembers);

mysql_select_db($database_PGC, $PGC);
$query_rsGliders = "SELECT glider, nnumber FROM pgc_gliders ORDER BY glider ASC";
$rsGliders = mysql_query($query_rsGliders, $PGC) or die(mysql_error());
$row_rsGliders = mysql_fetch_assoc($rsGliders);
$totalRows_rsGliders = mysql_num_rows($rsGliders);

mysql_select_db($database_PGC, $PGC);
$query_rsTowpilots = "SELECT pilot_name FROM pgc_pilot_ratings WHERE pgc_rating = 'Tow Pilot'";
$rsTowpilots = mysql_query($query_rsTowpilots, $PGC) or die(mysql_error());
$row_rsTowpilots = mysql_fetch_assoc($rsTowpilots);
$totalRows_rsTowpilots = mysql_num_rows($rsTowpilots);

mysql_select_db($database_PGC, $PGC);
$query_rs_instructors = "SELECT * FROM pgc_instructors WHERE rec_active = 'Y' AND cfig = 'Y' ORDER BY Name ASC";
$rs_instructors = mysql_query($query_rs_instructors, $PGC) or die(mysql_error());
$row_rs_instructors = mysql_fetch_assoc($rs_instructors);
$totalRows_rs_instructors = mysql_num_rows($rs_instructors);

mysql_select_db($database_PGC, $PGC);
$query_rs_altitudes = "SELECT altitude FROM pgc_flightlog_charges ORDER BY seq ASC";
$rs_altitudes = mysql_query($query_rs_altitudes, $PGC) or die(mysql_error());
$row_rs_altitudes = mysql_fetch_assoc($rs_altitudes);
$totalRows_rs_altitudes = mysql_num_rows($rs_altitudes);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>PGC Data Portal - Flightlog</title>
<style type="text/css">
<!--
.style1 {	font-size: 18px;
	font-weight: bold;
}
body {
	background-color: #333333;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #CCCCCC;
}
.style3 {font-size: 16px; font-weight: bold; }
.style16 {color: #CCCCCC; }
.style25 {font-size: 18px; font-weight: bold; color: #000000; }
.style26 {color: #CCCCCC; font-size: 14px; }
.style28 {color: #000000; font-weight: bold;}
.style29 {font-size: 14px; font-weight: bold; color: #000000; }
.style31 {color: #330033; font-size: 14px; }
.style32 {font-size: 14px}
.style33 {font-size: 14px; font-weight: bold; }
-->
</style>
</head>

<script language="javascript" src="../calendar/calendar.js"></script>




<body>
<p>&nbsp;</p>
<table width="800" align="center" cellpadding="2" cellspacing="2" bordercolor="#000033" bgcolor="#666666">
  <tr>
    <td><div align="center"><span class="style1">PGC DATA PORTAL </span></div></td>
  </tr>
  <tr>
    <td height="481" bgcolor="#4E4E4E"><table width="92%" height="447" align="center" cellpadding="2" cellspacing="2" bordercolor="#005B5B" bgcolor="#4F5359">
      
      <tr>
        <td height="373" bgcolor="#454B63"><p align="center"><span class="style33">FLIGHTLOG HISTORY UPDATE </span></p>
                <form method="post" name="form2" action="<?php echo $editFormAction; ?>">
                  <table align="center" cellpadding="3" cellspacing="3" bgcolor="#666666" class="style25">
              <tr valign="baseline">
                <td width="165" align="right" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Date:</div></td>
                <td width="329" bgcolor="#620000"><input name="Date" type="text" class="style28" value="<?php echo $row_Flightlog['Date']; ?>" size="32" /></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Glider:</div></td>
                <td bgcolor="#620000"><select name="Glider" class="style29" id="Glider">
                  <?php
do {  
?>
                  <option value="<?php echo $row_rsGliders['glider']?>"<?php if (!(strcmp($row_rsGliders['glider'], $row_Flightlog['Glider']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsGliders['glider']?></option>
                  <?php
} while ($row_rsGliders = mysql_fetch_assoc($rsGliders));
  $rows = mysql_num_rows($rsGliders);
  if($rows > 0) {
      mysql_data_seek($rsGliders, 0);
	  $row_rsGliders = mysql_fetch_assoc($rsGliders);
  }
?>
                </select></td>
              </tr>
              <tr valign="baseline">
                  <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Flight Type:</div></td>
                  <td bgcolor="#620000">
				  <select name="Flight_Type" class="style29" id="Flight_Type" select>
                      <option value="TRN" <?php if (!(strcmp("TRN", $row_Flightlog['Flight_Type']))) {echo "selected=\"selected\"";} ?>>TRN</option>
                      <option value="REG" <?php if (!(strcmp("REG", $row_Flightlog['Flight_Type']))) {echo "selected=\"selected\"";} ?>>REG</option>
                      <option value="AOF" <?php if (!(strcmp("AOF", $row_Flightlog['Flight_Type']))) {echo "selected=\"selected\"";} ?>>AOF</option>
                  </select></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Member Charged:</div></td>
                <td bgcolor="#620000"><span class="style31">
                  <select name="Pilot1" class="style25" id="Pilot1">
                    <?php
do {  
?>
                    <option value="<?php echo $row_rsMembers['NAME']?>"<?php if (!(strcmp($row_rsMembers['NAME'], $row_Flightlog['Pilot1']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsMembers['NAME']?></option>
                    <?php
} while ($row_rsMembers = mysql_fetch_assoc($rsMembers));
  $rows = mysql_num_rows($rsMembers);
  if($rows > 0) {
      mysql_data_seek($rsMembers, 0);
	  $row_rsMembers = mysql_fetch_assoc($rsMembers);
  }
?>
                  </select>
                </span></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Instructor:</div></td>
                <td bgcolor="#620000"><span class="style31">
                    <select name="Pilot2" class="style25" id="Pilot2">
                        <?php
do {  
?>
                        <option value="<?php echo $row_rs_instructors['Name']?>"<?php if (!(strcmp($row_rs_instructors['Name'], $row_Flightlog['Pilot2']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rs_instructors['Name']?></option>
                        <?php
} while ($row_rs_instructors = mysql_fetch_assoc($rs_instructors));
  $rows = mysql_num_rows($rs_instructors);
  if($rows > 0) {
      mysql_data_seek($rs_instructors, 0);
	  $row_rs_instructors = mysql_fetch_assoc($rs_instructors);
  }
?>
                    </select>
                </span></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Takeoff:</div></td>
                <td bgcolor="#620000"><input name="Takeoff" type="text" class="style28" value="<?php echo $row_Flightlog['Takeoff']; ?>" size="8" maxlength="8"></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Landing:</div></td>
                <td bgcolor="#620000"><input name="Landing" type="text" class="style28" value="<?php echo $row_Flightlog['Landing']; ?>" size="8" maxlength="8"></td>
              </tr>
              <tr valign="baseline">
                  <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Hours:  </div></td>
                  <td bgcolor="#620000"><input name="Landing2" type="text" class="style28" value="<?php echo $row_Flightlog['Time']; ?>" size="6" maxlength="6" /></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Tow Altitude:</div></td>
                <td bgcolor="#620000"><select name="Tow_Altitude" class="style29">
                  <?php
do {  
?>
                  <option value="<?php echo $row_rs_altitudes['altitude']?>"<?php if (!(strcmp($row_rs_altitudes['altitude'], $row_Flightlog['Tow Altitude']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rs_altitudes['altitude']?></option>
                  <?php
} while ($row_rs_altitudes = mysql_fetch_assoc($rs_altitudes));
  $rows = mysql_num_rows($rs_altitudes);
  if($rows > 0) {
      mysql_data_seek($rs_altitudes, 0);
	  $row_rs_altitudes = mysql_fetch_assoc($rs_altitudes);
  }
?>
                                </select></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Tow Plane:</div></td>
                <td bgcolor="#620000"><select name="Tow_Plane" class="style29">
                    <option value="305A" <?php if (!(strcmp("305A", $row_Flightlog['Tow Plane']))) {echo "selected=\"selected\"";} ?>>305A</option>
                    <option value="76P" <?php if (!(strcmp("76P", $row_Flightlog['Tow Plane']))) {echo "selected=\"selected\"";} ?>>76P</option>
                </select></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Tow Pilot:</div></td>
                <td bgcolor="#620000"><select name="Tow_Pilot" class="style29">
                    <?php
do {  
?>
                    <option value="<?php echo $row_rsTowpilots['pilot_name']?>"<?php if (!(strcmp($row_rsTowpilots['pilot_name'], $row_Flightlog['Tow Pilot']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsTowpilots['pilot_name']?></option>
                    <?php
} while ($row_rsTowpilots = mysql_fetch_assoc($rsTowpilots));
  $rows = mysql_num_rows($rsTowpilots);
  if($rows > 0) {
      mysql_data_seek($rsTowpilots, 0);
	  $row_rsTowpilots = mysql_fetch_assoc($rsTowpilots);
  }
?>
                </select></td>
              </tr>
              <tr valign="baseline">
                <td align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Tow Charge:</div></td>
                <td bgcolor="#620000"><input name="Tow_Charge" type="text" class="style28" value="<?php echo $row_Flightlog['Tow Charge']; ?>" size="6" maxlength="6"></td>
              </tr>
              <tr valign="baseline">
                <td height="28" align="right" valign="middle" nowrap bgcolor="#620000" class="style25"><div align="left" class="style26">Notes:</div></td>
                <td bgcolor="#620000"><input name="Notes" type="text" class="style28" value="<?php echo $row_Flightlog['Notes']; ?>" size="32"></td>
              </tr>
              <tr valign="baseline">
                <td colspan="2" align="right" nowrap bgcolor="#620000" class="style25"><div align="center" class="style32">
                    <input type="submit" class="style28" value="Update record" >
                </div></td>
                </tr>
            </table>
                  <input type="hidden" name="MM_update" value="form2">
            <input type="hidden" name="Key" value="<?php echo $row_Flightlog['Key']; ?>">
          </form>
          <p>
              <label></label>
              <label></label>
          </p></td>
      </tr>
      <tr>
 


        <td height="28"><div align="center"><strong class="style3"><a href="pgc_flightlog_list_edit.php" class="style16"> </a></strong></div></td>
      </tr>
    </table></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($Flightlog);

mysql_free_result($rsMembers);

mysql_free_result($rsGliders);

mysql_free_result($rsTowpilots);

mysql_free_result($rs_instructors);

mysql_free_result($rs_altitudes);
?>



