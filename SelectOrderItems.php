<?php
include('includes/DefineCartClass.php');
$PageSecurity = 5;
include('includes/session.inc');
if (isset($_GET['invoiceNo'])) {
	$title = _('Modifying Invoice') . ' ' . $_GET['invoiceNo'];
} else {
	$title = _('Select Invoice Items');
}
include('includes/header.inc');
include('includes/GetPrice.inc');
include('includes/SQL_CommonFunctions.inc');

?>
<html><body><br /><br /><br />
<?php
if (isset($_GET['invoiceNo'])) { 

$sql="SELECT * FROM salesorderdetails WHERE id='".$_GET['invoiceNo']."'";
	$result=DB_query($sql,$db);
	$myrow=DB_fetch_array($result);

?>

<table><form name="form1" method="post" action="">
<?php
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

$sql="SELECT invoice_items.*
		FROM invoice_items,salesorderdetails
		WHERE  invoice_items.invoice_id = salesorderdetails.id
		AND  salesorderdetails.id ='" . $_GET['invoiceNo'] . "'
		ORDER BY  salesorderdetails.id";
$result=DB_query($sql,$db);

// Count table rows
$count=DB_num_rows($result);
?>
<table width="500" border="0" cellspacing="1" cellpadding="0">

<tr>
<td>
<table width="500" border="0" cellspacing="1" cellpadding="0">


<tr>
<td align="center"><strong>Id</strong></td>
<td align="center"><strong>Invoice_id</strong></td>
<td align="center"><strong>Product_id</strong></td>
<td align="center"><strong>Quantity</strong></td>
<td align="center"><strong>unitprice</strong></td>
</tr>
<?php
while($rows=DB_fetch_array($result)){
?>
<tr>
<td align="center"><?php $id[]=$rows['id']; ?><?php echo $rows['id']; ?></td>
<td align="center"><input name="invoice_id[]" type="text"  value="<?php echo $rows['invoice_id']; ?>"></td>
<td align="center"><input name="Product_id[]" type="text"  value="<?php echo $rows['product_id']; ?>"></td>
<td align="center"><input name="qty[]" type="text"  value="<?php echo $rows['qty']; ?>"></td>
<td align="center"><input name="unitprice[]" type="text"  value="<?php echo $rows['unitprice']; ?>"></td>
</tr>

<?php

}
?>
<tr>
<td colspan="4" align="center"><input type="submit" name="Submit" value="Submit"></td>
</tr>
</table>
</td>
</tr>
</form>
</table>
<?php
// Check if button name "Submit" is active, do this
if (isset($_POST['Submit'])){
$unitprice= $_POST['unitprice'];
$qty= $_POST['qty'];
$productid= $_POST['Product_id'];
$i=0;
$linetotal=0;
while($i<$count){
$sql="UPDATE invoice_items SET qty='" .$qty[$i]. "', unitprice='" .$unitprice[$i]. "',totalinvoice='" .$qty[$i]*$unitprice[$i]. "'
WHERE id='$id[$i]'";
$ErrMsg = _('The customer could not be updated because');
$result = DB_query($sql,$db,$ErrMsg);
prnMsg( _('Customer updated'),'success');
$i++;
}
}
}




