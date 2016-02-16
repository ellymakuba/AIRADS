<?php
$PageSecurity = 5;
include('includes/session.inc');
$title = _('Manage Payments');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
$sql = "SELECT fullaccess FROM www_users
WHERE userid=  '" . trim($_SESSION['UserID']) . "'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$administrator_rights=$myrow[0];
$msg='';
?>
<html><body><br /><br /><br />
<table width="80%"><form name="manageinvoice" action="ManageInvoices.php" method="post">
<?php
	echo "<form method='post' action=" . $_SERVER['PHP_SELF'] . '>';
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
	echo '<table border="1">';
	
  echo '<tr><td>' . _('Search InvoiceID') . ':<input type="Text" name="searchval" 
  size=30   maxlength=20></td>
		<td><input  type="submit" name="form1" value="submit"></td></tr>';
echo '<tr>';
echo '<th>'._('Action').'</th>';
echo '<th>'._('Invoice No').'</th><th>'._('Student').'</th><th>'._('Date').'</th></tr>';   
  if (isset($_GET['pageno'])) {
   $pageno = $_GET['pageno'];
} else {
   $pageno = 1;
}
$sql = "SELECT count(*) FROM salesorderdetails";
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
$query_data = DB_fetch_row($result);
$numrows = $query_data[0];
			
$targetpage = "ManageInvoices.php";
$rows_per_page = 25;
$lastpage      = ceil($numrows/$rows_per_page);
$pageno = (int)$pageno;
if ($pageno > $lastpage) {
   $pageno = $lastpage;
} // if
$limit = 'LIMIT ' .($pageno - 1) * $rows_per_page .',' .$rows_per_page;	
$SearchString = '%' . str_replace(' ', '%', $_POST['searchval']) . '%';
if (isset($_POST['form1'])){
$sql = "SELECT * FROM salesorderdetails
WHERE student_id LIKE  '". $SearchString."'
OR id LIKE  '". $SearchString."'";
$DbgMsg = _('The SQL that was used to retrieve the information was');
$ErrMsg = _('Could not check whether the group is recursive because');
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
}				
else{
$sql = "SELECT id,student_id,invoice_date 
FROM salesorderdetails 
ORDER BY id DESC $limit";
$DbgMsg = _('The SQL that was used to retrieve the information was');
$ErrMsg = _('Could not check whether the group is recursive because');
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
}			
while ($row = DB_fetch_array($result))
{
$ovamount=-$row['ovamount']; ?>
<td><?php 
if($administrator_rights ==8){
echo '<a  href="' . $rootpath . '/EditInvoice.php?invoiceNo=' . $row['id'].'">'._('Edit').'</a>';
}
?></td><?php
echo "<td>".$row['id']."</td>";
echo "<td>".$row['student_id']."</td>";
echo "<td>".$row['invoice_date']."</td>";		  
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
