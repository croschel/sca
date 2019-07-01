<?php
  	if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
   
	if(!$_GET['ordem']) {
		$_GET['ordem']="1";
	}
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<Script Language="JavaScript">
function getRel(cota,tp_cota,ordem) {
	document.location=('index.php?nomeArquivo=rel_autz.php&cota=' + cota + '&tp_cota=' + tp_cota + '&ordem=' + ordem);
}
</Script>

<?php 
	echo "<font size='5'><br><b>Relatório de Abastecimentos Autorizados e Não Executados</b></font> <br>";
	echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<form name='frm_autz' method='GET' action='index.php?nomeArquivo=rel_autz.php&ordem=".$_GET['ordem']."' >";
	echo "<input type='hidden' name='nomeArquivo' value='rel_autz.php'>";
	echo "<input type='hidden' name='ordem' value='".$_GET['ordem']."'>";
	/*/Relação de Combustivel*/

	echo "<tr align='center' >
				<td colspan='9' ><br>";	
			
	/*/Relação de OM detentora combustível*/
	$qrycota = "select distinct om.codom, om.sigla 
					from om, abastecimento a
					where om.codom=a.cota_om
						AND a.qtd_abastecida is null ";
	
	if(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$qrycota .= "and a.cota_om in (select cota_om from abastecimento a, viatura v, om where om.codom=v.om_codom and v.eb=a.viatura_eb and om.subordinacao='".$_SESSION['codom']."') ";
	} elseif(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
		$qrycota = "select distinct om.codom, om.sigla 
					from om, (select * from abastecimento ab, viatura v 
									where v.eb=ab.viatura_eb and ab.cota_om='".$_SESSION['codom']."' and ab.qtd_abastecida is null ) a
					where om.codom=a.cota_om
						AND a.qtd_abastecida is null ";

	} elseif(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) {
		$qrycota .= "and (a.reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')
							or a.cota_om in (select distinct cota_om from viatura v, abastecimento a where v.eb=a.viatura_eb and om_codom='".$_SESSION['codom']."')) ";

	} elseif($_SESSION['perfil']=='ABASTECEDOR') {
		$qrycota .= "and a.reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."') ";
	} 
	
	$qrycota .= "order by abs(om.sigla), om.sigla";

	$rescota = mysql_query($qrycota);
			echo "<b><I>Cota do(a): </b></i>";
			echo "<select name='cota' onChange='getRel(this.value,\"".$_GET['tp_cota']."\",\"".$_GET['ordem']."\");'>";
			echo "<option value='Todas' >TODAS</option>";
			while ($cota = mysql_fetch_array($rescota, MYSQL_NUM)) {
				echo "<option value='$cota[0]' "; if($_GET['cota']==$cota[0]) {echo "selected";} echo "> $cota[1] </option>";
			}
			echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			
	/*/Relação de Tipos de Cota */
	$qrycota = "SELECT *
					FROM tipo_cota
					where cod_tp_cota<>'0'
					order by 2";

	$rescota = mysql_query($qrycota);
			echo "<b><I>Tipo de Combustível: </b></i>";
			echo "<select name='tp_cota' id='tp_cota' onChange='getRel(\"".$_GET['cota']."\",this.value,\"".$_GET['ordem']."\");'>";
			echo "<option value='Todas' >TODAS</option>";
			while ($cota = mysql_fetch_array($rescota, MYSQL_NUM)) {
				echo "<option value='$cota[0]' "; if($_GET['tp_cota']==$cota[0]) {echo "selected";} echo "> $cota[1] </option>";
			}
			echo "</select>
				</td>
			</tr>";
	echo "</form>";
	echo "<tr align='center' bgcolor='$cor1'>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='2'){echo "2 desc";}else{echo "2";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Viatura</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='3'){echo "3 desc";}else{echo "3";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Data</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='4'){echo "4 desc";}else{echo "4";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Comb</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='10'){echo "10 desc";}else{echo "10";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Tp Comb</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='5'){echo "5 desc";}else{echo "5";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Local</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='6'){echo "6 desc";}else{echo "6";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Qtd</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='8'){echo "8 desc";}else{echo "8";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Cota da OM</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='7'){echo "7 desc";}else{echo "7";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Missão</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&cota=".$_GET['cota']."&tp_cota=".$_GET['tp_cota']."&ordem="; if ($_GET['ordem']=='11'){echo "11 desc";}else{echo "11";} echo "' style='text-decoration:none'><font color='$cor_submenu'>Tipo Abastecimento</font></td>";
	echo "</tr>";

   /*Seleciona a relação de abastecimentos*/
	$query = "SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, a.reservatorio_codigo, a.qtd_autorizada, a.missao, a.cota_om, a.local, t.tipo_cota, a.tp_abastecimento
				FROM abastecimento a, om o, combustivel c, viatura v, tipo_cota t, reservatorio r
				WHERE o.codom = v.om_codom
				AND r.codigo = a.reservatorio_codigo
				AND c.codigo = r.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND t.cod_tp_cota=a.cod_tp_cota
				AND a.qtd_abastecida is null ";
				
	if ($_SESSION['perfil']=='ABASTECEDOR') {
		$query .= "AND a.reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."') ";			

	} elseif (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) {
		$query .= "AND (a.cota_om='".$_SESSION['codom']."' or a.reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')) ";

	} elseif (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')){
		$query .= "AND a.cota_om='".$_SESSION['codom']."' ";

	} elseif (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){
		$query .= "AND (o.subordinacao='".$_SESSION['codom']."' or o.codom='".$_SESSION['codom']."') ";

	} 
	//FILTRA PELA OM QUE SERá RETIRADO O COMBUSTÍVEL
	if(($_GET['cota']!='Todas') and ($_GET['cota']!='')) {
		$query.="AND a.cota_om='".$_GET['cota']."' ";
	}

	//FILTRA PELO TIPO DE COMBUSTÍVEL
	if(($_GET['tp_cota']!='Todas') and ($_GET['tp_cota']!='')) {
		if($_GET['tp_cota']==0) {
			$_GET['tp_cota']="0,1";
		} elseif($_GET['tp_cota']==1) {
			$_GET['tp_cota']="0,1";
		}
		$query.=" and a.cod_tp_cota in(".$_GET['tp_cota'].") ";	
	}
	$query.="order by ".$_GET['ordem'];
	$tot_gas=$tot_die=0;
	$result = mysql_query($query);
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$ano=substr($row[2],0,4);
		$mes=substr($row[2],5,2);
		$dia=substr($row[2],8,2);
		$hora=substr($row[2],11,5);
		$dt_nota = $dia."/".$mes."/".$ano;
		/*Seeleciona a OM que saiu o combustivel*/
		$busca="select sigla from om where codom='".$row[7]."'";
		$resultado = mysql_query($busca,$conexao);
		$cota = mysql_fetch_row($resultado);

		/*Seleciona a sigla da OM do reservatorio*/
		$busca="select o.sigla from om o, reservatorio r where o.codom=r.om_codom and r.codigo='".$row[4]."'";
		$resultado = mysql_query($busca,$conexao);
		$local = mysql_fetch_row($resultado);
		if($local[0]=='') {
			$cota[0]=$local[0]=$row[8];
		}
		echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'><!--td align='left'><font size='2'>$row[0]</td--><td><font size='2'>$row[1] - $row[0]</td>
		<td><font size='2'>$dt_nota</td><td><font size='2'>$row[3]</td><td><font size='2'>$row[9]</td><td><font size='2'>$local[0]</td>
		<td align='right'><font size='2'>$row[5] Lts&nbsp;</td><td align='left'><font size='2'>$cota[0]</td>
		<td align='left'><font size='2'>$row[6] </td><td align='left'><font size='2'>$row[10] </td></tr>";
		if ($row[3]=='Gasolina'){
			$tot_gas=$tot_gas+$row[5];
		} elseif ($row[3]=='Diesel'){
			$tot_die=$tot_die+$row[5];
		}
   }
	echo "<tr><td align='right'><font size='2'><i>Total de Gasolina: </td><td><font size='2'><b><i> &nbsp;&nbsp;".number_format($tot_gas, 0, ',', '.')."</b> Litros</td><td colspan=2 align=right><font size='2'><i>Total de Diesel:</td><td><font size='2'><b><i>&nbsp;&nbsp;".number_format($tot_die, 0, ',', '.')."</b> Litros</td></tr>";
    	$num_rows = mysql_num_rows($result);
    	mysql_free_result($result);

	echo "</tr>";
echo "</table>";
?>