else{
?>
<table><form name="invoice" action="SelectOrderItems.php" method="post" id="studentinvoice">
<?php
	echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';
?>
	<tr><td>Student:<select  name="student_id" ><option value=0> Select a Student</option>
	<?php
	$result=DB_query('SELECT debtorno, name FROM debtorsmaster ORDER BY debtorno',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['debtorno'] . '">' . $myrow['debtorno'] . '</option>'; 
	}
$DateString = Date($_SESSION['DefaultDateFormat']);		
?>

</select></td></tr><tr><td>Date(D/M/Y):<input type="text" class="date"  name="invoice_date" >
<?php 
$SQL_ClientSince = FormatDateForSQL($_POST['invoice_date']);
?>
</td></tr>
<tr><td><table><tr><th></th><th colspan="3">Item:</th><th>Quantity:</th><th>Unit Price:</th></tr>
<tr><td colspan="3">Product 1:</td><td><select  name="stock_id1" ><option value=0> Select a Product</option>
<?php 
$result=DB_query('SELECT stockid, description FROM stockmaster',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['stockid'] . '">' . $myrow['description'] . '</option>'; 
	}
?></select></td><td><input type="text" name="item_quantity1" /></td><td><input type="text" name="item_unitprice1" /></td></tr>
<tr><td colspan="3">Product 2:</td><td><select  name="stock_id2" ><option value=0> Select a Product</option>
<?php 
$result=DB_query('SELECT stockid, description FROM stockmaster',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['stockid'] . '">' . $myrow['description'] . '</option>'; 
	}
?>
</td><td><input type="text" name="item_quantity2" /></td><td><input type="text" name="item_unitprice2" /></td></tr>
<tr><td colspan="3">Product 3:</td><td><select  name="stock_id3" ><option value=0> Select a Product</option>
<?php 
$result=DB_query('SELECT stockid, description FROM stockmaster',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['stockid'] . '">' . $myrow['description'] . '</option>'; 
	}
?>
</td><td><input type="text" name="item_quantity3" /></td><td><input type="text" name="item_unitprice3" /></td></tr>
<tr><td colspan="3">Product 4:</td><td><select  name="stock_id4" ><option value=0> Select a Product</option>
<?php 
$result=DB_query('SELECT stockid, description FROM stockmaster',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['stockid'] . '">' . $myrow['description'] . '</option>'; 
	}
?>
</td><td><input type="text" name="item_quantity4" /></td><td><input type="text" name="item_unitprice4" /></td></tr>
<tr><td colspan="3">Product 5:</td><td><select  name="stock_id5" ><option value=0> Select a Product</option>
<?php 
$result=DB_query('SELECT stockid, description FROM stockmaster',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['stockid'] . '">' . $myrow['description'] . '</option>'; 
	}
?>
</td><td><input type="text" name="item_quantity5" /></td><td><input type="text" name="item_unitprice5" /></td></tr>
<tr><td colspan="3">Product 6:</td><td><select  name="stock_id6" ><option value=0> Select a Product</option>
<?php 
$result=DB_query('SELECT stockid, description FROM stockmaster',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['stockid'] . '">' . $myrow['description'] . '</option>'; 
	}
?>
</td><td><input type="text" name="item_quantity6" /></td><td><input type="text" name="item_unitprice6" /></td></tr>
<tr><td colspan="3">Product 7:</td><td><select  name="stock_id7" ><option value=0> Select a Product</option>
<?php 
$result=DB_query('SELECT stockid, description FROM stockmaster',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['stockid'] . '">' . $myrow['description'] . '</option>'; 
	}
?>
</td><td><input type="text" name="item_quantity7" /></td><td><input type="text" name="item_unitprice7" /></td></tr>
<tr><td colspan="3">Product 8:</td><td><select  name="stock_id8" ><option value=0> Select a Product</option>
<?php 
$result=DB_query('SELECT stockid, description FROM stockmaster',$db);
	while($myrow = DB_fetch_array($result)) { 
		echo '<option value="' . $myrow['stockid'] . '">' . $myrow['description'] . '</option>'; 
	}
	$invoice_total=($_POST['item_quantity1']*$_POST['item_unitprice1'])+($_POST['item_quantity2']*$_POST['item_unitprice2'])+($_POST['item_quantity3']*$_POST['item_unitprice3'])+($_POST['item_quantity4']*$_POST['item_unitprice4'])+($_POST['item_quantity5']*$_POST['item_unitprice5'])+($_POST['item_quantity6']*$_POST['item_unitprice6'])+($_POST['item_quantity7']*$_POST['item_unitprice7'])+($_POST['item_quantity8']*$_POST['item_unitprice8']);
	
?>
</td><td><input type="text" name="item_quantity8" /></td><td><input type="text" name="item_unitprice8" /></td></tr>
</table >
</td></tr>
<tr><td><input type="submit" name="submit" value="submit"/></td></tr>

<?php
if (!Is_Date($_SESSION['DateBanked'])){
	$_SESSION['DateBanked']= Date($_SESSION['DefaultDateFormat']);
	 
}

if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();
$InputError = 0;

if (isset($_POST['submit'])) {
$PeriodNo = GetPeriod($_SESSION['DateBanked'],$db);

	$i=1;
	
	if (empty($_POST['invoice_date'])) {
		$InputError = 1;
		prnMsg( _('Please enter a valid date'),'error');
		$Errors[$i] = 'invoice_date';
		$i++;
	}
	if(is_numeric($_POST['student_id']) && $_POST['student_id'] ==0){
		$InputError = 1;
		prnMsg( _('Please Select a Student'),'error');
		$Errors[$i] = 'student_id';
		$i++;
	}
	else if($InputError==0){
$sql = "INSERT INTO salesorderdetails ( student_id,invoice_date,transactiondate,addedby)
										VALUES ('".$_POST['student_id']."',
												'".$SQL_ClientSince."',
												'" . date('Y-m-d H-i-s') . "',
												'" . trim($_SESSION['UserID']) . "'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	$sql="SELECT LAST_INSERT_ID()";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	$id = $myrow[0];
	
	$invoice_total1=($_POST['item_quantity1'])*($_POST['item_unitprice1']);
	$invoice_total2=($_POST['item_quantity2'])*($_POST['item_unitprice2']);
	$invoice_total3=($_POST['item_quantity3'])*($_POST['item_unitprice3']);
	$invoice_total4=($_POST['item_quantity4'])*($_POST['item_unitprice4']);
	$invoice_total5=($_POST['item_quantity1'])*($_POST['item_unitprice5']);
	$invoice_total6=($_POST['item_quantity6'])*($_POST['item_unitprice6']);
	$invoice_total7=($_POST['item_quantity7'])*($_POST['item_unitprice7']);
	$invoice_total8=($_POST['item_quantity8'])*($_POST['item_unitprice8']);
	
	
	$sql = "INSERT INTO invoice_items ( invoice_id,product_id,
															qty,
															unitprice,
															totalinvoice
															)
										VALUES ('".$id."',
												'".$_POST['stock_id1']."',
												'".$_POST['item_quantity1']."',
												'".$_POST['item_unitprice1']."',
												'".$invoice_total1."'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	if($invoice_total2 > 0){
	$sql = "INSERT INTO invoice_items ( invoice_id,product_id,
															qty,
															unitprice,
															totalinvoice
															)
										VALUES ('".$id."',
												'".$_POST['stock_id2']."',
												'".$_POST['item_quantity2']."',
												'".$_POST['item_unitprice2']."',
												'".$invoice_total2."'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	}
	
	if($invoice_total3 > 0){
	$sql = "INSERT INTO invoice_items ( invoice_id,product_id,
															qty,
															unitprice,
															totalinvoice
															)
										VALUES ('".$id."',
												'".$_POST['stock_id3']."',
												'".$_POST['item_quantity3']."',
												'".$_POST['item_unitprice3']."',
												'".$invoice_total3."'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	}
	
	if($invoice_total4 > 0){
	$sql = "INSERT INTO invoice_items ( invoice_id,product_id,
															qty,
															unitprice,
															totalinvoice
															)
										VALUES ('".$id."',
												'".$_POST['stock_id4']."',
												'".$_POST['item_quantity4']."',
												'".$_POST['item_unitprice4']."',
												'".$invoice_total4."'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	}
	
	if($invoice_total5 > 0){
	$sql = "INSERT INTO invoice_items ( invoice_id,product_id,
															qty,
															unitprice,
															totalinvoice
															)
										VALUES ('".$id."',
												'".$_POST['stock_id5']."',
												'".$_POST['item_quantity5']."',
												'".$_POST['item_unitprice5']."',
												'".$invoice_total5."'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	}
	
	if($invoice_total6 > 0){
	$sql = "INSERT INTO invoice_items ( invoice_id,product_id,
															qty,
															unitprice,
															totalinvoice
															)
										VALUES ('".$id."',
												'".$_POST['stock_id6']."',
												'".$_POST['item_quantity6']."',
												'".$_POST['item_unitprice6']."',
												'".$invoice_total6."'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	}
	
	if($invoice_total7 > 0){
	$sql = "INSERT INTO invoice_items ( invoice_id,product_id,
															qty,
															unitprice,
															totalinvoice
															)
										VALUES ('".$id."',
												'".$_POST['stock_id7']."',
												'".$_POST['item_quantity7']."',
												'".$_POST['item_unitprice7']."',
												'".$invoice_total7."'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	}
	
	
	if($invoice_total8 > 0){
	$sql = "INSERT INTO invoice_items ( invoice_id,product_id,
															qty,
															unitprice,
															totalinvoice
															)
										VALUES ('".$id."',
												'".$_POST['stock_id8']."',
												'".$_POST['item_quantity8']."',
												'".$_POST['item_unitprice8']."',
												'".$invoice_total8."'
												)";
	$DbgMsg = _('The SQL that failed was');
	$ErrMsg = _('Unable to add the quotation line');
	$Ins_LineItemResult = DB_query($sql,$db,$ErrMsg,$DbgMsg,true);
	
	 ?>
	</form></table>
	<?php
	
	
    // prevent resending data
    echo "<meta http-equiv='Refresh' content='0; url=" . $rootpath ."/CustomerReceipt.php?"."&id=" . $id. "'>";

			echo '<div class="centre">' . _('You should automatically be forwarded to the Payment Page') .
			'. ' . _('If this does not happen') .' (' . _('if the browser does not support META Refresh') . ') ' .
			"<a href='" . $rootpath . "/CustomerReceipt.php?"."&id=" . $id. '.</div>';
			}
	}
	}
?>
<?php
include('includes/footer.inc');
?>