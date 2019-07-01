
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>



<?php    
	/*Conexão ao Banco de Dados*/
	$conexao = mysql_connect("localhost", "root", "" );
	$db = mysql_select_db("sca",$conexao);
  /*Seleciona a relação de Unidades*/
include "configuracao.php";
echo "<table border='0' align='center' valign='top'>";

  $query = "SELECT d.reservatorio_codigo, d.om_codom, d.qtd_atual, om.sigla, om.oc 
					FROM  `disponibilidade` d, om
					WHERE om.codom=d.om_codom
					and d.qtd_atual >0
					ORDER BY om.oc, om.sigla";
   $result = mysql_query($query);

   while ($disp = mysql_fetch_array($result, MYSQL_NUM)) {
   	$qryoc = "select sigla from om where codom='$disp[4]'";
   	$resoc = mysql_query($qryoc);
   	$oc = mysql_fetch_row($resoc);
   	$query1 = "Select r.combustivel_codigo, r.nr_nota_fiscal, c.om_codom, r.reservatorio_codigo, c.qtd_atual, c.qtd_destinada, r.dt_rec, om.sigla 
   				FROM recebimento r, cotas c, om
   				WHERE r.combustivel_codigo = c.recebimento_combustivel_codigo
   				and r.nr_nota_fiscal = c.recebimento_nr_nota_fiscal
   				and om.codom=c.om_codom
   				and c.om_codom = $disp[1]
   				and r.reservatorio_codigo = $disp[0]
   				order by r.dt_rec desc";
   	$result1 = mysql_query($query1);
   	echo "<tr bgcolor='$cor2'><td colspan='7'>OC: $oc[0]</td></tr>";
   	echo "<tr bgcolor='$cor3'><td>OM: $disp[3]</td><td colspan='7'>Disponibilidade: $disp[2] </td></tr>";
	   echo "<tr><td>Reservatorio</td><td>Combustivel</td><td>Nota Fiscal</td><td>Codom</td><td>Dt Rec</td><td>Qtd Dest</td><td>Qtd Atual</td></tr>";
	   $soma=0;
  		while ($cota = mysql_fetch_array($result1, MYSQL_NUM)) {
   		echo "<tr><td>$cota[3]</td><td>$cota[0]</td><td>$cota[1]</td><td>$cota[2] - $cota[7]</td><td>$cota[6]</td><td>$cota[5]</td><td>$cota[4]</td></tr>";
	   	$soma=$soma+$cota[4];
  			if($disp[2]>$cota[4]){
				$disp[2] = $disp[2] - $cota[4];
 			} else {
				$qryAlt = $qryAlt."<br> update cotas set qtd_atual='$disp[2]' where recebimento_combustivel_codigo='$cota[0]' and recebimento_nr_nota_fiscal='$cota[1]' and om_codom='$cota[2]';";
				$disp[2] = 0;
				$nr++;
 			}
			/*$result = mysql_query($qralt);		*/	
			$om = $cota[7];
 		}
		echo "<tr><td colspan='7'>$qryAlt</td></tr>";
		echo "<tr bgcolor='$cor3'><td>OM: $om</td><td colspan='6'>Soma Qtd Atual: $soma</td></tr>";
		$qryAlt='';
		echo "<tr><td colspan='7'>&nbsp;</td></tr>";

	}
echo "</table>";
echo "<br>Total: $nr registros alterados";
?>


