<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
</head>

<?php    
	echo "<table border='0' bgcolor='$cor_fundo' width='100%' align='left' valign='top'>";
	echo "<tr align='center'><td COLSPAN='8'>&nbsp;</td></tr>";
	echo "<tr align='center'><td COLSPAN='8'><b><font size='5'><I>Situação dos Reservatórios</b></I></td></tr>";
   
   /*LISTA OS Comandos*/ 
	if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){
		$query = "SELECT om.codom, om.sigla
				FROM om
				where 
					tipo in ('OC','Integrante') and sigla like 'CMDO%'
				ORDER BY CAST( SUBSTRING( sigla, 5 ) AS SIGNED )";
	}
	elseif (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){
		$query = "SELECT *
				FROM om 
				where tipo in ('OC','Integrante') and sigla like 'CMDO%'
				and codom='".$_SESSION['codom']."'
				ORDER BY sigla";
		$_GET['oc']=$_SESSION['codom'];
	}
	elseif ((($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) or ($_SESSION['perfil']=='ABASTECEDOR')){		
		$query = "SELECT *
				FROM om 
				where tipo in ('OC','Integrante') and nome like 'COMANDO%'
				and codom='".$_SESSION['om_enq']."'
				ORDER BY sigla";
				$_GET['oc']=$_SESSION['om_enq'];
				if($_GET['codom']=='') {
					$_GET['codom']=$_SESSION['codom'];
				}
	}

	$resultado=mysql_query($query);
	while ($oc = mysql_fetch_array($resultado, MYSQL_NUM)) {
		if($oc[0]==$_GET['oc']){
			echo "<tr bgcolor='$cor1' align='left'><td COLSPAN='8'><coluna>&nbsp;&nbsp;<a href='index.php?nomeArquivo=tancagem.php' style='text-decoration:none'><font color='$cor_submenu'>$oc[1]</font></a></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){
				echo " <a href='index.php?nomeArquivo=tancagem.php' style='text-decoration:none'>Fechar</a>";
			}			
			echo "</td></tr>";
			echo "<tr bgcolor='$cor1' align='left'><td bgcolor='$cor_fundo'>";
			echo "<table border='0'>";
			echo "<tr bgcolor='$cor1' align='center'><td bgcolor='$cor_fundo' rowspan='2'>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td rowspan='2'><coluna>&nbsp;OM&nbsp;</td>
						<td rowspan='2'><coluna>&nbsp;Reservatório&nbsp;</td>
						<td rowspan='2'><coluna>&nbsp;Combustível&nbsp;</td>
						<td rowspan='2'><coluna>&nbsp;Capacidade&nbsp;</td>
						<td colspan='2'><coluna>&nbsp;Quantidade&nbsp;</td>
						<td colspan='2' rowspan='2'><coluna>&nbsp;Uso&nbsp;</td></tr>";
			echo "<tr bgcolor='$cor1' align='center'><td><coluna>&nbsp;Atual&nbsp;</td><td><coluna>&nbsp;Livre&nbsp;</td></tr>";

			/*CALCULA A DISPONIBILIDADE DE GASOLINA DE OM*/					

			$qry_tanc ="SELECT om.codom, om.sigla, r.codigo, c.nome, r.capacidade, round(sum(e.qtd_atual),0)
						FROM om, estoque e, reservatorio r, combustivel c
						WHERE om.codom=r.om_codom
							and r.codigo = e.reservatorio_codigo
							and c.codigo=e.combustivel_codigo ";
			if((($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) or ($_SESSION['perfil']=='ABASTECEDOR')) {	
				$qry_tanc = $qry_tanc."and om.codom='".$_SESSION['codom']."' ";
			} else {			
				$qry_tanc = $qry_tanc."and (om.subordinacao='$oc[0]' or om.codom='$oc[0]') ";
			}
			$qry_tanc = $qry_tanc."group by om.codom, om.sigla, r.codigo, c.nome, r.capacidade
						order by om.sigla, c.nome";
			$res_tanc=mysql_query($qry_tanc);
			while ($om = mysql_fetch_array($res_tanc, MYSQL_NUM)) {
				if($_GET['res']==$om[2]) {
					echo "<tr><td>&nbsp;</td></tr>";
					echo "<tr bgcolor='$cor3' align='right'><td bgcolor='$cor_fundo'></td><td align='center'><coluna>&nbsp;$om[1]&nbsp;</td>
								<td align='center'><coluna>Nº&nbsp;$om[2]</td><td align='center'><coluna>$om[3]</td>
								<td><coluna>&nbsp;".number_format($om[4],0,',','.')."&nbsp;Lt&nbsp;</td>
								<td><coluna>&nbsp;";
								if($om[5]>$om[4]) {
									echo number_format($om[4],0,',','.');
								} else {
									echo number_format($om[5],0,',','.');
								}
								echo "&nbsp;Lt&nbsp;</td>
								<td><coluna>&nbsp;";
									if(number_format(($om[4]-$om[5]),0,',','.')<0) {
										echo "0";
									} else {
										echo number_format(($om[4]-$om[5]),0,',','.');
									}
									echo "&nbsp;Lt&nbsp;</td>
								<td colspan='2' align='center'><coluna>&nbsp;&nbsp;";
									if(number_format($om[5]/$om[4]*100)>100) {
										echo "100,00";
									} else {
										echo number_format(($om[5]/$om[4]*100),2,',','.');
									}
									echo "&nbsp;%&nbsp;</td>";
/*					if(($om[4]-$om[5])>=5000) {
						echo "<br>&nbsp;Possibilidade de depósito&nbsp;";
					}*/
					echo "</td></tr>";

					echo "<tr><td></td><td></td><td colspan='4'>";
	
					$qry_det = "SELECT om.sigla, round(sum(e.qtd_atual),0)
									FROM om, estoque e 
									where om.codom=e.om_codom
									and e.reservatorio_codigo='".$_GET['res']."'
									group by om.sigla
									ORDER BY om.sigla";
					$res_det=mysql_query($qry_det);
					echo "<table border='0' width='100%' >";
					echo "<tr bgcolor='$cor2' align='center'><td><coluna>OM Detentora</td><td><coluna>Qtd</td></tr>";
					while ($det = mysql_fetch_array($res_det, MYSQL_NUM)) {
						echo "<tr bgcolor='$cor3'><td><lin>$det[0]</td><td align='right'><lin>".number_format($det[1],0,',','.')." Lt</td></tr>";
					}
					echo "<tr><td colspan='3' align='center'><a href='index.php?nomeArquivo=tancagem.php&oc=".$_GET['oc']."' style='text-decoration:none'>Fechar</a></td></tr>";
					echo "<tr><td colspan='3' align='center'>&nbsp;</td></tr>";
					echo "</table>";
					echo "</td></tr>";
				} else {
				echo "<tr bgcolor='$cor3' align='right' class='marcar_linha'><td bgcolor='$cor_fundo'></td><td align='center'><lin>&nbsp;$om[1]&nbsp;</td>
							<td align='center'><lin>Nº&nbsp;$om[2]</td><td align='center'><lin>$om[3]</td>
							<td><lin>&nbsp;".number_format($om[4],0,',','.')."&nbsp;Lt&nbsp;</td>
							<td><lin>&nbsp;";
							if($om[5]>$om[4]) {
								echo number_format($om[4],0,',','.');
							} else {
								echo number_format($om[5],0,',','.');
							}
							echo "&nbsp;Lt&nbsp;</td>
							<td><lin>&nbsp;";
								if(number_format(($om[4]-$om[5]),0,',','.')<0) {
									echo "0";
								} else {
									echo number_format(($om[4]-$om[5]),0,',','.');
								}
								echo "&nbsp;Lt&nbsp;</td>
							<td>&nbsp;&nbsp;";
								if(number_format($om[5]/$om[4]*100)>100) {
									echo "100,00";
								} else {
									echo number_format(($om[5]/$om[4]*100),2,',','.');
								}
								echo "&nbsp;%&nbsp;</td>";
								echo "<td>&nbsp;<a href='index.php?nomeArquivo=tancagem.php&oc=".$_GET['oc']."&res=$om[2]' style='text-decoration:none'>Detalhar</a>&nbsp;</td>";
/*				if(($om[4]-$om[5])>=5000) {
					echo "<br>&nbsp;Possibilidade de depósito&nbsp;";
				}*/
				echo "</td></tr>";
					
				}
			}
			echo "</table>";
			echo "</td></tr>";

		} else {
			echo "<tr bgcolor='$cor1' align='left'><td COLSPAN='8'><b>&nbsp; <i><a href='index.php?nomeArquivo=tancagem.php&oc=$oc[0]' style='text-decoration:none'><font color='$cor_submenu'>$oc[1]</font>&nbsp;&nbsp;&nbsp;</b><linha>Listar</a>&nbsp;&nbsp;$oc[2]</td></tr>";
		}
	}
	echo "</table>";


?>
