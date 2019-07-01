<!doctype html>
<html>
	<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Line Chart</title>
		<script src="./Chart.js-master/Chart.js"></script>
		<style type="text/css">
		
		</style>
	</head>
	<body>
	
<Script Language="JavaScript">
function getOM(what) {
      var cod_om = what.value;
      document.location=('index.php?nomeArquivo=grafico_evaporacaos.php&codom=' + cod_om);
}
</Script>

<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";
	echo "<center><br><t1>Gráfico de evaporacaos</t1> <p> </center>";

	/*/Relação de OM*/
	if(($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
		$qryoc = "select codom, sigla from om where tipo in ('OC','Integrante') order by oc, sigla";
	} elseif(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$qryoc = "select codom, sigla from om where oc='".$_SESSION['codom']."'";
		$_GET['codom']=$_SESSION['codom'];
	} elseif(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
		$qryoc = "select codom, sigla from om where codom='".$_SESSION['codom']."'";
		$_GET['codom']=$_SESSION['codom'];
	} elseif(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) {
		$qryoc = "select codom, sigla from om where codom in (select om_codom from estoque where reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')) or codom='".$_SESSION['codom']."'";
	} elseif($_SESSION['perfil']=='ABASTECEDOR') {
		$qryoc = "select codom, sigla from om where codom in (select om_codom from estoque where reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."))'";
		$_GET['codom']=$_SESSION['codom'];
	}
	$result = mysql_query($qryoc);
	echo "OM: <select name='codom' onChange='getOM(this);'>";	
				while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$om[0]'";
							if ($om[0]==$_GET['codom'])
							{
								echo "selected";
							}
							echo ">$om[1]</option>";
						}
				echo "</select><p>";
	if($_GET['ano']=='') { $_GET['ano']=date('Y');}
	if($_GET['mes']=='') { $_GET['mes']=date('m');}
	if($_GET['mes']>'12') { $_GET['mes']='01'; $_GET['ano']=$_GET['ano']+1;}
	if($_GET['mes']<'1') { $_GET['mes']='12'; $_GET['ano']=$_GET['ano']-1;}

 	switch($_GET['mes']) {
		case 1: $mes_a = 'Dezembro / '.($_GET['ano']-1);$mes = 'Janiero'; $p_mes = 'Fevereiro / '.$_GET['ano']; $dias=31; break;
		case 2: $mes_a = 'Janiero / '.$_GET['ano'];$mes = 'Fevereiro'; $p_mes = 'Março / '.$_GET['ano']; $dias=28; break;
		case 3: $mes_a = 'Fevereiro / '.$_GET['ano'];$mes = 'Março'; $p_mes = 'Abril / '.$_GET['ano']; $dias=31; break;
		case 4: $mes_a = 'Março / '.$_GET['ano'];$mes = 'Abril'; $p_mes = 'Maio / '.$_GET['ano']; $dias=30; break;
		case 5: $mes_a = 'Abril / '.$_GET['ano'];$mes = 'Maio'; $p_mes = 'Junho / '.$_GET['ano']; $dias=31; break;
		case 6: $mes_a = 'Maio / '.$_GET['ano'];$mes = 'Junho'; $p_mes = 'Julho / '.$_GET['ano']; $dias=30; break;
		case 7: $mes_a = 'Junho / '.$_GET['ano'];$mes = 'Julho'; $p_mes = 'Agosto / '.$_GET['ano']; $dias=31; break;
		case 8: $mes_a = 'Julho / '.$_GET['ano'];$mes = 'Agosto'; $p_mes = 'Setembro / '.$_GET['ano']; $dias=31; break;
		case 9: $mes_a = 'Agosto / '.$_GET['ano'];$mes = 'Setembro'; $p_mes = 'Outubro / '.$_GET['ano']; $dias=30; break;
		case 10: $mes_a = 'Setembro / '.$_GET['ano'];$mes = 'Outubro'; $p_mes = 'Novembro / '.$_GET['ano']; $dias=31; break;
		case 11: $mes_a = 'Outubro / '.$_GET['ano'];$mes = 'Novembro'; $p_mes = 'Dezembro / '.$_GET['ano']; $dias=30; break;
		case 12: $mes_a = 'Novembro / '.$_GET['ano'];$mes = 'Dezembro'; $p_mes = 'Janiero / '.($_GET['ano']+1); $dias=31; break;
	}
	echo "<a href='index.php?nomeArquivo=grafico_evaporacaos.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".($_GET['mes']-1)."&codom=".$_GET['codom']."' style='text-decoration:none' title='Mês Anterior - $mes_a'>&nbsp;Anterior&nbsp;</a>&nbsp;
			<font size='4' color='blue'><b>- $mes / ".$_GET[ano]."</font></b> - 
			<a href='index.php?nomeArquivo=grafico_evaporacaos.php&oc=".$_GET['oc']."&ano=".$_GET['ano']."&mes=".($_GET['mes']+1)."&codom=".$_GET['codom']."' style='text-decoration:none' title='Próximo Mês - $p_mes'>&nbsp; Próximo&nbsp;</a>";	   

