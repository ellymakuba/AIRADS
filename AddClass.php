<?php
$PageSecurity = 3;
include('includes/session.inc');
$title = _('Class Maintenance');
include('includes/header.inc');
echo '<p class="page_title_text">' . ' ' . _('Class Maintenance') . '';   
if (isset($_GET['SelectedClass'])) {
	$SelectedClass=$_GET['SelectedClass'];
} elseif (isset($_POST['SelectedClass'])) {
	$SelectedClass=$_POST['SelectedClass'];
}

if (isset($Errors)) {
	unset($Errors);
}

$Errors = array();

$sql="SELECT course_name FROM courses WHERE id='".$_POST['course_id']."'";
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_array($result);
	$course=$myrow['course_name'];

	$course= ucwords($course);	
	$sql="SELECT month_name FROM months WHERE id='".$_POST['month_id']."'";
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_array($result);
	$month=$myrow['month_name'];
	
	$sql="SELECT year FROM years WHERE id='".$_POST['year_id']."'";
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_array($result);
	$year=$myrow['year'];

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	$i=1;
	$class=strtoupper($course ._('/'). $month._('/').$year);
	
	$sql="SELECT count(id)
			FROM classes WHERE class_name='".$class."'";
	$result=DB_query($sql, $db);
	$myrow=DB_fetch_row($result);

	if ($myrow[0]>0 and !isset($SelectedClass)) {
		$InputError = 1;
		prnMsg( _('Class already exists in the database'),'error');
		$Errors[$i] = 'class_name';
		$i++;
	}
	
	if (isset($SelectedClass) AND $InputError !=1) {	
	$sql = "UPDATE debtorsmaster SET 		 
grade_level_id='".$_POST['grade_level_id']."'
WHERE class_id = '".$SelectedClass."'";
$query = DB_query($sql,$db);


	
			$sql = "UPDATE classes
				SET class_name='".$class . "',
				month_id='" . $_POST['month_id'] . "',
				year_id='" . $_POST['year_id'] . "',
				current_term='" . $_POST['current_term'] . "',
				course_id='" . $_POST['course_id'] . "',
				final_term='" . $_POST['final_term'] . "',
				status='" . $_POST['status'] . "'
			WHERE id = '" . $SelectedClass . "'";
		
		$msg = _('The class details have been updated');
	} elseif ($InputError !=1) {

	
		$sql = "INSERT INTO classes (
						class_name,
						month_id,
						year_id,
						current_term,
						course_id,
						final_term,
						status
						)
				VALUES ('".$class. "',
					'" . $_POST['month_id'] . "',
					'" . $_POST['year_id'] . "',
					'" . $_POST['current_term'] . "',
					'" . $_POST['course_id'] . "',
					'" . $_POST['final_term'] . "',
					'" . $_POST['status'] . "'
					)";
		$msg = _('The new class has been entered');
	}

	//run the SQL from either of the above possibilites
	if( $InputError !=1 ) {
		$ErrMsg = _('The class could not be inserted or modified because');
		$DbgMsg = _('The SQL used to insert/modify the class details was');
		$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

		prnMsg($msg,'success');
		echo '<br>';
		unset($_POST['class_name']);
		unset($_POST['month_id']);
		unset($_POST['year_id']);
		unset($_POST['current_term']);
		unset($_POST['final_term']);
		unset($_POST['course_id']);
		unset($SelectedClass);
	}


} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;


	$sql= "SELECT COUNT(class_id) FROM registered_students WHERE class_id='$SelectedClass'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		prnMsg(_('Cannot delete this class since there are students who have registered for it'),'warn');
		echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('students in this class');

	}
	
	$sql= "SELECT COUNT(class_id) FROM debtorsmaster WHERE class_id='$SelectedClass'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		prnMsg(_('Cannot delete this class since there are students who belong to it'),'warn');
		echo '<br> ' . _('There are') . ' ' . $myrow[0] . ' ' . _('students in this class');

	}
	
	if (!$CancelDelete) {
		$sql="DELETE FROM classes WHERE id='$SelectedClass'";
		$result = DB_query($sql,$db);
		prnMsg(_('Class deleted'),'success');
	} //end if Delete bank account

	unset($_GET['delete']);
	unset($SelectedClass);
}

