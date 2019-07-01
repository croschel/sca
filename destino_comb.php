<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" >
   <link rel="stylesheet" type="text/css" href="css/estilos.css">
</head>

<?php
include "conexao.php";
include "configuracao.php";
echo "<body bgcolor='$cor_fundo'>";
 $qry="SELECT h.dt_abastecimento, h.viatura_eb, h.qtd_abastecida, om.sigla, a.tp_abastecimento 
			FROM om, viatura v, abastecimento a, historico_abastecimento h
			where om.codom=v.om_codom
				and v.eb=a.viatura_eb
				and a.dt_abastecimento=h.dt_abastecimento
				and a.viatura_eb=h.viatura_eb
				and h.nr_repasse='".$_GET['rep']."'
			 	and h.om_codom='".$_GET['codom']."'
			order by 1 ";
$dest=mysql_query($qry);		  
			  
echo "<table border='0' width='100%' align='left' valign='top'>";
echo "<tr align='center'>
			<td colspan='5'><b><i>Destino do Combustível do Repasse nº ".$_GET['rep']." de ".substr($_GET['dt'],8,2)."/".substr($_GET['dt'],5,2)."/".substr($_GET['dt'],0,4)." a(o) ".$_GET['om']."</td>
		</tr>";
echo "<tr align='center'>
			<td colspan='5'><b><i>Relação de Abastecimentos</td>
		</tr>";
echo "<tr bgcolor='$cor2' align='center'>
			<td><b><i>Data e Hora</td>
			<td><b><i>Viatura</td>
			<td><b><i>OM</td>
			<td><b><i>Qtd</td>
			<td><b><i>Tipo Abastecimento</td>
		</tr>";
$soma=0;
while ($row = mysql_fetch_array($dest, MYSQL_NUM)) {
	echo "<tr align='center' bgcolor='$cor3' class='marcar_linha'>
				<td>".substr($row[0],8,2)."/".substr($row[0],5,2)."/".substr($row[0],0,4)." ".substr($row[0],11,5)."</td>	
				<td>$row[1]</td>	
				<td>$row[3]</td>	
				<td align='right'>". number_format($row[2], 0, ',', '.')." lts</td>	
				<td>$row[4]</td>	
			</tr>";
			$soma = $soma + $row[2];
}			
echo "<tr bgcolor='$cor3' align='right'>
			<td colspan='3'><b><i>Total:&nbsp;</td><td><b><i>". number_format($soma, 0, ',', '.')." lts</td><td></td>
		</tr>";
echo "<tr bgcolor='' align='center'>
			<td colspan='5'><b><i>&nbsp;</td>
		</tr>";

echo "<tr bgcolor='' align='center'>
			<td colspan='2'><b><i>Registro de Evaporação</td>
		</tr>";
echo "<tr bgcolor='$cor2' align='center'>
			<td><b><i>Data</td>
			<td><b><i>Qtd</td>
		</tr>";
 $qry="SELECT data_registro, qtd_evaporada 
			  FROM evaporacao
			  where nr_repasse='".$_GET['rep']."'
				  and om_codom='".$_GET['codom']."'
			  order by 1 ";
$dest=mysql_query($qry);	
$soma=0;
while ($row = mysql_fetch_array($dest, MYSQL_NUM)) {
	echo "<tr align='center' bgcolor='$cor3' class='marcar_linha'>
				<td>".substr($row[0],8,2)."/".substr($row[0],5,2)."/".substr($row[0],0,4)."</td>	
				<td>". number_format($row[1], 3, ',', '.')." lts</td>	
			</tr>";
			$soma = $soma + $row[1];
}				
	
echo "<tr bgcolor='$cor3' align='right'>
			<td><b><i>Total:&nbsp;</td><td><b><i>". number_format($soma, 0, ',', '.')." lts</td>
		</tr>";
		
echo "<tr bgcolor='' align='center'>
			<td colspan='4'><b><i>&nbsp;</td>
		</tr>";

