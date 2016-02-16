<?php
$PageSecurity = 5;
include('includes/session.inc');
$title = _('Receipt Entry');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
$id=0;
$id = $_REQUEST['id'];
$msg='';
?>
<html><body><br /><br /><br />
<table class=enclosed><form name="payment" action="CustomerReceipt.php" method="post">
<?php
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
?>
<tr><td>Invoice ID:</td><td><input type="text"  name="invoice_id" value="<?php echo $id ?>" readonly=""/>
<?php 
if (isset($id)) {

$sql = "SELECT SUM(totalinvoice) as total FROM invoice_items 
		WHERE  invoice_id='".$id."'";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');

            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

            $row = DB_fetch_array($result);
			$invoice_total = $row['total'];
			
$sql = "SELECT * FROM salesorderdetails WHERE id=$id";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');

            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);

            $row = DB_fetch_array($result);
			$student_id = $row['student_id'];
	}
?>
<tr><td>Invoice total:</td><td><input type="text"  name="total_invoice" value="<?php echo $invoice_total ?>" readonly=""/>
<tr><td>Student:</td><td><input type="text"  name="student_id" value="<?php echo $student_id ?>" readonly=""/>
<?php
$sql = "SELECT SUM(ovamount) as paidsum FROM debtortrans
		WHERE transno='".$id."'";

            $DbgMsg = _('The SQL that was used to retrieve the information was');
            $ErrMsg = _('Could not check whether the group is recursive because');
            $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
			$row = DB_fetch_array($result);
			$paidsum=-$row['paidsum'];
			if($paidsum<=0)
			{
			$paidsum=0;
			}
?>
<tr><td>Prevoius Payments:</td><td><input type="text"  name="amount_paid" value="<?php echo $paidsum ?>" readonly=""/>
<tr><td>Bank Account:</td><td><select  name="account_code" ><option value=0> Select a Bank Account</option>
<?php 


	$SQL = "SELECT 
			bankaccounts.accountcode,
			bankaccounts.bankaccountname
		FROM bankaccounts,
			chartmaster
		WHERE bankaccounts.accountcode=chartmaster.accountcode
                AND invoice=1";
	$ErrMsg =_('The bank account name cannot be retrieved because');
	$result= DB_query($SQL,$db,$ErrMsg);
		while(list($accountcode, $accountname) = DB_fetch_row($result)) { 
		echo '<option value="' . $accountcode . '">' . $accountname . '</option>'; 
	}
		
?></select>
</td></tr>
<tr><td>Payment Method:</td><td><select  name="payment_method" >
<?php 
	include('includes/GetPaymentMethods.php');
/* The array ReceiptTypes is defined from the setup tab of the main menu under payment methods - the array is populated from the include file GetPaymentMethods.php */

foreach ($ReceiptTypes as $RcptType) {
	if (isset($_POST['ReceiptType']) and $_POST['ReceiptType']==$RcptType){
		echo "<option selected Value='$RcptType'>$RcptType";
	} else {
		echo "<option Value='$RcptType'>$RcptType";
	}
}
		
?></select>
</td></tr>
<tr><td>Amount:</td><td><input type="text"  name="amount" ></td></tr>
<tr><td>Date(D/M/Y):</td><td><input type="text"  name="payment_date"  class="date" >
<tr><td>Notes:</td><td><textarea name="notes"></textarea></td></tr>
</td></tr>
<tr><td><input type="submit"  name="payment" onClick="confirmation()" value="submit"></td></tr>
</form></table>
<?php

$_SESSION['DateBanked']= Date($_SESSION['DefaultDateFormat']);

$SQL = "SELECT currabrev FROM currencies,debtorsmaster WHERE 
		debtorsmaster.currcode=currencies.currabrev
		AND debtorsmaster.debtorno='" . $_POST['student_id']."'";
	$ErrMsg =_('The currency name cannot be retrieved because');
	$result= DB_query($SQL,$db,$ErrMsg);
	$row = DB_fetch_row($result);
	$currcode=$row[0]; 
	$_SESSION['Currency']=$currcode;
	