/* Always show the list of accounts */
If (!isset($SelectedClass)) {
echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	
echo '<table class=enclosed>';
	echo '<tr><td>' . _('Enter Class Name') . ':<input type="Text" name="searchval" 
  size=30   maxlength=20></td>
		<td><input  type="submit" name="form1" value="Search"></td></tr>';

	
	
	echo "<tr><th>" . _('Class Name') . "</th>
		<th>" . _('Intake Month') . "</th>
		<th>" . _('Current Term') . "</th>
		<th>" . _('Intake Year') . "</th>
		<th>" . _('Course') . "</th>
	</tr>";
	$k=0; //row colour counter
if (isset($_GET['pageno'])) {
   $pageno = $_GET['pageno'];
} else {
   $pageno = 1;
}
$sql = "SELECT count(*) FROM classes";
$result = DB_query($sql,$db);
$query_data = DB_fetch_row($result);
$numrows = $query_data[0];
			
$targetpage = "AddClass.php";
$rows_per_page = 10;
$lastpage      = ceil($numrows/$rows_per_page);
$pageno = (int)$pageno;
if ($pageno > $lastpage) {
   $pageno = $lastpage;
} // if
$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;
$SearchString = '%' . str_replace(' ', '%', $_POST['searchval']) . '%';
if (isset($_POST['form1'])){
$sql = "SELECT * FROM classes
	WHERE class_name LIKE  '". $SearchString."'
	ORDER BY class_name LIMIT 100";
	$result = DB_query($sql,$db);
}
else{	
	$sql = "SELECT *
		FROM classes
		ORDER BY class_name $limit";
	$result = DB_query($sql,$db);
}
	
	while ($myrow = DB_fetch_array($result)) {		
	echo '<tr>';
	$sql2 = "SELECT title
	FROM terms
	WHERE id='".$myrow['current_term']."'";
	$result2 = DB_query($sql2,$db);
	$myrow2=DB_fetch_array($result2);
	$current_term=$myrow2['title'];
	
	$sql3 = "SELECT course_name
	FROM courses
	WHERE id='".$myrow['course_id']."'";
	$result3 = DB_query($sql3,$db);
	$myrow3=DB_fetch_array($result3);
	$course=$myrow3['course_name'];
	
	$sql4 = "SELECT year
	FROM years
	WHERE id='".$myrow['year_id']."'";
	$result4 = DB_query($sql4,$db);
	$myrow4=DB_fetch_array($result4);
	$year=$myrow4['year'];
	
	$sql5= "SELECT month_name
	FROM months
	WHERE id='".$myrow['month_id']."'";
	$result5 = DB_query($sql5,$db);
	$myrow5=DB_fetch_array($result5);
	$month=$myrow5['month_name'];

		printf("<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td><a href=\"%s&SelectedClass=%s&delete=1&class_name=%s\">" . _('Delete') . "</a></td>
			",
			$myrow['class_name'],
			$month,
			$current_term,
			$year,
			$course,
			$_SERVER['PHP_SELF'] . "?" . SID,
			$myrow['id'],
			urlencode($myrow['class_name']));
 print "<td class=\"visible\"><a href=javascript:viewdetails(".$myrow['id'].")>Edit</a></td></tr>";
	} //END WHILE LIST LOOP
	echo "<tr>";
if ($pageno == 1) {
   echo "<td>"." FIRST PREV"." </td>";
} else {
   echo " <td><a href='{$_SERVER['PHP_SELF']}?pageno=1'>FIRST</a> ";
   $prevpage = $pageno-1;
   echo " <a href='{$_SERVER['PHP_SELF']}?pageno=$prevpage'>PREV</a> "." </td>";
}
echo " <td>( Page $pageno of $lastpage ) "." </td>";
if ($pageno == $lastpage) {
   echo "<td> NEXT LAST "."</td>";
} else {
   $nextpage = $pageno+1;
   echo "<td> <a href='{$_SERVER['PHP_SELF']}?pageno=$nextpage'>NEXT</a> ";
   echo " <a href='{$_SERVER['PHP_SELF']}?pageno=$lastpage'>LAST</a> "." </td>";
}		
	echo '</tr></table><p>';
}


if (isset($SelectedClass)) {
	echo '<p>';
	echo "<br /><div class='centre'><a href='" . $_SERVER['PHP_SELF'] ."?" . SID . "'>" . _('Show all Classes') . '</a></div>';
	echo '<p>';
}

echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . "?" . SID . ">";
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
if (isset($SelectedClass) AND !isset($_GET['delete'])) {
	//editing an existing bank account  - not deleting

	$sql = "SELECT *
		FROM classes
		WHERE id='$SelectedClass'";

	$result = DB_query($sql, $db);
	$myrow = DB_fetch_array($result);

	$_POST['class_name'] = $myrow['class_name'];
	$_POST['month_id']  = $myrow['month_id'];
	$_POST['year_id']  = $myrow['year_id'];
	$_POST['current_term']  = $myrow['current_term'];
	$_POST['course_id'] = $myrow['course_id'];
	$_POST['final_term'] = $myrow['final_term'];
	$_POST['status'] = $myrow['status'];
	
	echo '<input type=hidden name=SelectedClass VALUE=' . $SelectedClass . '>';
	echo '<input type=hidden name=class_name VALUE=' . $_POST['class_name'] . '>';
	echo '<table class=enclosed> ';
} else { //end of if $Selectedbank account only do the else when a new record is being entered
	echo '<table class=enclosed><tr>';

	
}

// Check if details exist, if not set some defaults
if (!isset($_POST['class_name'])) {
	$_POST['class_name']='';
}
if (!isset($_POST['month_id'])) {
	$_POST['month_id']='';
}
if (!isset($_POST['year_id'])) {
        $_POST['year_id']='';
}
if (!isset($_POST['current_term'])) {
        $_POST['current_trm']='';
}
if (!isset($_POST['course_id'])) {
	$_POST['course_id']='';
}
if (!isset($_POST['final_term'])) {
	$_POST['final_term']='';
}

echo '</br><tr><td>' . _('Class Name') . ': </td>
			<td><input tabindex="2" ' . (in_array('class_name',$Errors) ?  'class="inputerror"' : '' ) .' type="Text" name="class_name" value="' . $_POST['class_name'] . '" size=100 maxlength=150 readonly=""></td></tr>
			
						
<tr><td>' . _('Intake Month') . ': </td><td><select tabindex="5" name="month_id">';
$result = DB_query('SELECT * FROM months',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['month_id']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['month_name'];
} //end while loop


echo '</select></td></tr>

		<tr><td>' . _('Intake Year') . ': </td><td><select tabindex="5" name="year_id">';


$result = DB_query('SELECT * FROM years',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['year_id']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['year'];
} //end while loop

echo '</select></td></tr>
<tr><td>' . _('Current Term') . ': </td><td><select tabindex="5" name="current_term">';
$result = DB_query('SELECT * FROM terms',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['current_term']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['title'];
} //end while loop	
echo '</select></td></tr>


<tr><td>' . _('Final Term') . ': </td><td><select tabindex="5" name="final_term">';
$result = DB_query('SELECT * FROM terms',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['final_term']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['title'];
} //end while loop


echo '</select></td></tr>
	<tr><td>' . _('Course') . ': </td><td><select tabindex="5" name="course_id">';


$result = DB_query('SELECT * FROM courses ORDER BY course_name',$db);
while ($myrow = DB_fetch_array($result)) {
	if ($myrow['id']==$_POST['course_id']) {
		echo '<option selected VALUE=';
	} else {
		echo '<option VALUE=';
	}
	echo $myrow['id'] . '>' . $myrow['course_name'];
} //end while loop

echo '</select></td></tr>';
echo '<TR><td class="visible">' . _('Status') . ":</TD><td class=\"visible\"><SELECT name='status'>";
if ($_POST['status']==0){
	echo '<OPTION SELECTED VALUE=0>' . _('Active');
	echo '<OPTION VALUE=1>' . _('Inactive');
} else {
 	echo '<OPTION SELECTED VALUE=1>' . _('Inactive');
	echo '<OPTION VALUE=0>' . _('Active');
}

echo '</select></td>';
echo '</tr></table><br>
		<div class="centre"><input tabindex="7" type="Submit" name="submit" value="'. _('Enter Information') .'"></div>';

echo '</form>';
include('includes/footer.inc');
?>
<script language="JavaScript">
   
	 
     function viewdetails(whereid)
	 {
        var url = "EditClass.php?id="+whereid;
   
        newwin = window.open(url,'View','width=800,height=400,toolbar=0,location=0,directories=0,status=0,menuBar=0,scrollBars=1,resizable=1');
        newwin.focus();
     }
</script>