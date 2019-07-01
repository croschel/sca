<?php
	include "verifica_sessao.php";
	/*Seeleciona a quantidade recebida*/
	$query="SELECT r.nr_nota_fiscal, r.dt_rec, c.nome, r.qtd_rec, r.combustivel_codigo, r.reservatorio_codigo, o.sigla, r.oc
		FROM combustivel c, recebimento r, reservatorio rs, om o
		WHERE c.codigo=r.combustivel_codigo
		and o.codom=rs.om_codom
		and rs.codigo=r.reservatorio_codigo
		and r.combustivel_codigo=".$_GET['combustivel']."
		and r.nr_nota_fiscal='".$_GET['nota_fiscal']."'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$ano=substr($row[1],0,4);
	$mes=substr($row[1],5,2);
	$dia=substr($row[1],8,2);
	$dt_nota = $dia."/".$mes."/".$ano;
	
	/*Seeleciona a quantidade ja distribuida*/
	$query="SELECT sum(qtd_destinada) FROM estoque 
		WHERE combustivel_codigo=".$_GET['combustivel']."
		and nr_nota_fiscal='".$_GET['nota_fiscal']."'";
	$result = mysql_query($query,$conexao);
	$qtd_dist = mysql_fetch_row($result);
	$qtd_disp = ($row[3]-$qtd_dist[0]);

echo "<div align='left'>";
echo "<br>";
echo "<table width='100%'>";
echo "<tr><td>";
echo "<table border='0' width='100%' align='left' valign='top'>";
echo "<tr><td COLSPAN='5' align='center'><t1>Gerenciamento de Cotas de Combustíveis</td></tr>";
echo "<tr><td COLSPAN='5'>&nbsp;</td></tr>";
echo "<tr><td><b>Combustível: <I><font color='$cor_menu'>$row[2]</I></td>";
		echo "<td><b>Reservatório do(a): <I><font color='$cor_menu'>$row[6]</font></td>";
		echo "<td><b>Nota Fiscal Nº: <I><font color='$cor_menu'>$row[0]</td></tr>";
echo "<tr><td><b>Qtd Recebida: <I><font color='red'>$row[3] Lts</td><td><b>Data Recebimento: <I><font color='$cor_menu'>$dt_nota</td>
		<td><b>";
		if($_GET['tela']=='rel') {		
			echo "<a href='index.php?nomeArquivo=rel_recebimento.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".$_GET['mes']."' style='text-decoration:none'>::Retornar::</a>";
		} else {
			echo "<a href='index.php?nomeArquivo=cons_recebimento.php&m=".$_GET['m']."&ord=".$_GET['ord']."' style='text-decoration:none'>::Retornar::</a>";
		}
		echo "</td></tr>";
