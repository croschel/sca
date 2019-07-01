

<?php    
//<!--script language="JavaScript" type="text/javascript">
// window.print();
//</script-->

	include "qrlib.php";

	$fig;// = $_GET['fig'];
   $PNG_WEB_DIR = 'temp/';
	$filename='qrcode/temp/'.$fig.'.png';

   $errorCorrectionLevel = 'L';
   if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    
	
	QRcode::png($fig , $filename, $errorCorrectionLevel, 4, 2);    
   
   //display generated file
/*for($i=1;$i <= $_GET['qtd']; $i++) {
 	echo "<table width='300' border='1'>";
	echo "<tr><td colspan='2' align='center'><font size='1'><i><b>Suprimento Proviniente da Desmontagem</td></tr>";
	echo "<tr><td rowspan='5'><img src=".$PNG_WEB_DIR.basename($filename)." /></td><td><font size=1><i><b>Vtr:</b> ".$_GET['eb']."</font></td></tr>";
	echo "<tr><td><font size='1'><i><b>NSN: </b>".$_GET['nsn']."<br>".$_GET['comp']."</td></tr>";
	echo "<tr><td><font size='1'><i><b>NS:</b> ".$_GET['ns']."</td></tr>";
	echo "<tr><td><font size='1'><i><b>Situação:</b> ".$_GET['sit']."</font></td></tr>";
 	echo "</table>";
	echo "<br>"; 
}  
*/
