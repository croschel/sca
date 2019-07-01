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
	echo "<table border='0' width='80%' align='left' valign='top'>";
	echo "<tr><td colspan='3' align='center'>&nbsp;</td></tr>";
	echo "<tr><td colspan='3' align='center'><t1>Detalhamento do Combustível Retirado</td></tr>";
	echo "<tr bgcolor='$cor1'><td colspan='3' align='center'>&nbsp;</td></tr>";
	
	$total=0;
	$data1= substr($_GET['dt_reg'],8,2)."/".substr($_GET['dt_reg'],5,2)."/".substr($_GET['dt_reg'],0,4);
	echo "<tr align='center'><td colspan='3'><br><coluna>Reservatório:</b>  Nº ".$_GET['res']." - ".$_GET['posto']." </font><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Combustível:</b> ".$_GET['comb']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Data:</b> $data1</td></tr>";
	echo "<tr bgcolor='$cor2' align='center'><td><b>OM</td><td><b>Cota</td><td><b>Quantidade</td></tr>";
echo	$qry1 = "select o.sigla, sum(s.qtd_retirada), t.tipo_cota
					from om o, estoque e, sangria s, tipo_cota t
					where o.codom=e.om_codom 
					and e.om_codom=s.om_codom
					and e.nr_repasse=s.nr_repasse
					and t.cod_tp_cota=e.cod_tp_cota
					and e.reservatorio_codigo='".$_GET['res']."' 
					and s.data_sangria='".$_GET['dt_reg']."' 
					group by o.sigla, t.tipo_cota
					order by o.sigla
					";
	$result = mysql_query($qry1);
	while ($qtd = mysql_fetch_array($result, MYSQL_NUM)) {
		$qtd_evap = str_replace(".", ",",$qtd[1]);
		echo "<tr bgcolor='$cor3' class='marcar_linha'><td>$qtd[0]</td><td align='center'>$qtd[2]</td><td align='right'>$qtd_evap Lts &nbsp;</td></tr>";
		$total=$total+$qtd[1];
	}
	echo "<tr bgcolor='$cor2'><td align='center' colspan='2'><i><b>Total</td><td align='right'><b>".number_format($total,0,',','.')." Lts &nbsp;</td></tr>";
	
	echo "<tr><td colspan='3' align='center'><hr></td></tr>";
	echo "<tr><td colspan='3' align='center'><input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
	echo "</table>";
?>


