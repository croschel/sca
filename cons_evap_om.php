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
<script language="JavaScript" type="text/javascript">
function mascara(dt_sangria){
              var mdata = '';
              mdata = mdata + dt_sangria;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[0].dt_sangria.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[0].dt_sangria.value = mdata;
              }
              if (mdata.length == 10){
                  return true;
              }
          }
</script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<table border="0" width="60%" align="left" valign="top">
	<tr><td colspan=4 align='center'>&nbsp;</td></tr>
	<tr><td colspan=4 align='center'><font size=5><b><i>Combustível Evaporado do(a) <?echo $_SESSION['unidade']?></td></tr>
	<tr><td colspan=4 align='center'><hr></td></tr>
	<tr><td colspan=4 ><b><i>Período:</b><font color='blue'> 01 º <?echo date(d)." ".$mes." ".date(y)?></td></tr>

	<tr bgcolor="#eeeeee">
	<?php    
        //Conexão ao Banco de Dados
    	include "conexao.php";
    	//Seleciona a relação de Reservatorios
    	$resultado = mysql_query("select o.sigla, r.codigo
				from reservatorio r, om o
				where o.codom=r.om_codom
				order by r.codigo");
	while ($row = mysql_fetch_array($resultado, MYSQL_NUM)) {
		$total=0;
		echo "<tr bgcolor='#4A708B'><td colspan=2 width='50%'><b>&nbsp;Reservatório:</b> <font color='blue'> Nº $row[1] - $row[0]</td><td width='25%' align='center'><b>Data</td><td width='25%' align='center'><b>Quantidade</td></tr>";
		$result = mysql_query("select data_registro, round(sum(qtd_evaporada),3)
					from evaporacao
					where data_registro between '".date(Y)."-".date(m)."-01' and '".date(Y)."-".date(m)."-".date(d)."'
					and disponibilidade_reservatorio_codigo=".$row[1]."
					and disponibilidade_om_codom='".$_SESSION['codom']."'
					group by data_registro
					order by data_registro");
		while ($qtd = mysql_fetch_array($result, MYSQL_NUM)) {
			$dt_sang=substr($qtd[0],8,2)."/".substr($qtd[0],5,2)."/".substr($qtd[0],0,4);
			$dt_reg=substr($qtd[0],0,4).substr($qtd[0],5,2).substr($qtd[0],8,2);
			$qtd_evap = str_replace(".", ",",$qtd[1]);
			echo "<tr><td colspan=2></td><td align='center' bgcolor='#7EC0EE'><a href='index.php?nomeArquivo=cons_evap_data.php&&dt_reg=$dt_reg&&res=$row[1]'>$dt_sang</td><td align='right' bgcolor='#7EC0EE'>$qtd_evap Lts &nbsp;</td></tr>";
		$total=$total+$qtd[1];
		}
		$total = str_replace(".", ",",$total);
		echo "<tr><td colspan=2></td><td align='center' bgcolor='#7EC0EE'><i><b>Total</td><td align='right' bgcolor='#7EC0EE'><font color='blue'>$total Lts &nbsp;</td></tr>";
		echo "<tr><td colspan=4>&nbsp;</td></tr>";
	}
	?>
</table>
