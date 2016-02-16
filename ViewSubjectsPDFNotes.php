<?php
$PageSecurity = 7;
include('includes/session.inc');
$title = _('View Subjects PDF notes');
include('includes/header.inc');
echo '<p class="page_title_text">' . ' ' . $title . '';  
$sql = "SELECT SUM(totalinvoice) as total FROM invoice_items,salesorderdetails 
WHERE salesorderdetails.id=invoice_items.invoice_id
AND student_id='".$_SESSION['UserID']."'";
$DbgMsg = _('The SQL that was used to retrieve the information was');
$ErrMsg = _('Could not check whether the group is recursive because');
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
$row = DB_fetch_array($result);
$studenttotal = $row['total'];
$_SESSION['studenttotal']=$studenttotal;
			
$sql = "SELECT SUM(ovamount) as totalpayment FROM debtortrans WHERE debtorno='".$_SESSION['UserID']."'";
$DbgMsg = _('The SQL that was used to retrieve the information was');
$ErrMsg = _('Could not check whether the group is recursive because');
$result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
$row = DB_fetch_array($result);
$studenttotalpayment = -$row['totalpayment'];
$_SESSION['studenttotalpayment']=$studenttotalpayment;
$_SESSION['totalbalance']=$studenttotal-$studenttotalpayment;
			     
$sql = "SELECT class_id FROM debtorsmaster
WHERE debtorno=  '" . trim($_SESSION['UserID']) . "'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['class']=$myrow[0];

$sql = "SELECT course_id FROM classes
WHERE id=  '" . $_SESSION['class'] . "'";
$result=DB_query($sql,$db);
$myrow=DB_fetch_row($result);
$_SESSION['course']=$myrow[0];

echo '<table class=enclosed>';
$sql = "SELECT subpdf.* FROM subjectpdfnotes subpdf
INNER JOIN allowed_programs ap ON ap.subject_id=subpdf.subject_id
WHERE ap.program= '".$_SESSION['course']."' ORDER BY filename";
$result = DB_query($sql,$db);
if($_SESSION['studenttotal'] ==0){
$percantageBalance=0;
}
else
{
$percantageBalance=number_format($_SESSION['totalbalance']/$_SESSION['studenttotal']*100,0);
}
echo '<tr><td>'._('Fee balance is: ').$_SESSION['totalbalance']._(', Percentage balance : ').$percantageBalance.'</td></tr>';
if($percantageBalance < 51){
while ($row = DB_fetch_array($result))
{
	echo "<tr >";
	echo '<td class="visible"><a target="_blank" href="' . $rootpath .'/pdfnotes/'. $row['filename'] . '">' . $row['filename'] . '</a></td>';
	echo '</tr>';
}
}
else{
while ($row = DB_fetch_array($result))
{
	echo "<tr >";
	echo '<td class="visible">' . $row['filename'] . '</td>';
	echo '</tr>';
}
}
echo '</table>';
include('includes/footer.inc');
?>
