<?php
  if(!isset($_SESSION['usuario']))
    include "verifica_sessao.php";
?>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php    
	echo "<br><t1>Relação do Recebimento de Combustíveis</t1><br>";
	echo "<table border='0' width='100%' align='center' valign='top'>";

	if($_GET['ord']=='') {
		$_GET['ord']='3 desc';
	}
   /*Seleciona a relacao de recebimentos*/

	if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
		$query = "SELECT r.nr_nota_fiscal, c.nome, r.dt_rec, r.qtd_rec, r.combustivel_codigo, r.nr_pedido, r.nr_remessa, rs.codigo, o.sigla, r.oc, s.qtd_atual
				FROM combustivel c, om o, reservatorio rs, 
						recebimento r left join (SELECT nr_nota_fiscal, combustivel_codigo, om_codom, sum(qtd_atual) qtd_atual FROM estoque group by nr_nota_fiscal, combustivel_codigo, om_codom) s 
										on (r.nr_nota_fiscal=s.nr_nota_fiscal and r.combustivel_codigo=s.combustivel_codigo and r.oc=s.om_codom)
				WHERE c.codigo = r.combustivel_codigo
				and rs.codigo=r.reservatorio_codigo
				and o.codom=rs.om_codom
				ORDER BY r.oc, ".$_GET['ord']." ";
	} elseif (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$query = "SELECT r.nr_nota_fiscal, c.nome, r.dt_rec, r.qtd_rec, r.combustivel_codigo, r.nr_pedido, r.nr_remessa, rs.codigo, o.sigla, s.om_codom, s.qtd_atual
				FROM combustivel c, reservatorio rs, om o, 
						recebimento r left join (SELECT nr_nota_fiscal, combustivel_codigo, om_codom, sum(qtd_atual) qtd_atual 
															FROM estoque 
															where om_codom ='".$_SESSION['codom']."'
															group by nr_nota_fiscal, combustivel_codigo, om_codom) s 
										on (r.nr_nota_fiscal=s.nr_nota_fiscal and r.combustivel_codigo=s.combustivel_codigo )
				WHERE c.codigo = r.combustivel_codigo
				and rs.codigo=r.reservatorio_codigo
				and o.codom=rs.om_codom
				and s.om_codom='".$_SESSION['codom']."'
				ORDER BY r.oc, ".$_GET['ord']." ";
	} elseif ((($_SESSION['perfil']=='CMT DA OM') or ($_SESSION['perfil']=='FISCAL DA OM')) and ($_SESSION['gerente']=='SIM')) {
		echo "<div align='left'><coluna><a href='index.php?nomeArquivo=cad_recebimento.php' style='text-decoration:none'>&nbsp;&nbsp;Novo Recebimento<IMG SRC='./imagens/novo.png' width='25' height='25' border=0 ></a><br>";
		echo "<tr align='center' bgcolor='$cor1'>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='1'){echo "1 desc";}else{echo "1";} echo "' style='text-decoration:none'>Nota Fiscal</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='3'){echo "3 desc";}else{echo "3";} echo "' style='text-decoration:none'>Data</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='8'){echo "8 desc";}else{echo "8";} echo "' style='text-decoration:none'>Reservatório</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='2'){echo "2 desc";}else{echo "2";} echo "' style='text-decoration:none'>OC</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='6'){echo "6 desc";}else{echo "6";} echo "' style='text-decoration:none'>Nº Protocolo</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='7'){echo "7 desc";}else{echo "7";} echo "' style='text-decoration:none'>Nº Remessa</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='4'){echo "4 desc";}else{echo "4";} echo "' style='text-decoration:none'>Qtd Recebida</a></td>
					<td><coluna>Distribuição</td>
					<td><coluna>Alterar</td>
					<td><coluna>Remover</td>
				</tr>";
		$query = "SELECT r.nr_nota_fiscal, c.nome, r.dt_rec, r.qtd_rec, r.combustivel_codigo, r.nr_pedido, r.nr_remessa, rs.codigo, o.sigla, r.oc, u.sigla
				FROM combustivel c, recebimento r, reservatorio rs, om o, (select sigla from om where codom='".$_SESSION['codom']."') u
				WHERE c.codigo = r.combustivel_codigo
				and rs.codigo=r.reservatorio_codigo
				and o.codom=r.oc
				and rs.om_codom='".$_SESSION['codom']."'
				ORDER BY r.oc, ".$_GET['ord']." ";
	}

	$result = mysql_query($query);
	$nf_zero = $dt_zero = $res_zero = $comb_zero = $ccomb_zero = $ped_zero = $rec_zero = $disp_zero = $oc_zero = array();

	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		/*Seleciona o somatorio das quatidades atuais nas cota do recebimento*/
		$qry="select sum(qtd_atual) from cotas where recebimento_nr_nota_fiscal='$row[0]' group by recebimento_nr_nota_fiscal, recebimento_combustivel_codigo";  
		$resultado1 = mysql_query($qry);
		$rec = mysql_fetch_row($resultado1);
		/*Se ainda existe combustivel pra ser remanejado mostra a linha*/

		$ano=substr($row[2],0,4);
      $mes=substr($row[2],5,2);
      $dia=substr($row[2],8,2);
      $dt_nota = $dia."/".$mes."/".$ano;
		/*/Seleciona a quantidade já distribuida*/
		$query = "SELECT sum(qtd_destinada), sum(qtd_atual)
					FROM estoque
					WHERE nr_nota_fiscal='".$row[0]."'
					and combustivel_codigo='".$row[4]."'
					and om_codom='".$row[9]."'";
		$resultado = mysql_query($query);
		$cotas = mysql_fetch_row($resultado);
		if ($cotas[0] == ''){
			$cotas[0]=0;
		}

		/*/Verifica se existe distribuição para outras Unidades*/ 
		$query = "SELECT count(qtd_destinada)
					FROM estoque
					WHERE nr_nota_fiscal='".$row[0]."'
					and combustivel_codigo='".$row[4]."'";
		$resultado = mysql_query($query);
		$linhas = mysql_fetch_row($resultado);