?>
		<div style="width:70%">
			<div>
				<canvas id="canvas" height="40" width="80"></canvas>
			</div>
		<div style="width:20%" align="left" id="legenda">
		</div>
		</div>

<?php
	include "conexao.php";
echo	$qry = "SELECT substr(a.dt,1,10) dt, ifnull(SUM( ad.qtd_evaporada ),0) adm, ifnull(SUM( op.qtd_evaporada ),0) op, ifnull(SUM( it.qtd_evaporada ),0) it, ifnull(SUM( e.qtd_evaporada ),0) e,
							 ifnull(SUM( e.qtd_evaporada ),0) m, ifnull(SUM( e.qtd_evaporada ),0) ap, ifnull(SUM( e.qtd_evaporada ),0) t
					FROM (SELECT DATE_FORMAT( ADDDATE( '".$_GET['ano']."-".$_GET['mes']."-01', @num := @num +1 ) ,  '%Y-%m-%d' ) dt 
							FROM auditoria, (SELECT @num :=-1)num
							LIMIT $dias ) a left join
							(SELECT substr(dt_registro,1,10) dt, qtd_evaporada
							FROM  `evaporacao` v, estoque e
							WHERE e.nr_repasse dt_registro between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31' and cod_tp_cota='1' and qtd_evaporada IS NOT NULL and cota_om='".$_GET['codom']."') ad on (a.dt = ad.dt)	left join
							(SELECT substr(dt_registro,1,10) dt, qtd_evaporada
							FROM  `evaporacao`
							WHERE dt_registro between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31' and cod_tp_cota='2' and qtd_evaporada IS NOT NULL and cota_om='".$_GET['codom']."') op on (a.dt = op.dt)	left join
							(SELECT substr(dt_registro,1,10) dt, qtd_evaporada
							FROM  `evaporacao`
							WHERE dt_registro between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31' and cod_tp_cota='3' and qtd_evaporada IS NOT NULL and cota_om='".$_GET['codom']."') it on (a.dt = it.dt)	left join
							(SELECT substr(dt_registro,1,10) dt, qtd_evaporada
							FROM  `evaporacao`
							WHERE dt_registro between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31' and cod_tp_cota='4' and qtd_evaporada IS NOT NULL and cota_om='".$_GET['codom']."') e on (a.dt = e.dt)	left join
							(SELECT substr(dt_registro,1,10) dt, qtd_evaporada
							FROM  `evaporacao`
							WHERE dt_registro between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31' and cod_tp_cota='5' and qtd_evaporada IS NOT NULL and cota_om='".$_GET['codom']."') m on (a.dt = m.dt)	left join
							(SELECT substr(dt_registro,1,10) dt, qtd_evaporada
							FROM  `evaporacao`
							WHERE dt_registro between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31' and cod_tp_cota='6' and qtd_evaporada IS NOT NULL and cota_om='".$_GET['codom']."') ap on (a.dt = ap.dt)	left join
							(SELECT substr(dt_registro,1,10) dt, qtd_evaporada
							FROM  `evaporacao`
							WHERE dt_registro between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-31' and cod_tp_cota='7' and qtd_evaporada IS NOT NULL and cota_om='".$_GET['codom']."') t on (a.dt = t.dt)								
					GROUP BY substr(a.dt,1,10)";
	$consultar = mysql_query($qry);
 
	$i = 1;

	while($resultado = mysql_fetch_array($consultar)){
		$data = $data."','".substr($resultado['dt'],8,2)."/".substr($resultado['dt'],5,2)."/".substr($resultado['dt'],0,4);
		$adm = $adm.",".$resultado['adm'];
		$op = $op.",".$resultado['op'];
		$it = $it.",".$resultado['it'];
		$e = $e.",".$resultado['e'];
		$m = $m.",".$resultado['m'];
		$ap = $ap.",".$resultado['ap'];
		$t = $t.",".$resultado['t'];
		$i++;
	}
	$data = $data."'";

	echo "<script>" . "\n";
	echo "var dias = new Array(".substr($data,2).");";
	echo "var adm = new Array(".substr($adm,1).");";
	echo "var op = new Array(".substr($op,1).");";
	echo "var it = new Array(".substr($it,1).");";
	echo "var e = new Array(".substr($e,1).");";
	echo "var m = new Array(".substr($m,1).");";
	echo "var ap = new Array(".substr($ap,1).");";
	echo "var t = new Array(".substr($t,1).");";
	echo "</script>"  . "\n";
?>

	<script>
		var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
		var lineChartData = {
			labels : dias,
			datasets : [
				{
					label: "Administrativo",
					fillColor : "rgba(250,250,250,0.2)",
					strokeColor : "rgba(250,250,250,1)",
					pointColor : "rgba(250,250,250,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(250,250,250,1)",
					data : adm
				},
				{
					label: "Operacional",
					fillColor : "rgba(255,51,51,0.2)",
					strokeColor : "rgba(255,51,51,1)",
					pointColor : "rgba(255,51,51,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(255,51,51,1)",
					data : op
				},
				{
					label: "Inteligência",
					fillColor : "rgba(102,102,255,0.2)",
					strokeColor : "rgba(102,102,255,1)",
					pointColor : "rgba(102,102,255,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(102,102,255,1)",
					data : it
				},
				{
					label: "Ensino",
					fillColor : "rgba(178,178,178,0.2)",
					strokeColor : "rgba(178,178,178,1)",
					pointColor : "rgba(178,178,178,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(151,187,205,1)",
					data : e
				},
				{
					label: "Manutenção",
					fillColor : "rgba(119,41,83,0.2)",
					strokeColor : "rgba(119,41,83,1)",
					pointColor : "rgba(119,41,83,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(119,41,83,1)",
					data : m
				},
				{
					label: "Apoio",
					fillColor : "rgba(255,255,153,0.2)",
					strokeColor : "rgba(255,255,153,1)",
					pointColor : "rgba(255,255,153,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(255,255,153,1)",
					data : ap
				},
				{
					label: "Em Trânsito",
					fillColor : "rgba(0,0,0,0.2)",
					strokeColor : "rgba(0,0,0,1)",
					pointColor : "rgba(0,0,0,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(0,0,0,1)",
					data : t
				}
			]

		}

	window.onload = function(){
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true, multiTooltipTemplate: "<%= value%> Lt", legendTemplate : '<ul>'
                  +'<% for (var i=0; i<datasets.length; i++) { %>'
                    +'<li style=\"color:<%=datasets[i].strokeColor%>;\">'
                    +'<span></span>'
                    +'<% if (datasets[i].label) { %><%= datasets[i].label %><% } %>'
                  +'</li>'
                +'<% } %>'
              +'</ul>'
		});
		var legend = myLine.generateLegend();
		document.getElementById("legenda").innerHTML = legend;
	}
	</script>



	</body>
</html>