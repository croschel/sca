<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<Script Language="JavaScript">
function getOM(what,oc,ano,mes) {
//	alert(oc);
      var cod_om = what.value;
     document.location=('index.php?nomeArquivo=rel_recebimento.php&oc=' + oc + '&ano=' + ano + '&mes=' + mes + '&codom=' + cod_om);
}
</Script>

<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";

	if($_GET['ano']=='') { $_GET['ano']=date('Y');}
	if($_GET['mes']=='') { $_GET['mes']=date('m');}
	if($_GET['mes']>'12') { $_GET['mes']='01'; $_GET['ano']=$_GET['ano']+1;}
	if($_GET['mes']<'1') { $_GET['mes']='12'; $_GET['ano']=$_GET['ano']-1;}

 	switch($_GET['mes']) {
		case 1: $mes_a = 'Dezembro / '.($_GET['ano']-1);$mes = 'Janiero'; $p_mes = 'Fevereiro / '.$_GET['ano']; break;
		case 2: $mes_a = 'Janiero / '.$_GET['ano'];$mes = 'Fevereiro'; $p_mes = 'Março / '.$_GET['ano']; break;
		case 3: $mes_a = 'Fevereiro / '.$_GET['ano'];$mes = 'Março'; $p_mes = 'Abril / '.$_GET['ano']; break;
		case 4: $mes_a = 'Março / '.$_GET['ano'];$mes = 'Abril'; $p_mes = 'Maio / '.$_GET['ano']; break;
		case 5: $mes_a = 'Abril / '.$_GET['ano'];$mes = 'Maio'; $p_mes = 'Junho / '.$_GET['ano']; break;
		case 6: $mes_a = 'Maio / '.$_GET['ano'];$mes = 'Junho'; $p_mes = 'Julho / '.$_GET['ano']; break;
		case 7: $mes_a = 'Junho / '.$_GET['ano'];$mes = 'Julho'; $p_mes = 'Agosto / '.$_GET['ano']; break;
		case 8: $mes_a = 'Julho / '.$_GET['ano'];$mes = 'Agosto'; $p_mes = 'Setembro / '.$_GET['ano']; break;
		case 9: $mes_a = 'Agosto / '.$_GET['ano'];$mes = 'Setembro'; $p_mes = 'Outubro / '.$_GET['ano']; break;
		case 10: $mes_a = 'Setembro / '.$_GET['ano'];$mes = 'Outubro'; $p_mes = 'Novembro / '.$_GET['ano']; break;
		case 11: $mes_a = 'Outubro / '.$_GET['ano'];$mes = 'Novembro'; $p_mes = 'Dezembro / '.$_GET['ano']; break;
		case 12: $mes_a = 'Novembro / '.$_GET['ano'];$mes = 'Dezembro'; $p_mes = 'Janiero / '.($_GET['ano']+1); break;
	}

	echo "<form name='rel_recebimento' method='POST' action='index.php?nomeArquivo=rel_recebimento.php'>";

	echo "<table border='0'	 width='100%' align='center' valign='top'>";
	echo "<tr align='center' >";
		echo "<td><br><i><t1>Controle de Combustíveis </t1><br> Mapa Mensal de Recebimentos<br><br></td>";
   echo "</tr>";
