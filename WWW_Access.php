<?php
$PageSecurity=1;
include('includes/session.inc');
$title = _('Access Permission Maintenance');
include('includes/header.inc');
echo '<p class="page_title_text"><img src="'.$rootpath.'/css/'.$theme.'/images/group_add.png" title="' .
_('Search') . '" alt="">' . ' ' . $title.'</p';
echo '</table>';
if (isset($_GET['SelectedRole'])){
	$SelectedRole = $_GET['SelectedRole'];
} elseif (isset($_POST['SelectedRole'])){
	$SelectedRole = $_POST['SelectedRole'];
}

if (isset($_POST['submit']) || isset($_GET['remove']) || isset($_GET['add']) ) {
	$InputError = 0;
	if (isset($_POST['SecRoleName']) && strlen($_POST['SecRoleName'])<4){
		$InputError = 1;
		prnMsg(_('The role description entered must be at least 4 characters long'),'error');
	}
	unset($sql);
	if (isset($_POST['SecRoleName']) ){ // Update or Add Security Headings
		if(isset($SelectedRole)) { // Update Security Heading
			$sql = "UPDATE securityroles SET secrolename = '".$_POST['SecRoleName']."'
					WHERE secroleid = '".$SelectedRole . "'";
			$ErrMsg = _('The update of the security role description failed because');
			$ResMsg = _('The Security role description was updated.');
		} else { // Add Security Heading
			$sql = "INSERT INTO securityroles (secrolename) VALUES ('".$_POST['SecRoleName']."')";
			$ErrMsg = _('The update of the security role failed because');
			$ResMsg = _('The Security role was created.');
		}
		unset($_POST['SecRoleName']);
		unset($SelectedRole);
	} elseif (isset($SelectedRole) ) {
		$PageTokenId = $_GET['PageToken'];
		if( isset($_GET['add']) ) { // updating Security Groups add a page token
			$sql = "INSERT INTO securitygroups (
					secroleid, tokenid
					) VALUES (
					'".$SelectedRole."',
					'".$PageTokenId."'
					)";
			$ErrMsg = _('The addition of the page group access failed because');
			$ResMsg = _('The page group access was added.');
		} elseif ( isset($_GET['remove']) ) { // updating Security Groups remove a page token
			$sql = "DELETE FROM securitygroups
					WHERE secroleid = '".$SelectedRole."'
					AND tokenid = '".$PageTokenId . "'";
			$ErrMsg = _('The removal of this page-group access failed because');
			$ResMsg = _('This page-group access was removed.');
		}
		unset($_GET['add']);
		unset($_GET['remove']);
		unset($_GET['PageToken']);
	}
	// Need to exec the query
	if (isset($sql) && $InputError != 1 ) {
		$result = DB_query($sql,$db,$ErrMsg);
		if( $result ) {
			prnMsg( $ResMsg,'success');
		}
	}
} elseif (isset($_GET['delete'])) {
	$sql= "SELECT COUNT(*) FROM www_users WHERE fullaccess='" . $_GET['SelectedRole'] . "'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		prnMsg( _('Cannot delete this role because user accounts are setup using it'),'warn');
		echo '<br>' . _('There are') . ' ' . $myrow[0] . ' ' . _('user accounts that have this security role setting') . '</font>';
	} else {
		$sql="DELETE FROM securitygroups WHERE secroleid='" . $_GET['SelectedRole'] . "'";
		$result = DB_query($sql,$db);
		$sql="DELETE FROM securityroles WHERE secroleid='" . $_GET['SelectedRole'] . "'";
		$result = DB_query($sql,$db);
		prnMsg( $_GET['SecRoleName'] . ' ' . _('security role has been deleted') . '!','success');

	} //end if account group used in GL accounts
	unset($SelectedRole);
	unset($_GET['SecRoleName']);
}

