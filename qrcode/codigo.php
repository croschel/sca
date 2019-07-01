<script language="JavaScript" type="text/javascript">
 window.print();
</script>
<?php    
	include "qrlib.php";

	$fig = str_replace(" ", "",$fig = str_replace("/", "", $_GET['fig']));
   $PNG_WEB_DIR = 'temp/';
	$filename='temp/'.$fig.'.png';
	if(($_GET['val']=='00/00/0000') or ($_GET['val']=='//')) {
		$val='Indeterminado';
	} else {
		$val=$_GET['val'];
	}
   $errorCorrectionLevel = 'L';
   if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    
	
	QRcode::png($fig , $filename, $errorCorrectionLevel, 4, 2);    
   
   //display generated file
for($i=1;$i <= $_GET['qtd']; $i++) {
 	echo "<table width='300' border=1>";
	echo "<tr><td rowspan='5'><img src=".$PNG_WEB_DIR.basename($filename)." /></td><td><font size=1><i>NSN: ".$_GET['nsn']."</td></tr>";
	echo "<tr><td><font size=1><i>".$_GET['comp']."</td></tr>";
	echo "<tr><td><font size=1><i>Doc Entrada: ".$_GET['doc']."</font></td></tr>";
	echo "<tr><td><font size=1><i>Data Entrada: ".$_GET['ent']."</font></td></tr>";
	echo "<tr><td><font size=1><i>Data Validade: ".$val."</font></td></tr>";
 	echo "</table>";
	echo "<br>"; 
}  