$PeriodNo = GetPeriod($_SESSION['DateBanked'],$db);
$_SESSION['payment_date']=$_POST['payment_date'];	
	
if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();
$InputError = 0;	
	
if (isset($_POST['payment'])) {

	$i=1;
	
	if (empty($_SESSION['payment_date'])) {
		$InputError = 1;
		prnMsg( _('Please enter a validate date'),'error');
		$Errors[$i] = 'payment_date';
		$i++;
	}
	else if(($_POST['account_code']) ==0 ){
		$InputError = 1;
		prnMsg( _('Please Select a Bank account'),'error');
		$Errors[$i] = 'account_code';
		$i++;
	}
	else if(($_POST['total_invoice']) < ($_POST['amount']+$_POST['amount_paid']) ){
		$InputError = 1;
		prnMsg( _('The total payments cannot exceed invoiced amount'),'error');
		$Errors[$i] = 'payment_date';
		$i++;
	}
	else if($InputError==0){
	$sql = "INSERT INTO debtortrans ( transno,type,debtorno,trandate,inputdate,prd,ovamount,addedby,invtext)
			VALUES ('".$_POST['invoice_id']."',12,'".$_POST['student_id']."','".FormatDateForSQL($_SESSION['payment_date'])."',
			'" . date('Y-m-d H-i-s') . "','".$PeriodNo."','".-$_POST['amount']."','" . trim($_SESSION['UserID']) . "','".$_POST['notes']."'
			)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	$sqltrans="SELECT LAST_INSERT_ID()";
	$resulttrans = DB_query($sqltrans,$db);
	$myrowtrans = DB_fetch_row($resulttrans);
	$transid = $myrowtrans[0]; 
	
	mysql_connect("localhost", "elly", "masinde");
	mysql_select_db("cmc") or die(mysql_error());
	$query="INSERT INTO gltrans (type,
					typeno,
					trandate,
					periodno,
					account,
					narrative,
					amount)
				VALUES (
					12,
					'" .$_POST['invoice_id'] . "',
					'".FormatDateForSQL($_POST['payment_date'])."',
					'" . $PeriodNo . "',
					'" . $_POST['account_code']. "',
					'" . $_POST['notes'] . "',
					'" . $_POST['amount'] . "'
				)";
			$result = mysql_query($query);
			
			
			$query="INSERT INTO gltrans ( type,
					typeno,
					trandate,
					periodno,
					account,
					narrative,
					amount)
			VALUES (
				12,
					'" .$_POST['invoice_id'] . "',
					'".FormatDateForSQL($_SESSION['payment_date'])."',
					'" . $PeriodNo . "',
					1100,
					'" . $_POST['notes'] . "',
					'" .-$_POST['amount'] . "'
				)";
			$result = mysql_query($query);
			mysql_close($result);
			
			
			$SQL="INSERT INTO banktrans (type,
					transno,
					bankact,
					ref,
					exrate,
					functionalexrate,
					transdate,
					banktranstype,
					amount,
					inputdate,
					addedby,
					currcode,
					studenttransid)
				VALUES (
					12,
					'".$_POST['invoice_id']."',
					'" . $_POST['account_code']. "',
					'" . $_POST['notes'] . "',
					1,
					1,
					'".FormatDateForSQL($_SESSION['payment_date'])."',
					'" .$_POST['payment_method']  . "',
					'" .$_POST['amount']  . "',
					'" . date('Y-m-d') . "',
					'" . trim($_SESSION['UserID']) . "',
					'" . $_SESSION['Currency'] . "',
					'$transid'
				)";
			$DbgMsg = _('The SQL that failed to insert the bank account transaction was');
			$ErrMsg = _('Cannot insert a bank transaction');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			
	 echo "<meta http-equiv='Refresh' content='0; url=" . $rootpath ."/ManagePayments.php". "'>";

			echo '<div class="centre">' . _('You should automatically be forwarded to the Manage Payment Page') .
			'. ' . _('If this does not happen') .' (' . _('if the browser does not support META Refresh') . ') ' .
			"<a href='" . $rootpath . "/ManagePayments.php". '.</div>';
			}
	}
include ('includes/GLPostings.inc');	
include('includes/footer.inc');
?>