if (!isset($SelectedRole)) {
	$sql = "SELECT secroleid,secrolename FROM securityroles ORDER BY secroleid";
	$result = DB_query($sql,$db);
	echo '<table class="enclosed">';
	echo "<tr><th>" . _('Role') . "</th></tr>";
	$k=0; //row colour counter
	while ($myrow = DB_fetch_array($result)) 
	{
		printf("<td>%s</td>
			<td><a href=\"%s&SelectedRole=%s\">" . _('Edit') . "</a></td>
			<td><a href=\"%s&SelectedRole=%s&delete=1&SecRoleName=%s\">" . _('Delete') . "</a></td>
			</tr>",
			$myrow['secrolename'],
			$_SERVER['PHP_SELF']  . "?" . SID,
			$myrow['secroleid'],
			$_SERVER['PHP_SELF'] . "?" . SID,
			$myrow['secroleid'],
			urlencode($myrow['secrolename']));

	} //END WHILE LIST LOOP
	echo '</table>';
} //end of ifs and buts!
if (isset($SelectedRole)) {
	echo "<br /><div class='centre'><a href='" . $_SERVER['PHP_SELF'] ."?" . SID . "'>" . _('Review Existing Roles') . '</a></div>';
}
if (isset($SelectedRole)) {
	$sql = "SELECT secroleid,
			secrolename
		FROM securityroles
		WHERE secroleid='" . $SelectedRole . "'";
	$result = DB_query($sql, $db);
	if ( DB_num_rows($result) == 0 ) {
		prnMsg( _('The selected role is no longer available.'),'warn');
	} else {
		$myrow = DB_fetch_array($result);
		$_POST['SelectedRole'] = $myrow['secroleid'];
		$_POST['SecRoleName'] = $myrow['secrolename'];
	}
}
echo '<br>';
echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . "?" . SID . ">";
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if( isset($_POST['SelectedRole'])) {
	echo "<input type=hidden name='SelectedRole' VALUE='" . $_POST['SelectedRole'] . "'>";
}
echo '<table class=enclosed>';
if (!isset($_POST['SecRoleName'])) {
	$_POST['SecRoleName']='';
}
echo '<tr><td>' . _('Role') . ":</td>
	<td><input type='text' name='SecRoleName' size=40 maxlength=40 VALUE='" . $_POST['SecRoleName'] . "'></tr>";
echo "</table><br />
	<div class='centre'><input type='Submit' name='submit' value='" . _('Enter Role') . "'></div></form>";

if (isset($SelectedRole)) {
	$sql = 'SELECT tokenid, tokenname FROM securitytokens';
	$sqlUsed = "SELECT tokenid FROM securitygroups WHERE secroleid='". $SelectedRole . "'";
	$Result = DB_query($sql, $db);
	/*Make an array of the used tokens */
	$UsedResult = DB_query($sqlUsed, $db);
	$TokensUsed = array();
	$i=0;
	while ($myrow=DB_fetch_row($UsedResult)){
		$TokensUsed[$i] =$myrow[0];
		$i++;
	}
	echo '<br /><table class=enclosed><tr>';
	if (DB_num_rows($Result)>0 ) {
		echo "<th colspan=3><div class='centre'>"._('Assigned Security Tokens')."</div></th>";
		echo "<th colspan=3><div class='centre'>"._('Available Security Tokens')."</div></th>";
	}
	echo '</tr>';
	$k=0; //row colour counter
	while($AvailRow = DB_fetch_array($Result)) {
		if (in_array($AvailRow['tokenid'],$TokensUsed)){
			printf("<td>%s</td><td>%s</td>
				<td><a href=\"%s&SelectedRole=%s&remove=1&PageToken=%s\">" . _('Remove') . "</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>",
				$AvailRow['tokenid'],
				$AvailRow['tokenname'],
				$_SERVER['PHP_SELF']  . "?" . SID,
				$SelectedRole,
				$AvailRow['tokenid']
				);
		} else {
			printf("<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>%s</td>
				<td>%s</td>
				<td><a href=\"%s&SelectedRole=%s&add=1&PageToken=%s\">" . _('Add') . "</a></td>",
				$AvailRow['tokenid'],
				$AvailRow['tokenname'],
				$_SERVER['PHP_SELF']  . "?" . SID,
				$SelectedRole,
				$AvailRow['tokenid']
				);
		}
		echo '</tr>';
	}
	echo '</table>';
	}
include('includes/footer.inc');
?>