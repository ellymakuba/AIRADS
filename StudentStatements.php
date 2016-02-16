<?php

/* $Id: CustomerReceipt.php 3868 2010-09-30 14:53:59Z tim_schofield $ */
/* $Revision: 1.46 $ */
ob_start();
$PageSecurity = 3;
include('includes/session.inc');

$title = _('Receipt Entry');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

$debtorno = $_REQUEST['debtorno'];
$msg='';
?>
<html><body><br /><br /><br />
<table class=enclosed><form name="payment" action="CustomerReceipt.php" method="post">
<?php
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
?>
<?php 
$sql = "SELECT SUM(totalinvoice) as total FROM invoice_items,salesorderdetails 
		WHERE salesorderdetails.id=invoice_items.invoice_id
		AND student_id='".$debtorno."'";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');

            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

            $row = DB_fetch_array($result);
			$studenttotal = $row['total'];
			
$sql = "SELECT SUM(ovamount) as totalpayment FROM debtortrans WHERE debtorno='".$debtorno."'";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');

            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

            $row = DB_fetch_array($result);
			$studenttotalpayment = -$row['totalpayment'];
			$totalbalance=$studenttotal-$studenttotalpayment;
$sql = "SELECT debtorno,name FROM debtorsmaster WHERE debtorno='".$debtorno."'";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');

            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

            $row = DB_fetch_array($result);
			$student_id = $row['debtorno'];
			$name = $row['name'];
	
?>
<tr><td>Student Name:</td><td><?php echo $name  ?></td><td>Total Invoices:</td><td><?php echo $studenttotal  ?></td></tr>
<tr><td>Student RegNo:</td><td><?php echo $student_id  ?></td><td>Total Payments:</td><td><?php echo $studenttotalpayment  ?></td></tr>
<tr><td><?php echo $name  ?>'s Invoices</td><td>Balance:</td><td><?php echo $totalbalance  ?></td></tr>
<tr><th>ID</th><th>Total</th><th>Paid</th><th>Owing</th><th>Date</th></tr>
<?php
$sql = "SELECT	
		s.id, 
		s.invoice_date,
		(SELECT sum( COALESCE(ii.totalinvoice, 0)) FROM invoice_items ii WHERE ii.invoice_id=s.id) As invd,
		(SELECT sum( COALESCE(dt.ovamount, 0)) FROM debtortrans dt where dt.transno = s.id) As pmt,
		(SELECT COALESCE(invd, 0)) As total, 
		(SELECT COALESCE(pmt, 0)) As paid, 
		(select (total - paid)) as owing 
	FROM 
		salesorderdetails s 
	WHERE 
		s.student_id ='".$debtorno."'
	ORDER BY 
		s.id DESC;";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');
            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
			while ($row = DB_fetch_array($result))
			{
		    echo "<tr>";
			$ovamount=-$row['paid'];
			$balance=$row['total']-$ovamount;
		  echo "<td>"."<a href ='" . $rootpath ."/CustomerReceipt.php?"."&id=" . $row['id']. "'>".	          $row['id']."</a>"."</td>";
		  echo "<td>".$row['total']."</td>";
		  echo "<td>".$ovamount."</td>";
		  echo "<td>".$balance."</td>";
		  echo "<td>".$row['invoice_date']."</td>";
		    echo "</tr>";
		  $j++;
			} ?>
</table>
<table class=enclosed>
<tr><td><?php echo $name  ?>'s Payments</td></tr>
<tr><th>Receipt NO</th><th>Amount</th><th>Date</th></tr>
<?php
$sql = "SELECT * FROM debtortrans
		WHERE debtorno='".$debtorno."'
		ORDER BY id DESC";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');
            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
			while ($row = DB_fetch_array($result))
			{
		    echo "<tr>";
			$ovamount=-$row['ovamount'];
			$balance=$row['totalinvoice']-$ovamount;
		   echo "<td>"."<a href ='" . $rootpath ."/PDFReceipt.php?"."&ReceiptNumber=" . $row['id']. "'>".	          $row['id']."</a>"."</td>";
		  echo "<td>".$ovamount."</td>";
		  echo "<td>".$row['trandate']."</td>";
		    echo "</tr>";
		  $j++;
			} ?>
			</table><?php
include('includes/footer.inc');
?>
