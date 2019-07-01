
<html>
	<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>Line Chart</title>
		<script src="./Chart.js-master/Chart.js"></script>
		<style type="text/css">
			li{}
		</style>
	</head>
	<body>
	
<Script Language="JavaScript">
function getOM(what) {
      var cod_om = what.value;
      document.location=('index.php?nomeArquivo=grafico_repasse.php&codom=' + cod_om);
}
</Script>

<?php
	echo "<center><br><t1>Repasse de Combustíveis</t1> <p> </center>";

	/*/Relação de OM*/
	if(($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
		$qryoc = "select codom, sigla, subordinacao from om where tipo in ('OC','Integrante') order by oc, sigla";
	} elseif(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$qryoc = "select codom, sigla, subordinacao from om where subordinacao='".$_SESSION['codom']."'";
	} elseif(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
		$qryoc = "select codom, sigla, subordinacao from om where codom='".$_SESSION['codom']."'";
		$_GET['codom']=$_SESSION['codom'];
	} elseif(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) {
		$qryoc = "select codom, sigla, subordinacao from om where codom in (select om_codom from estoque where reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')) or codom='".$_SESSION['codom']."'";
	} elseif($_SESSION['perfil']=='ABASTECEDOR') {
		$qryoc = "select codom, sigla, subordinacao from om where codom in (select om_codom from estoque where reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."'))";
	}
	$result = mysql_query($qryoc);
	echo "OM: <select name='codom' onChange='getOM(this);'>";	
				echo "<option value=''>:-:-:-:  Escolha a OM  :-:-:-:</option>";
				while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
					if($cmdo!=$om[2]) {
						$qrycmdo="select sigla from om where codom='$om[2]'";
						$rescmdo = mysql_query($qrycmdo);
						$cmdo = mysql_fetch_row($rescmdo);
  						echo "<optgroup label='Comando: $cmdo[0]'>";
  						$cmdo=$om[2];
  					}
					echo "<option value='$om[0]'"; if ($om[0]==$_GET['codom']){	echo "selected";}	echo ">$om[1]</option>";
					if($_GET['codom']=='') {
						$_GET['codom']=$om[0];
					}
				}
				echo "</select><p>";

	if($_GET['ano']=='') { $_GET['ano']=date('Y');}
	$anterior = ($_GET['ano'] - 1);
	$proximo = ($_GET['ano'] + 1);
	echo "<a href='index.php?nomeArquivo=grafico_repasse.php&codom=".$_GET['codom']."&ano=$anterior' style='text-decoration:none' title='Ano Anterior - $anterior'>&nbsp;Anterior&nbsp;</a>&nbsp;
			<font size='4' color='blue'><b>- ".$_GET[ano]."</font> -</b> 
			<a href='index.php?nomeArquivo=grafico_repasse.php&codom=".$_GET['codom']."&ano=$proximo' style='text-decoration:none' title='Próximo Ano - $proximo'>&nbsp; Próximo&nbsp;</a>";	   
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
	$qry = "SELECT substr(r.dt,1,7) dt, ifnull(SUM( ad.qtd_destinada ),0) adm, ifnull(SUM( op.qtd_destinada ),0) op, ifnull(SUM( it.qtd_destinada ),0) it, ifnull(SUM( e.qtd_destinada ),0) e,
							 ifnull(SUM( m.qtd_destinada ),0) m, ifnull(SUM( ap.qtd_destinada ),0) ap, ifnull(SUM( t.qtd_destinada ),0) t
					FROM (SELECT DATE_FORMAT( ADDDATE( '".$_GET['ano']."-01-01', @num := @num + 28) ,  '%m' ) dt 
							FROM auditoria, (SELECT @num :=-1)num
							LIMIT 12
							) r left join
							(SELECT DATE_FORMAT( dt_repasse,  '%m' ) dt, qtd_destinada
							FROM estoque
							WHERE dt_repasse between '".$_GET['ano']."-01-01' and '".$_GET['ano']."-12-31' and cod_tp_cota in('0','1') and om_codom='".$_GET['codom']."' GROUP BY DATE_FORMAT( dt_repasse,  '%m' )) ad on (r.dt = ad.dt) left join
							(SELECT DATE_FORMAT( dt_repasse,  '%m' ) dt, qtd_destinada
							FROM estoque
							WHERE dt_repasse between '".$_GET['ano']."-01-01' and '".$_GET['ano']."-12-31' and cod_tp_cota='2' and om_codom='".$_GET['codom']."' GROUP BY DATE_FORMAT( dt_repasse,  '%m' )) op on (r.dt = op.dt) left join
							(SELECT DATE_FORMAT( dt_repasse,  '%m' ) dt, qtd_destinada
							FROM estoque
							WHERE dt_repasse between '".$_GET['ano']."-01-01' and '".$_GET['ano']."-12-31' and cod_tp_cota='3' and om_codom='".$_GET['codom']."' GROUP BY DATE_FORMAT( dt_repasse,  '%m' )) it on (r.dt = it.dt) left join
							(SELECT DATE_FORMAT( dt_repasse,  '%m' ) dt, qtd_destinada
							FROM estoque
							WHERE dt_repasse between '".$_GET['ano']."-01-01' and '".$_GET['ano']."-12-31' and cod_tp_cota='4' and om_codom='".$_GET['codom']."' GROUP BY DATE_FORMAT( dt_repasse,  '%m' )) e on (r.dt = e.dt) left join
							(SELECT DATE_FORMAT( dt_repasse,  '%m' ) dt, qtd_destinada
							FROM estoque
							WHERE dt_repasse between '".$_GET['ano']."-01-01' and '".$_GET['ano']."-12-31' and cod_tp_cota='5' and om_codom='".$_GET['codom']."' GROUP BY DATE_FORMAT( dt_repasse,  '%m' )) m on (r.dt = m.dt) left join
							(SELECT DATE_FORMAT( dt_repasse,  '%m' ) dt, qtd_destinada
							FROM estoque
							WHERE dt_repasse between '".$_GET['ano']."-01-01' and '".$_GET['ano']."-12-31' and cod_tp_cota='6' and om_codom='".$_GET['codom']."' GROUP BY DATE_FORMAT( dt_repasse,  '%m' )) ap on (r.dt = ap.dt) left join
							(SELECT DATE_FORMAT( dt_repasse,  '%m' ) dt, qtd_destinada
							FROM estoque
							WHERE dt_repasse between '".$_GET['ano']."-01-01' and '".$_GET['ano']."-12-31' and cod_tp_cota='7' and om_codom='".$_GET['codom']."' GROUP BY DATE_FORMAT( dt_repasse,  '%m' )) t on (r.dt = t.dt) 
					GROUP BY substr(r.dt,1,7)";
	$consultar = mysql_query($qry);
 
	$i = 1;
	while($resultado = mysql_fetch_array($consultar)){
		switch ($resultado['dt']) {
    		case "01": $resultado['dt']='Janeiro';    break;
    		case "02": $resultado['dt']='Fevereiro';    break;
    		case "03": $resultado['dt']='Março';    break;
    		case "04": $resultado['dt']='Abril';    break;
    		case "05": $resultado['dt']='Maio';    break;
    		case "06": $resultado['dt']='Junho';    break;
    		case "07": $resultado['dt']='Julho';    break;
    		case "08": $resultado['dt']='Agosto';    break;
    		case "09": $resultado['dt']='Setembro';    break;
    		case "10": $resultado['dt']='Outubro';    break;
    		case "11": $resultado['dt']='Novembro';    break;
    		case "12": $resultado['dt']='Dezembro';    break;
		}
		$data = $data."','".$resultado['dt'];
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