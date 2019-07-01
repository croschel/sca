<script language="JavaScript" type="text/javascript">
 //window.print();
</script>
<?php    
	include "qrlib.php";
	$fig = $_GET['fig'];
   $PNG_WEB_DIR = 'temp/';
	$filename='temp/'.$fig.'.png';
   $errorCorrectionLevel = 'L';
   if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    
	
	QRcode::png($fig , $filename, $errorCorrectionLevel, 4, 2);    
   
   //display generated file
 	echo "<table width='300' border=1>";
	echo "<tr><td rowspan='5' align='center'><img src=".$PNG_WEB_DIR.basename($filename)." /></td><td><font size=2><i>OM: ".$_GET['om']."</td></tr>";
	echo "<tr><td><font size=2><i>Codom: ".$_GET['codom']."</td></tr>";
	echo "<tr><td><font size=2><i>".$_GET['dep']."</td></tr>";
	echo "<tr><td><font size=2><i>".$_GET['loc']."</td></tr>";
 	echo "</table>";
	echo "<br>"; 
 