//	echo "</form>";
	echo "</table>";


	/*/Relação de órgão Controlador*/
	if(($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
		$qryoc = "select codom, sigla from om where tipo='OC' order by CAST( SUBSTRING( sigla, 5 ) AS SIGNED )";
	} elseif(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$qryoc = "select codom, sigla from om where codom='".$_SESSION['codom']."'";
		$_GET['oc']=$_SESSION['codom'];
	} elseif(($_SESSION['perfil']=='FISCAL DA OM') or ($_SESSION['perfil']=='ABASTECEDOR')) {
		$qryoc = "select codom, sigla from om where codom='".$_SESSION['oc']."'";
		$_GET['oc']=$_SESSION['oc'];
	} 
	$resoc = mysql_query($qryoc);

	echo "<table border='0' width='100%' align='left' valign='top'>";
	echo "<tr bgcolor='$cor1'><td><i><b>&nbsp;&nbsp;ORGÃO CONTROLADOR&nbsp;</td></tr>";		
	while ($oc = mysql_fetch_array($resoc, MYSQL_NUM)) {
  		if($_GET['oc']==$oc[0]) {
			/*/Seleciona os repasses do mes escolhido*/
			$qry_estoque="SELECT o.sigla, e.qtd_destinada, t.tipo_cota, e.dt_repasse, e.qtd_atual, e.nr_repasse, e.om_codom, e.obs, c.nome, e.reservatorio_codigo, 
										rs.sigla, e.nr_nota_fiscal, r.dt_rec, e.combustivel_codigo, r.oc
												FROM om o, estoque e, tipo_cota t, combustivel c, (select om.sigla, r.codigo from om, reservatorio r where om.codom=r.om_codom) rs, recebimento r
												where o.codom=e.om_codom 
													and c.codigo=e.combustivel_codigo
													and t.cod_tp_cota=e.cod_tp_cota
													and rs.codigo=e.reservatorio_codigo
													and r.nr_nota_fiscal=e.nr_nota_fiscal
													and r.combustivel_codigo=e.combustivel_codigo
													and e.dt_repasse between '".$_GET['ano']."-".$_GET['mes']."-01 00:00:01' and '".$_GET['ano']."-".$_GET['mes']."-31 23:59:59' 
													and o.oc='".str_pad(substr($_GET['oc'], 1), 6, "0", STR_PAD_LEFT)."' ";
			if(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){
				$qry_estoque = $qry_estoque."and o.subordinacao='".$_SESSION['codom']."' ";
			} else
			if(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='Sim')){
				$qry_estoque = $qry_estoque."and e.om_codom='".$_SESSION['codom']."' ";
			}


			$qry_estoque = $qry_estoque."ORDER BY o.sigla, e.dt_repasse";
			$resultado1 = mysql_query($qry_estoque); 

			echo "<tr bgcolor='$cor1'><td><i><b>&nbsp;&nbsp;&nbsp;::&nbsp;<font color='$cor_submenu'>$oc[1]</font></b>&nbsp;&nbsp;&nbsp;"; if(($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')){ echo "<i><a href='index.php?nomeArquivo=rel_recebimento.php&ano=".$_GET['ano']."&mes=".$_GET['mes']."' style='text-decoration:none'>Fechar</a>";} echo "</td></tr>";		
  			echo "<tr><td colspan='2' align='center'>";
  			echo "<table  border='0' width='80%'>";
			echo "<tr><td colspan='5' align='left'><i>
					<a href='index.php?nomeArquivo=rel_recebimento.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".($_GET['mes']-1)."&codom=".$_GET['codom']."' style='text-decoration:none' title='Mês Anterior - $mes_a'>&nbsp;Anterior&nbsp;</a>&nbsp;
					<font size='4' color='blue'><b>- $mes / ".$_GET[ano]."</font></b> - 
					<a href='index.php?nomeArquivo=rel_recebimento.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".($_GET['mes']+1)."&codom=".$_GET['codom']."' style='text-decoration:none' title='Próximo Mês - $p_mes'>&nbsp; Próximo&nbsp;</a>";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<coluna>OM Receptora: <select name='codom'  onChange='getOM(this,1".substr($_GET['oc'], 1).",".$_GET['ano'].",".$_GET['mes'].");'>";
						echo "<option value='Todas'>TODAS</option>";   
    					while ($om = mysql_fetch_array($resultado1, MYSQL_NUM)) {
    						if($om[6]!=$ant) {
								echo "<option value='$om[6]' ";	if ($om[6]==str_pad($_GET['codom'], 6, "0", STR_PAD_LEFT))	{	echo 'selected';}	echo ">$om[0]</option>";
								$ant=$om[6];
							}
						}
					
			echo "</select></td></tr>";	   

			$resultado1 = mysql_query($qry_estoque); 
			echo "<tr><td colspan='6' align='center'>"; 
			echo "<table width='100%'>";
			echo "<tr bgcolor='$cor1'><td align='center' colspan='12'><b><i>Distribuição do Combustível</td></tr>";
			echo "<tr bgcolor='$cor2' align='center'>
						<td rowspan='2'><b><i>OM Receptora</td>
						<td rowspan='2'><b><i>Nº Repasse</td>
						<td rowspan='2'><b><i>Data</td>
						<td rowspan='2'><b><i>Reservatório</td>
						<td rowspan='2'><b><i>Combustível</td>
						<td rowspan='2'><b><i>Tipo</td>
						<td rowspan='2'><b><i>Obs</td>
						<td align='center' colspan='2'><b><i>Quantidade</td>
						<td rowspan='2'><b><i>Destino</td>
						<td align='center' colspan='2'><b><i>Nota Fiscal</td>
					</tr>";
			echo "<tr bgcolor='$cor2' align='center'><td><b><i>Recebida</td><td ><b><i>Atual</td><td>Nº</td><td>Data</td></tr>";
			$total_rec = $total_disp = 0;
			while ($dist = mysql_fetch_array($resultado1, MYSQL_NUM)) {
				if($dist[6]==$dist[7]) {$dist[1]=$dist[8];}
				if(($_GET['codom']!='') and ($_GET['codom']!='Todas')) {
					if ($dist[6]==str_pad($_GET['codom'], 6, "0", STR_PAD_LEFT)){
						echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td align='left'>$dist[0] </td><td>$dist[5]</td>
						<td>".substr($dist[3],8,2)."/".substr($dist[3],5,2)."/".substr($dist[3],0,4)."</td>
						<td>Nº $dist[9] ($dist[10])</td><td> $dist[8]</td><td>$dist[2]</td><td>$dist[7]</td>
						<td align='right'>". number_format($dist[1], 0, ',', '.')." Lts&nbsp;</td>
						<td align='right'>". number_format($dist[4], 0, ',', '.')." Lts&nbsp;</td>
						<td align='center'>
							<a href=\"#\" title='Listar destino do combustível deste repasse.' onClick=\"window.open('destino_comb.php?om=$dist[0]&dt=$dist[3]&codom=$dist[6]&oc=$rec[8]&rep=$dist[5]&nota_fiscal=$rec[3]','janela','location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600');\"><img src='imagens/interrogacao.png'></a>
						</td>
						<td>";
						if($_SESSION['codom']==$dist[14]) {
							echo "<a href='index.php?nomeArquivo=gerenciar_cotas.php&nota_fiscal=$dist[11]&combustivel=$dist[13]&tela=rel&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".$_GET['mes']."' style='text-decoration:none'>$dist[11]</a>";
						} else {
							echo "$dist[11]";
						}
						echo "</td><td>".substr($dist[12],8,2)."/".substr($dist[12],5,2)."/".substr($dist[12],0,4)."</td>
						</tr>";
					}
				} else {
					echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><td align='left'>$dist[0] </td><td>$dist[5]</td>
					<td>".substr($dist[3],8,2)."/".substr($dist[3],5,2)."/".substr($dist[3],0,4)."</td>
					<td>Nº $dist[9] ($dist[10])</td><td> $dist[8]</td><td>$dist[2]</td><td>$dist[7]</td>
					<td align='right'>". number_format($dist[1], 0, ',', '.')." Lts&nbsp;</td>
					<td align='right'>". number_format($dist[4], 0, ',', '.')." Lts&nbsp;</td>
					<td align='center'>
						<a href=\"#\" title='Listar destino do combustível deste repasse.' onClick=\"window.open('destino_comb.php?om=$dist[0]&dt=$dist[3]&codom=$dist[6]&oc=$rec[8]&rep=$dist[5]&nota_fiscal=$rec[3]','janela','location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600');\"><img src='imagens/interrogacao.png'></a>
					</td>
					<td>";
					if($_SESSION['codom']==$dist[14]) {
						echo "<a href='index.php?nomeArquivo=gerenciar_cotas.php&nota_fiscal=$dist[11]&combustivel=$dist[13]&tela=rel&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".$_GET['mes']."' style='text-decoration:none'>$dist[11]</a>";
					} else {
						echo "$dist[11]";
					}
					echo "</td><td>".substr($dist[12],8,2)."/".substr($dist[12],5,2)."/".substr($dist[12],0,4)."</td>
					</tr>";
				}
			}
			echo "<tr align='right' bgcolor='$cor2'><td  COLSPAN='12'>&nbsp;</td></tr>";
			echo "</table>";
			echo "</td></tr>"; 

   	echo "</table>";
		echo "</td></tr>"; 
 		echo "<tr align='center' bgcolor='$cor_fundo'><td>&nbsp;</td></tr>";
    	} else {
			echo "<tr bgcolor='$cor1'><td><i><b>&nbsp;&nbsp;&nbsp;::&nbsp;</b>$oc[1]&nbsp;&nbsp;&nbsp;<i><a href='index.php?nomeArquivo=rel_recebimento.php&oc=$oc[0]&ano=".$_GET['ano']."&mes=".$_GET['mes']."' style='text-decoration:none'>Detalhar</a></td></tr>";		
		}
	}

	echo "</table><p>";

?>




