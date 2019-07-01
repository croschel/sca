<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
	$mes = date(m);
	switch ($mes) {
		case 1: $mes='Jan'; break;
		case 2: $mes='Fev'; break;
		case 3: $mes='Mar'; break;
		case 4: $mes='Abr'; break;
		case 5: $mes='Mai'; break;
		case 6: $mes='Jun'; break;
		case 7: $mes='Jul'; break;
		case 8: $mes='Ago'; break;
		case 9: $mes='Set'; break;
		case 10: $mes='Out'; break;
		case 11: $mes='Nov'; break;
		case 12: $mes='Dez'; break;
	}
?>

<Script Language="JavaScript">
function getStates(what) {
   if (what.selectedIndex != '') {
      var cod_om = what.value;
      document.location=('index.php?nomeArquivo=cons_evaporacao.php&cod_om=' + cod_om);
   }
}
</script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<?php 
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

	echo "<table border='0' width='80%' align='left' valign='top'>";
	echo "<tr><td colspan='4' align='center'>&nbsp;</td></tr>";
	echo "<tr><td colspan='4' align='center'><font size='5'><b><i>Combustível Evaporado</td></tr>";
	echo "<tr bgcolor='$cor1'><font size='4'><td colspan='4' align='center'>&nbsp;</td></tr>";
	echo "<tr bgcolor='$cor3' align='center'><td colspan='4'><i>
					<a href='index.php?nomeArquivo=cons_evaporacao.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".($_GET['mes']-1)."' style='text-decoration:none' title='Mês Anterior - $mes_a'>&nbsp;Anterior&nbsp;</a>&nbsp;
					<font size='4' color='blue'><b>- $mes / ".$_GET[ano]."</font></b> - 
					<a href='index.php?nomeArquivo=cons_evaporacao.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".($_GET['mes']+1)."' style='text-decoration:none' title='Próximo Mês - $p_mes'>&nbsp; Próximo&nbsp;</a></td></tr>";	   

  //Seleciona a relação de Reservatorios

  	if (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
		$qry = "select o.sigla, e.reservatorio_codigo, c.nome
				from om o, estoque e, reservatorio r, combustivel c
				where o.codom=r.om_codom and c.codigo=r.combustivel_codigo
				and r.codigo=e.reservatorio_codigo
				and d.om_codom='".$_SESSION['codom']."'
				order by e.reservatorio_codigo";
	} elseif ((($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) or ($_SESSION['perfil']=='ABASTECEDOR')) {
		$qry = "select o.sigla, r.codigo, c.nome
				from reservatorio r, om o, combustivel c
				where o.codom=r.om_codom and c.codigo=r.combustivel_codigo
				and r.om_codom='".$_SESSION['codom']."'
				order by r.codigo";
	} elseif(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$qry = "select o.sigla, r.codigo, c.nome
				from reservatorio r, om o, combustivel c
				where o.codom=r.om_codom and c.codigo=r.combustivel_codigo
				and (o.oc='".$_SESSION['codom']."'
				or o.oc='".$_SESSION['oc']."')
				order by r.codigo";

	} else {
		$qry = "select o.sigla, r.codigo, c.nome
				from reservatorio r, om o, combustivel c
				where o.codom=r.om_codom and c.codigo=r.combustivel_codigo
				order by r.codigo";

	}

	$resultado = mysql_query($qry);

	while ($row = mysql_fetch_array($resultado, MYSQL_NUM)) {
		$total=0;
		echo "<tr bgcolor='$cor1'><td colspan=2 width='50%'><coluna>&nbsp;Reservatório:</b> <font color='$cor_submenu'> Nº $row[1] - $row[0] - $row[2] </td><td width='25%' align='center'><coluna>Data</td><td width='25%' align='center'><coluna>Quantidade</td></tr>";
			$query = "select v.data_registro, sum(v.qtd_evaporada)
					from estoque t, evaporacao v
					where t.nr_repasse=v.nr_repasse
						and t.om_codom=v.om_codom
						and v.data_registro between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31'
						and t.reservatorio_codigo=".$row[1]."
					group by v.data_registro
					order by v.data_registro";
		$result = mysql_query($query);
		
		while ($qtd = mysql_fetch_array($result, MYSQL_NUM)) {
			$dt_sang=substr($qtd[0],8,2)."/".substr($qtd[0],5,2)."/".substr($qtd[0],0,4);
			$dt_reg=substr($qtd[0],0,4).substr($qtd[0],5,2).substr($qtd[0],8,2);
			$qtd_evap = str_replace(".", ",",$qtd[1]);
			if (($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
				echo "<tr ><td colspan='2'></td><td align='center' bgcolor='$cor3'><lin>$dt_sang</td><td align='right' bgcolor='$cor3'><lin>".number_format($qtd[1],3,',','.')." Lts &nbsp;</td></tr>";
			} else {
				echo "<tr ><td colspan='2'></td><td align='center' bgcolor='$cor3'><lin><a href='index.php?nomeArquivo=cons_evap_data.php&posto=$row[0]&dt_reg=$dt_reg&res=$row[1]&comb=$row[2]' style='text-decoration:none'>$dt_sang</td><td align='right' bgcolor='$cor3'><lin>".number_format($qtd[1],3,',','.')." Lts &nbsp;</td></tr>";
			}
			$total=$total+$qtd[1];
		}
		echo "<tr><td colspan='2'></td><td align='center' bgcolor='$cor3'><coluna>Total</td><td align='right' bgcolor='$cor3'><font color='blue'><lin>".number_format($total,3,',','.')." Lts &nbsp;</td></tr>";
		echo "<tr><td colspan='4'>&nbsp;</td></tr>";
	}
?>
</table>