if(round($cotas[1],0)>0) {
		if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='ADMINISTRADOR')) {
			if($row[9]!=$ant) {
				echo "<tr><td>&nbsp;</td></tr>";
				$qrysigla = "SELECT sigla from om where codom='$row[9]' ";
				$ressigla = mysql_query($qrysigla);
				$sigla = mysql_fetch_row($ressigla);
				echo "<tr align='left' bgcolor='$cor1'>
							<td colspan='9'><coluna>&nbsp;OC: $sigla[0]</td>
						</tr>";
				echo "<tr align='center' bgcolor='$cor1'>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='1'){echo "1 desc";}else{echo "1";} echo "' style='text-decoration:none'>Nota Fiscal</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='3'){echo "3 desc";}else{echo "3";} echo "' style='text-decoration:none'>Data</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='8'){echo "8 desc";}else{echo "8";} echo "' style='text-decoration:none'>Reservatório</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='2'){echo "2 desc";}else{echo "2";} echo "' style='text-decoration:none'>Combustível</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='6'){echo "6 desc";}else{echo "6";} echo "' style='text-decoration:none'>Nº Protocolo</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='7'){echo "7 desc";}else{echo "7";} echo "' style='text-decoration:none'>Nº Remessa</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='4'){echo "4 desc";}else{echo "4";} echo "' style='text-decoration:none'>Qtd Recebida</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='11'){echo "11 desc";}else{echo "11";} echo "' style='text-decoration:none'>Disponível</a></td>
					<td><coluna>Cotas</td>
				</tr>";
				$ant = $row[9];
				
			}
			
			//Verifica o somatório dos abastecimentos autorizados e não realizados
			$qrya = "select sum(qtd_autorizada) 
							from abastecimento 
							where reservatorio_codigo='$row[7]' and nr_nota_fiscal='$row[7]' and cota_om='".$_SESSION['codom']."' and qtd_abastecida is null";
			$resa = mysql_query($qrya);
			$qtda = mysql_fetch_row($resa);
			$cotas[1] = str_replace(".", ",",($cotas[1]-$qtda[0]));

			echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'>
						<td><lin>$row[0]</td>
						<td><lin>$dt_nota</td>
						<td><lin>Nº $row[7] - $row[8]</td>
						<td><lin>$row[1]</td>
						<td><lin>$row[5]</td>
						<td><lin>$row[6]</td>
						<td><lin>$row[3] Lts</td>";
					echo "<td><lin>".round($cotas[1],0)." Lts </td>";
							echo "<td align='center'><lin><a href='index.php?nomeArquivo=gerenciar_cotas.php&nota_fiscal=$row[0]&combustivel=$row[4]&m=".$_GET['m']."&ord=".$_GET['ord']."' style='text-decoration:none'>Repasses</td>";
			echo "</tr>";
		} elseif ((($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='CMT DA OM')) and ($_SESSION['gerente']=='SIM')) {
			echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'>
						<td><lin>$row[0]</td>
						<td><lin>$dt_nota</td>
						<td><lin>Nº $row[7] - $row[10]</td>
						<td><lin>$row[8]</td>
						<td><lin>$row[5]</td>
						<td><lin>$row[6]</td>
						<td><lin>$row[3] Lts</td>
						<td><lin><a href='index.php?nomeArquivo=gerenciar_cotas.php&nota_fiscal=$row[0]&combustivel=$row[4]' style='text-decoration:none'>Repasses</td>
						<td align='center'><lin><a href='index.php?nomeArquivo=alterar_recebimento.php&nr_nota_fiscal=$row[0]&combustivel=$row[4]&oc=$row[8]' style='text-decoration:none'>Alterar</td>";
			if ($linhas[0] == 1){
				echo "<td align='center'><lin><a href='index.php?nomeArquivo=remover_registro.php&valor=$row[0]&valor1=$row[4]&valor2=$row[9]&tab=recebimento&ch=nr_nota_fiscal&ch1=combustivel_codigo&registro=a Nota Fiscal Nº $row[0] de $dt_nota&&arquivo=remover_recebimento.php' style='text-decoration:none'>Remover</td>";
			} else {
				echo "<td align='center'>-</td>";
			}							
			echo "</tr>";
  		}
} 
else {
	$cont++;
	array_push($nf_zero,$row[0]); 
	array_push($dt_zero,$dt_nota); 
	array_push($res_zero,"Nº ".$row[7]." - ".$row[8]); 
	array_push($comb_zero,$row[1]); 
	array_push($ccomb_zero,$row[4]); 
	array_push($ped_zero,$row[5]); 
	array_push($rem_zero,$row[6]); 
	array_push($rec_zero,$row[3]); 
	array_push($disp_zero,round($cotas[1],0)); 
	array_push($oc_zero,$row[9]); 

}
   }
  	$num_rows = mysql_num_rows($result);
  	mysql_free_result($result);


	echo "<tr><td>&nbsp;</td></tr>";
	if($_GET['m']!='1') {
		echo "<tr ><td colspan='9'><coluna>&nbsp;&nbsp;&nbsp;<a href='index.php?nomeArquivo=cons_recebimento.php&m=1' style='text-decoration:none' title='Mostrar Registros Totalmente Distribuídos'>+ Mostrar Registros Totalmente Distribuídos</a></td></tr>";
	} else {
	echo "<tr align='center' bgcolor='$cor1'><td colspan='9'><coluna>REGISTROS TOTALMENTE DISTRIBUÍDOS</coluna><br><i><a href='index.php?nomeArquivo=cons_recebimento.php&m=0' style='text-decoration:none' title='Ocultar Registros Totalmente Distribuídos'>Ocultar</a></td></tr>";

		for($i=0; $i<$cont; $i++) {
			if($oc_zero[$i]!=$ant) {
				if($i!=0) {
					echo "<tr><td>&nbsp;</td></tr>";
				}
				$qrysigla = "SELECT sigla from om where codom='$oc_zero[$i]' ";
				$ressigla = mysql_query($qrysigla);
				$sigla = mysql_fetch_row($ressigla);
				echo "<tr align='left' bgcolor='$cor1'>
							<td colspan='9'><coluna>&nbsp;OC: $sigla[0]</td>
						</tr>";
		echo "<tr align='center' bgcolor='$cor1'>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='1'){echo "1 desc";}else{echo "1";} echo "' style='text-decoration:none'>Nota Fiscal</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='3'){echo "3 desc";}else{echo "3";} echo "' style='text-decoration:none'>Data</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='8'){echo "8 desc";}else{echo "8";} echo "' style='text-decoration:none'>Reservatório</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='2'){echo "2 desc";}else{echo "2";} echo "' style='text-decoration:none'>OC</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='6'){echo "6 desc";}else{echo "6";} echo "' style='text-decoration:none'>Nº Protocolo</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='7'){echo "7 desc";}else{echo "7";} echo "' style='text-decoration:none'>Nº Remessa</a></td>
					<td><coluna><a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord="; if ($_GET['ord']=='4'){echo "4 desc";}else{echo "4";} echo "' style='text-decoration:none'>Qtd Recebida</a></td>
					<td><coluna>Saldo</td>
					<td><coluna>Distribuição</td>
				</tr>";
				$ant = $oc_zero[$i];
				
			}
			echo "<tr align='center' class='marcar_linha' >
			<td><lin>$nf_zero[$i]</td><td><lin>$dt_zero[$i]</td><td><lin>$res_zero[$i]</td><td><lin>$comb_zero[$i]</td>
			<td><lin>$ped_zero[$i]</td><td><lin>$rem_zero[$i]</td><td><lin>$rec_zero[$i] Lts</td><td><lin>$disp_zero[$i] Lts</td> ";
//			if($oc_zero[$i]==$_SESSION['codom']) {
				echo "<td align='center'><lin><a href='index.php?nomeArquivo=gerenciar_cotas.php&nota_fiscal=$nf_zero[$i]&combustivel=$ccomb_zero[$i]&m=".$_GET['m']."' style='text-decoration:none'>Repasses</td>";
//			} else {
//				echo "<td align='center'>-</td>";
//			}							
			echo "</tr>";
		}
	}
	$cont=0;

	echo "</table>";
?>
