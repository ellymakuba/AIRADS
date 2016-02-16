<?php
$PageSecurity = 2;
include('includes/session.inc');
$title = _('Manage Students');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
$msg='';
echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table class=enclosed>';
	
echo '<tr><td>' . _('Search Student RegNo/Name') . ':<input type="Text" name="searchval" 
  size=30   maxlength=20></td>
		<td><input  type="submit" name="form1" value="submit"></td></tr>';
	
    echo '<tr><th>' . _('View') . ':</th>
		<th>' . _('Bill') . ':</th>
		<th>' . _('Edit') . ':</th>
		<th>' . _('Report Card') . ':</th>
		<th>' . _('Transcript') . ':</th>
		<th>' . _('Name') . ':</th>
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
			
$targetpage = "SelectCustomer.php";
$rows_per_page = 10;
$lastpage      = ceil($numrows/$rows_per_page);
$pageno = (int)$pageno;
if ($pageno > $lastpage) {
   $pageno = $lastpage;
} // if
$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;	
$SearchString = '%' . str_replace(' ', '%', $_POST['searchval']) . '%';
if (isset($_POST['form1'])){
$sql = "SELECT * FROM debtorsmaster
		WHERE debtorno LIKE  '". $SearchString."'
		OR name LIKE  '". $SearchString."'
		";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');
            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
			
			
}		
else{
$sql = "SELECT * FROM debtorsmaster
		ORDER BY debtorno $limit";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');
            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
}			
			while ($row = DB_fetch_array($result))
			{
		    echo "<tr>";			
	echo '<td class="visible"><a  href="' .'StudentStatements.php? &debtorno=' . $row['debtorno'].'">'._('View Statement').'</a></td>';
	echo '<td class="visible"><a  href="' .'StudentBilling.php? &debtorno=' . $row['debtorno'].'">'._('Bill Student').'</a></td>';
	echo '<td class="visible"><a href="' . $rootpath .'/Students.php?&DebtorNo=' . $row['debtorno'] . '">' . _('Edit Student') . '</a></td>';
	echo '<td class="visible"><a href="' . $rootpath .'/ReportCard.php?&debtorno=' . $row['debtorno'] . '">' . _('Print ReportCard') . '</a></td>';
	echo '<td class="visible"><a href="' . $rootpath .'/Transcript.php?&debtorno=' . $row['debtorno'] . '">' . _('Print Transcript') . '</a></td>';
		  echo "<td class=\"visible\">".$row['name']."</td>";
		  echo "<td class=\"visible\">".$row['debtorno']."</td>";
		  
		    echo "</tr>";
		  $j++;
			}
			

if ($pageno == 1) {
   echo "<tr><td>"." FIRST PREV ";
} else {
   echo " <a href='{$_SERVER['PHP_SELF']}?pageno=1'>FIRST</a> ";
   $prevpage = $pageno-1;
   echo " <a href='{$_SERVER['PHP_SELF']}?pageno=$prevpage'>PREV</a> ";
}
echo " ( Page $pageno of $lastpage ) ";
if ($pageno == $lastpage) {
   echo " NEXT LAST "."</td></tr>";
} else {
   $nextpage = $pageno+1;
   echo " <a href='{$_SERVER['PHP_SELF']}?pageno=$nextpage'>NEXT</a> ";
   echo " <a href='{$_SERVER['PHP_SELF']}?pageno=$lastpage'>LAST</a> ";
}
			
include('includes/footer.inc');
?>