echo "<tr><td COLSPAN='5'><hr></td></tr>";
echo "</table>";
echo "</td></tr>";
echo "<tr><td>";
echo "<table border='1' width='90%' align='left'>";

		if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='ADMINISTRADOR')){
			/*Seeleciona a quantidade disponivel na cota do OC neste recebimento*/
			$qry="SELECT qtd_atual, nr_repasse FROM estoque 
				WHERE combustivel_codigo=".$_GET['combustivel']."
				and nr_nota_fiscal='".$_GET['nota_fiscal']."'
				and om_codom='".$_SESSION['codom']."'";
			$result1 = mysql_query($qry);
			$qtd_atual_oc = mysql_fetch_row($result1);
			echo "<tr bgcolor='$cor1'>
						<td COLSPAN='7' align='center'><b>DISTRIBUIÇÃO DO RECEBIMENTO</b></td><td align='center'>"; if (($qtd_atual_oc[0]>0) and ($row[7]==$_SESSION['codom'])) {echo "<a href='index.php?nomeArquivo=cad_cota.php&nr=$row[0]&combustivel=$row[2]&cod_comb=$row[4]&reservatorio=$row[5]&local=$row[6]&cota=0&nr_repasse=$qtd_atual_oc[1]' style='text-decoration:none'>&nbsp;<font color='$cor_submenu'><b>Nova Cota</b></font><IMG SRC='./imagens/novo.png' width='15' height='15' border=0>";} else {echo " ";} 
			echo "</td></tr>";
			echo "<tr align='center' bgcolor='$cor2'>
						<td width='10%'><coluna>Data</td>
						<td width='20%'><coluna>OM </td>
						<td width='10%'><coluna>Qtd Rec </td>
						<td width='10%'><coluna>Qtd Atual </td>
						<td width='10%'><coluna>Tipo</td>
						<td width='10%'><coluna>Remessa </td>
						<td width='20%'><coluna>Obs </td>
						<td width='20%'><coluna>Opções</td>
					</tr>";
		} elseif (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) {
			echo "<tr bgcolor='$cor1'><td COLSPAN='8' align='center'><b>DISTRIBUIÇÃO DO RECEBIMENTO</b></td></tr>";
			echo "<tr align='center' bgcolor='$cor2'>
						<td width='10%'><coluna>Data</td>
						<td width='20%'><coluna>OM </td>
						<td width='10%'><coluna>Qtd Rec </td>
						<td width='10%'><coluna>Qtd Atual </td>
						<td width='10%'><coluna>Tipo</td>
						<td width='10%'><coluna>Remessa </td>
						<td width='20%'><coluna>Obs </td>
						<td width='20%'><coluna>Opções</td>
					</tr>";
		}
    	/*Seleciona a relaçao de repasses da nota fiscal selecionada*/
    	$qry_cota = "SELECT o.sigla, e.qtd_destinada, o.codom, e.nr_remessa, t.tipo_cota, e.qtd_atual, e.nr_repasse, e.obs, e.dt_repasse, o.tipo, t.cod_tp_cota, o.oc
				FROM om o, tipo_cota t right join estoque e on (t.cod_tp_cota=e.cod_tp_cota) 
				WHERE o.codom = e.om_codom  
				and e.combustivel_codigo='".$_GET['combustivel']."'
				and e.nr_nota_fiscal='".$_GET['nota_fiscal']."'
				ORDER BY e.dt_repasse";
		$result = mysql_query($qry_cota);
    	while ($cota = mysql_fetch_array($result, MYSQL_NUM)) {
			echo "<tr bgcolor='$cor3' class='marcar_linha'>
					<td align='center'><lin>".substr($cota[8],8,2)."/".substr($cota[8],5,2)."/".substr($cota[8],0,4).substr($cota[8],10,6)."</td>
					<td><lin>$cota[0]</td><td align='right'><lin>$cota[1] Lts&nbsp;</td>
					<td align='right'><lin>".round($cota[5],0)." Lts&nbsp;</td>";
			if($cota[9]=='OC') {
				echo "<td align='center'><lin>-</td>";
			} else {
				echo "<td align='center'><lin>$cota[4]</td>";
			}
			echo "<td align='center'><lin>$cota[3]</td><td><lin>$cota[7]</td>";
			echo "<td align='center'><lin>"; 
//			if( ($cota[10]!=0) and ($cota[5]>1) and (($row[9]!='OC') and substr($cota[8],0,10)==date('Y-m-d')) ){
			if (($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='ADMINISTRADOR')){
				if( ($cota[10]!=0) and ($cota[5]>1) and ($row[7]==$_SESSION['codom']) and ($cota[9]!='OC')){
					echo "<a href='index.php?nomeArquivo=remover_registro.php&ch=nr_repasse&valor=$cota[6]&ch1=om_codom&valor1=$cota[2]&valor2=$row[0]&valor3=$row[4]&reserv=$row[5]&qtd=$cota[1]&qtd_atual=$cota[5]&tab=estoque&registro=o repasse nº ".substr($cota[6],0,4)."-".substr($cota[6],4)." ($cota[4]) de ".round($cota[5],0)." lts do(a) $cota[0]&arquivo=remover_estoque.php' style='text-decoration:none'>Remover</a><br>"; 
				} elseif (($cota[9]=='OC') and substr($cota[8],0,10)==date('Y-m-d') and $cota[2]!=$_SESSION['codom']){
					echo "<a href='index.php?nomeArquivo=remover_registro.php&ch=nr_repasse&valor=$cota[6]&ch1=om_codom&valor1=$cota[2]&valor2=$row[0]&valor3=$row[4]&reserv=$row[5]&qtd=$cota[1]&qtd_atual=$cota[5]&tab=estoque&registro=o repasse nº ".substr($cota[6],0,4)."-".substr($cota[6],4)." ($cota[4]) de ".round($cota[5],0)." lts do(a) $cota[0]&arquivo=remover_estoque.php' style='text-decoration:none'>Remover</a><br>"; 
				}
			}
			if (($_SESSION['perfil']=='ADMINISTRADOR *') and ($cota[2]==$_SESSION['codom']) and ($cota[5]>1)){
				echo "<a href='index.php?nomeArquivo=cad_cota.php&nr=$row[0]&combustivel=$row[2]&cod_comb=$row[4]&reservatorio=$row[5]&local=$row[6]&cota=$cota[10]&nr_repasse=$cota[6]' style='text-decoration:none'>Repasse</a><br>";
			}
			echo "<a href=\"#\" title='Listar destino do combustível deste repasse.' onClick=\"window.open('destino_comb.php?om=$cota[0]&dt=$cota[8]&codom=$cota[2]&oc=$row[7]&rep=$cota[6]&nota_fiscal=".$_GET['nota_fiscal']."','janela','location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=0,width=800,height=600');\" style='text-decoration:none'>Visualizar</a>"; 
			echo "</td>";
			echo "</tr>";
		$tp_comb='';
    	}

    	$num_rows = mysql_num_rows($result);
    	mysql_free_result($result);

echo "</table>";
echo "</td></tr>";
echo "</table>";
//  
?>