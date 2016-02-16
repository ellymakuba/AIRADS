<?php
$PageSecurity = 1;
include('includes/session.inc');
$title = _('Period Maintenance');
include('includes/header.inc');
echo '<p class="page_title_text">' . ' ' . _('Period Maintenance') . '';       

if (isset($_GET['SelectedPeriod'])) {
	$SelectedPeriod=$_GET['SelectedPeriod'];
} elseif (isset($_POST['SelectedPeriod'])) {
	$SelectedPeriod=$_POST['SelectedPeriod'];
}

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();

if (isset($_POST['submit'])) {
	$InputError = 0;
	$i=1;

	if (isset($SelectedPeriod) AND $InputError !=1) {

			$sql = "UPDATE collegeperiods
				SET term_id='" . $_POST['term'] . "',
				start_date='" . FormatDateForSQL($_POST['start_date']) . "',
				end_term_one='" . FormatDateForSQL($_POST['end_term_one']) . "',
				end_term_two='" . FormatDateForSQL($_POST['end_term_two']) . "',
				end_term_three='" . FormatDateForSQL($_POST['end_term_three']) . "',
				end_date='" . FormatDateForSQL($_POST['end_date']) . "',
				month_id='" . $_POST['month_id'] . "',
				year ='" . $_POST['year'] . "'
			WHERE id = '" . $SelectedPeriod . "'";

		$msg = _('The period details have been updated');
	} elseif ($InputError !=1) {
$sql = "SELECT id FROM collegeperiods
		WHERE term_id='".$_POST['term'] ."'
		AND year='". $_POST['year'] ."'";
		$result=DB_query($sql,$db);
if(DB_fetch_row($result)>0){
		prnMsg(_('These term and year already exist in the system,please enter a different period'),'warn');
		
}
else{
	$sql = "INSERT INTO collegeperiods (
			term_id,
			start_date,
			end_term_one,
			end_term_two,
			end_term_three,
			end_date,
			month_id,
			year
						)
				VALUES ('" . $_POST['term'] . "',
					'" . FormatDateForSQL($_POST['start_date']) . "',
					'" . FormatDateForSQL($_POST['end_term_one']) . "',
					'" . FormatDateForSQL($_POST['end_term_two']) . "',
					'" . FormatDateForSQL($_POST['end_term_three']) . "',
					'" . FormatDateForSQL($_POST['end_date']) . "',
					'" . $_POST['month_id'] . "',
					'" . $_POST['year'] . "'
					)";
		$msg = _('The new period has been entered');
	}
}
	//run the SQL from either of the above possibilites
	if( $InputError !=1 ) {
		$ErrMsg = _('The period could not be inserted or modified because');
		$DbgMsg = _('The SQL used to insert/modify the period details was');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

		echo '<br>';
		unset($_POST['start_date']);
		unset($_POST['end_date']);
		unset($_POST['end_term_one']);
		unset($_POST['end_term_two']);
		unset($_POST['end_term_three']);
		unset($_POST['term']);
		unset($_POST['year']);
		unset($_POST['month_id']);
		unset($SelectedPeriod);
	}


} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'BankTrans'

	$sql= "SELECT COUNT(*) FROM registered_students WHERE period_id='$SelectedPeriod'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		prnMsg(_('Cannot delete this period since there are students under it'),'warn');
		echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('students under this period');

	}
	if (!$CancelDelete) {
		$sql="DELETE FROM collegeperiods WHERE id='$SelectedPeriod'";
		$result = DB_query($sql,$db);
		prnMsg(_('period deleted'),'success');
	} //end if Delete bank account

	unset($_GET['delete']);
	unset($SelectedPeriod);
}

/* Always show the list of accounts */
If (!isset($SelectedPeriod)) {
	$sql = "SELECT cp.*,t.title as term_name,y.year as year_name FROM collegeperiods cp
	INNER JOIN terms t ON t.id=cp.term_id
	INNER JOIN years y ON y.id=cp.year
		ORDER BY id";
	$result = DB_query($sql,$db);

	echo '<table class=enclosed>';
	
	echo "<tr><th>" . _('Term') . "</th>
		<th>" . _('Start Date') . "</th>
		<th>" . _('End of Term 1') . "</th>
		<th>" . _('End of Term 2') . "</th>
		<th>" . _('End of Term 3') . "</th>
		<th>" . _('End Date') . "</th>
		<th>" . _('Month') . "</th>
		<th>" . _('Year') . "</th>
	</tr>";
	$k=0; //row colour counter

	while ($myrow = DB_fetch_array($result)) {
			echo '<tr>';
		printf("<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td><a href=\"%s&SelectedPeriod=%s\">" . _('Edit') . "</a></td>
			<td><a href=\"%s&SelectedPeriod=%s&delete=1&term_id=%s\">" . _('Delete') . "</a></td>
			</tr>",
			$myrow['term_name'],
			$myrow['start_date'],
			$myrow['end_term_one'],
			$myrow['end_term_two'],
			$myrow['end_term_three'],
			$myrow['end_date'],
			$myrow['month_id'],
			$myrow['year_name'],
			$_SERVER['PHP_SELF']  . "?" . SID,
			$myrow['id'],
			$_SERVER['PHP_SELF'] . "?" . SID,
			$myrow['id'],
			urlencode($myrow['term_id']));

	} //END WHILE LIST LOOP
	echo '</table><p>';
}

