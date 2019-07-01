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

<?php    

	$total=0;
	$data1= substr($_GET['dt_reg'],6,2)."/".substr($_GET['dt_reg'],4,2)."/".substr($_GET['dt_reg'],0,4);
	
	echo "<table border='0' width='80%' align='left' valign='top'>";
	echo "<tr><td colspan='3' align='center'>&nbsp;</td></tr>";
	echo "<tr><td colspan='3' align='center'><t1>Detalhamento do Combustível Evaporado</td></tr>";
	echo "<tr bgcolor='$cor1'><td colspan='3' align='center'>&nbsp;</td></tr>";

	echo "<tr align='center'><td colspan='3'><br><coluna>Reservatório:</b>  Nº ".$_GET['res']." - ".$_GET['posto']." </font><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Combustível:</b> ".$_GET['comb']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Data:</b> $data1</td></tr>";
	echo "<tr bgcolor='$cor2' align='center'><td><b>OM</td><td><b>Cota</td><td><b>Quantidade</td></tr>";
	$qry1 = "select o.sigla, sum(v.qtd_evaporada), c.tipo_cota
				from om o, estoque t, evaporacao v, tipo_cota c
				where o.codom=t.om_codom and c.cod_tp_cota=t.cod_tp_cota
					and t.nr_repasse=v.nr_repasse
					and t.om_codom=v.om_codom
					and t.reservatorio_codigo='".$_GET['res']."'
					and v.data_registro='".substr($_GET['dt_reg'],0,4)."-".substr($_GET['dt_reg'],4,2)."-".substr($_GET['dt_reg'],6,2)."' 
				group by v.om_codom, t.cod_tp_cota
				order by o.sigla";
	$result = mysql_query($qry1);
	while ($qtd = mysql_fetch_array($result, MYSQL_NUM)) {
		$qtd_evap = str_replace(".", ",",$qtd[1]);
		echo "<tr bgcolor='$cor3' class='marcar_linha'><td>$qtd[0]</td><td>$qtd[2]</td><td align='right'>$qtd_evap Lts &nbsp;</td></tr>";
		$total=$total+$qtd[1];
	}
	echo "<tr bgcolor='$cor2'><td align='center' colspan='2'><i><b>Total</td><td align='right'><b>".number_format($total,3,',','.')." Lts &nbsp;</td></tr>";
	
	echo "<tr><td colspan='3' align='center'><hr></td></tr>";
	echo "<tr><td colspan='3' align='center'><input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
	echo "</table>";
?>