echo "<tr bgcolor='' align='center'>
			<td colspan='3'><b><i>Registro de Sangria</td>
		</tr>";
echo "<tr bgcolor='$cor2' align='center'>
			<td><b><i>Data</td>
			<td><b><i>Responsável</td>
			<td><b><i>Qtd</td>
		</tr>";
$qry="SELECT s.data_sangria, concat(substr(u.post_grad,2),' ',u.nome_guerra), s.qtd_retirada 
			  FROM usuario u, sangria s
			  where u.idt=s.usuario_idt
			  		and s.nr_repasse='".$_GET['rep']."'
				  	and s.om_codom='".$_GET['codom']."'
			  order by 1 ";
$dest=mysql_query($qry);	
$soma=0;
while ($row = mysql_fetch_array($dest, MYSQL_NUM)) {
	echo "<tr align='center' bgcolor='$cor3' class='marcar_linha'>
				<td>".substr($row[0],8,2)."/".substr($row[0],5,2)."/".substr($row[0],0,4)."</td>	
				<td>$row[1] </td>	
				<td>". number_format($row[2], 3, ',', '.')." lts</td>	
			</tr>";
			$soma = $soma + $row[2];
}	

echo "<tr bgcolor='$cor3' align='right'>
			<td colspan='2'><b><i>Total:&nbsp;</td><td><b><i>". number_format($soma, 0, ',', '.')." lts</td>
		</tr>";
		
echo "<tr bgcolor='' align='center'>
			<td colspan='5'><b><i>&nbsp;</td>
		</tr>";

echo "<tr bgcolor='' align='center'>
			<td colspan='5'><b><i>Repasse(s) de Combustível(is)</td>
		</tr>";
echo "<tr bgcolor='$cor2' align='center'>
			<td><b><i>Data</td>
			<td colspan='2'><b><i>Registro</td>
			<td><b><i>Qtd</td>
			<td><b><i>OM</td>
		</tr>";

/* $qry="SELECT e.dt_repasse, e.obs, e.qtd_destinada, om.codom
			FROM  `estoque` e,om
			WHERE om.codom=e.om_codom and e.nr_nota_fiscal = '".$_GET['nota_fiscal']."'
				and e.om_codom = '".$_GET['codom']."'
				and e.nr_repasse = '".$_GET['rep']."'
				union";*/
 $qry="	SELECT h.dt_registro, h.registro, h.quantidade, h.om_destino
			FROM historico_repasse h, om
			where om.codom=h.om_destino
				and h.nr_repasse='".$_GET['rep']."'
			 	and h.om_codom='".$_GET['codom']."'
			order by 1 ";
$dest=mysql_query($qry);	
$soma=0;
while ($row = mysql_fetch_array($dest, MYSQL_NUM)) {
//	if($row[1]!='') {
	$qrysigla = "select sigla from om where codom='$row[3]'";
	$ressigla=mysql_query($qrysigla);
	$sigla=mysql_fetch_row($ressigla);
	echo "<tr align='center' bgcolor='$cor3' class='marcar_linha'>
				<td>".substr($row[0],8,2)."/".substr($row[0],5,2)."/".substr($row[0],0,4)." ".substr($row[0],11,5)."</td>	
				<td colspan='2'>$row[1] </td>	
				<td>". number_format($row[2], 0, ',', '.')." lts</td>	";
//				if($_GET['oc']==$_GET['codom']) {
					echo "<td>$sigla[0] </td>";
//				}else {
	//				echo "<td>".$_GET['om']."</td>";
	//			}
		echo "</tr>";
			if($row[1]=='Cancelamento de Repasse') {
				$soma = $soma - $row[2];
			} elseif($row[1]=='Repasse') {
				$soma = $soma + $row[2];
			} 
//	}
}	
echo "<tr bgcolor='$cor3' align='right'>
			<td colspan='3'><b><i>Total:&nbsp;</td><td><b><i>". number_format($soma, 0, ',', '.')." lts</td><td></td>
		</tr>";
		
echo "</table>";
echo "</body>";
?>
