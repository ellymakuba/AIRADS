<?php
$PageSecurity = 6;
include('includes/session.inc');

$title = _('Students Per Course');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
$msg='';
echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class=enclosed>';
	
echo '<tr><td>' . _('Class') . ":</td>
		<td><select name='student_class'>";
		echo '<OPTION SELECTED VALUE=0>' . _('Select Class');
		$sql="SELECT cl.id,cl.class_name,c.course_name FROM classes cl 
		INNER JOIN courses c ON c.id=cl.course_id
		WHERE status=0
		ORDER BY cl.class_name";
		$result=DB_query($sql,$db);
		while ($myrow = DB_fetch_array($result)) {
	echo '<option value='. $myrow['id'] . '>' . $myrow['class_name'];
		} //end while loop
		DB_data_seek($result,0);
	echo '</select></td></tr></table>';
		echo '<table class=enclosed>';
echo "<br><div class='centre'><input  type='Submit' name='submit' value='" . _('Display Students') . "'>&nbsp;<input  type=submit action=RESET VALUE='" . _('Reset') . "'></div>";	
if (isset($_POST['submit'])) {
$_SESSION['class']=$_POST['student_class'];
echo '<table class=enclosed>';
	
echo '<tr><td>' . _('Search Student RegNo/Name') . ':<input type="Text" name="searchval" 
  size=30   maxlength=20></td>
		<td><input  type="submit" name="form1" value="Search"></td></tr>';
	
    echo '<tr><th>' . _('Name') . ':</th>
		<th>' . _('RegNo') . ':</th>';
		
  if (isset($_GET['pageno'])) {
   $pageno = $_GET['pageno'];
} else {
   $pageno = 1;
}
$sql = "SELECT count(*) FROM debtorsmaster";
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
$query_data = DB_fetch_row($result);
$numrows = $query_data[0];
			
$targetpage = "StudentPerClassReport.php";
$rows_per_page = 25;
$lastpage      = ceil($numrows/$rows_per_page);
$pageno = (int)$pageno;
if ($pageno > $lastpage) {
   $pageno = $lastpage;
} // if
$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;	
$SearchString = '%' . str_replace(' ', '%', $_POST['searchval']) . '%';
if (isset($_POST['form1'])){
$sql = "SELECT * FROM debtorsmaster
WHERE (debtorno LIKE  '". $SearchString."'
OR name LIKE  '". $SearchString."')
AND class_id='".$_SESSION['class']."'";
$DbgMsg = _('The SQL that was used to retrieve the information was');
$ErrMsg = _('Could not check whether the group is recursive because');
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);		
}		
else{
$sql = "SELECT COUNT(debtorno) FROM debtorsmaster
WHERE course_id='".$_SESSION['class']."'";
$result = DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$no_of_students=$myrow[0];

$sql = "SELECT * FROM debtorsmaster
WHERE class_id='".$_SESSION['class']."'
ORDER BY debtorno ";
$DbgMsg = _('The SQL that was used to retrieve the information was');
$ErrMsg = _('Could not check whether the group is recursive because');
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
}			
			while ($row = DB_fetch_array($result))
			{
			 if (($j%2)==1)
		    echo "<tr bgcolor=\"F0F0F0\">";
		  else
		    echo "<tr bgcolor=\"FFFFFF\">";
		  echo "<td class='visible'>".$row['name']."</td>";
		  echo "<td class='visible'>".$row['debtorno']."</td>";
		  
		    echo "</tr>";
		  $j++;
			}
	
}	
include('includes/footer.inc');
?>
