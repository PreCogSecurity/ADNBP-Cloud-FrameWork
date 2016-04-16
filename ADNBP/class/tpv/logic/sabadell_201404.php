<HTML>
<HEAD>
  <TITLE>PHP de ejemplo</TITLE>
</HEAD>
<BODY>

<?PHP
// If form is submitted with all required data then show the form
// else show error page
empty($Formulario) ?                            
      ShowForm($Ds_Merchant_Amount,$Ds_Merchant_Currency,$prod) :
      ShowError();
exit;
?>

<?PHP


function ShowError () {
    echo "<html><head><title>Results</title></head><body><table width=100% height=50%><tr><td><p><h2><center>Compruebe que todos los datos del formulario son correctos!!</center></h2></p></td></tr></table></body></html>\n";
} # End of function ShowError

function ShowForm ($amount,$currency,$producto) {
// Posted data
global $HTTP_POST_VARS;

// Valores constantes del comercio
$url_tpvv='https://sis-t.redsys.es:25443/sis/realizarPago';
$clave='qwertyasdf0123456789';
$name='Comercio Pruebas';
$code='999008881';
$terminal='6';
$order=date('ymdHis');
$amount='25';
$currency='978';
$transactionType='0';
$urlMerchant='http://www.redsys.es';
$producto='Zapatos';

// Now, print the HTML script
echo "<html><head><title>Comercio Simulador</title></head>
<script language=JavaScript>
function calc() { 
vent=window.open('','tpv','width=725,height=600,scrollbars=no,resizable=yes,status=yes,menubar=no,location=no');
document.forms[0].submit();}
</script>
<body bgcolor=white>
<form name=compra action=$url_tpvv method=post target=tpv>
<pre>
<table>
<tr><td>
<h2>Comercio de prueba.</h2>
</td></tr><tr><td>
Comercio: <font color=blue>$name</font>
</td></tr><tr><td>
FUC: <font color=blue>$code</font>
</td></tr><tr><td>
Terminal: <font color=blue>$terminal</font>
</td></tr><tr><td>
Pedido: <font color=blue>$order</font>
</td></tr><tr><td>
Producto: <font color=blue>$producto</font>
</td></tr><tr><td>
Importe: <font color=blue>$amount</font>
</td></tr><tr><td>
Tipo de Operacion: <font color=blue>$transactionType (Autorizaci�n)</font>
</td></tr><tr><td>
URL del comercio: <font color=blue>$urlMerchant</font>
</td></tr><tr><td>";

// Currency strings 
if ($currency == "978") {
    echo "Moneda: <font color=blue>Euros</font>";
}

echo "</td>
</tr><tr><td>
<input type=hidden name=Ds_Merchant_Amount value='$amount'>
</td></tr><tr><td>
<input type=hidden name=Ds_Merchant_Currency value='$currency'>
</td></tr><tr><td>
<input type=hidden name=Ds_Merchant_Order  value='$order'>
</td></tr><tr><td>
<input type=hidden name=Ds_Merchant_MerchantCode value='$code'>
</td></tr><tr><td>
<input type=hidden name=Ds_Merchant_Terminal value='$terminal'>
</td></tr><tr><td>
<input type=hidden name=Ds_Merchant_TransactionType value='$transactionType'>
</td></tr><tr><td>
<input type=hidden name=Ds_Merchant_MerchantURL value='$urlMerchant'>
</td></tr><tr><td>";

// Compute hash to sign form data
// $signature=sha1_hex($amount,$order,$code,$currency,$clave);
$message = $amount.$order.$code.$currency.$clave;
$signature = strtoupper(sha1($message));

echo "<input type=hidden name=Ds_Merchant_MerchantSignature value='$signature'>
</td></tr>
</table>
<center><a href='javascript:calc()'><img src='/tpvirtual.jpg' border=0 ALT='TPV Virtual'></a></center>
</pre>
</form>                                       
</body></html>";
} # End of function ShowForm
?>