if (isset($SelectedPeriod)) {
	echo '<p>';
	echo "<div class='centre'><a href='" . $_SERVER['PHP_SELF'] ."?" . SID . "'>" . _('Show all periods') . '</a></div>';
	echo '<p>';
}

echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . "?" . SID . ">";
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if (isset($SelectedPeriod) AND !isset($_GET['delete'])) {
	//editing an existing bank account  - not deleting

	$sql = "SELECT *
		FROM collegeperiods
		WHERE id='$SelectedPeriod'";

	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);

	$_POST['title'] = $myrow['title'];
	$_POST['term']  = $myrow['term_id'];
	$_POST['start_date']  = $myrow['start_date'];
	$_POST['end_term_one'] = $myrow['end_term_one'];
	$_POST['end_term_two'] = $myrow['end_term_two'];
	$_POST['end_term_three'] = $myrow['end_term_three'];
	$_POST['end_date'] = $myrow['end_date'];
	$_POST['month_id'] = $myrow['month_id'];
	$_POST['year'] = $myrow['year'];
	
	echo '<input type=hidden name=SelectedPeriod VALUE=' . $SelectedPeriod . '>';
	echo '<input type=hidden name=term_id VALUE=' . $_POST['term_id'] . '>';
	echo '<table class=enclosed>';
} else { //end of if $Selectedbank account only do the else when a new record is being entered
	echo '<table class=enclosed><tr>';

	
}

// Check if details exist, if not set some defaults

if (!isset($_POST['term'])) {
	$_POST['term']='';
}
if (!isset($_POST['start_date'])) {
        $_POST['start_date']='';
}
if (!isset($_POST['end_date'])) {
	$_POST['end_date']='';
}
if (!isset($_POST['year'])) {
	$_POST['year']='';
}
if (!isset($_POST['end_term_one'])) {
	$_POST['end_term_one']='';
}
if (!isset($_POST['end_term_two'])) {
	$_POST['end_term_two']='';
}
if (!isset($_POST['end_term_three'])) {
	$_POST['end_term_three']='';
}
if (!isset($_POST['month_id'])) {
	$_POST['month_id']='';
}

echo '<td>' .  _('Term') . ': </td><td><select tabindex="5" name="term">';
$result = DB_query('SELECT id,title FROM terms',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['title']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['title'];
} //end while loop
echo '</select></td></tr>
		<tr><td>' . _('Start Date') . ': </td>
			<td><input tabindex="3" ' . (in_array('start_date',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="start_date" value="' . $_POST['start_date'] . '" size=40 maxlength=50></td></tr>
			
<tr><td>' . _('End Term 1') . ': </td>
			<td><input tabindex="3" ' . (in_array('end_term_one',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="end_term_one"  size=40 value="' . $_POST['end_term_one'] . '" size=40 maxlength=50></td></tr>			
		
<tr><td>' . _('End Term 2') . ': </td>
			<td><input tabindex="3" ' . (in_array('end_term_two',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="end_term_two"  size=40 value="' . $_POST['end_term_two'] . '" size=40 maxlength=50></td></tr>	
			
<tr><td>' . _('End Term 3') . ': </td>
<td><input tabindex="3" ' . (in_array('end_term_three',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="end_term_three"  size=40 value="' . $_POST['end_term_three'] . '" size=40 maxlength=50></td></tr>	
			
<tr><td>' . _('End Date') . ': </td>
<td><input tabindex="3" ' . (in_array('end_date',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" class="date" alt="'.$_SESSION['DefaultDateFormat'].'" name="end_date"  size=40 value="' . $_POST['end_term_three'] . '" size=40 maxlength=50></td></tr>	

<tr><td>' . _('Month') . ': </td><td><select tabindex="5" name="month_id">';
$result = DB_query('SELECT id,month_name FROM months',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['month_id']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['month_name'];
} //end while loop

echo '</select></td></tr>
				
		<tr><td>' . _('Year') . ': </td><td><select tabindex="5" name="year">';
$result = DB_query('SELECT id,year FROM years',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['year']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['year'];
} //end while loop

echo '</select></td>';

echo '</tr></table><br>
		<div class="centre"><input tabindex="7" type="Submit" name="submit" value="'. _('Enter Information') .'"></div>';

echo '</form>';
include('includes/footer.inc');
?>
