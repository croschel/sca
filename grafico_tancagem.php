
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
function getRes(what) {
      var codom = what.value;
      document.location=('index.php?nomeArquivo=grafico_tancagem.php&codom=' + codom);
}
</Script>
	
	
<?php
	if(empty($_SESSION['usuario']))
		include "verifica_sessao.php";
	echo "<center><br><t1>Histórico de Armazenamento</t1> <p> </center>";

	/*/Relação de OM*/
	if(($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
		$qryoc = "select distinct r.om_codom, om.sigla, om.subordinacao from reservatorio r, om where om.codom=r.om_codom order by 3, 2";
	} elseif(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$qryoc = "select distinct r.om_codom, om.sigla, om.subordinacao from reservatorio r, om where om.codom=r.om_codom and om.subordinacao='".$_SESSION['codom']."' order by 2";
	} elseif(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) {
		$qryoc = "select distinct r.om_codom, om.sigla, om.subordinacao from reservatorio r, om where om.codom=r.om_codom and om.codom='".$_SESSION['codom']."' order by 2";
		$_GET['codom']=$_SESSION['codom'];
	} elseif($_SESSION['perfil']=='ABASTECEDOR') {
		$qryoc = "select distinct r.om_codom, om.sigla, om.subordinacao from reservatorio r, om where om.codom=r.om_codom and om.codom='".$_SESSION['codom']."' order by 2";
	}
	//echo $qryoc;
	$result = mysql_query($qryoc);
	echo "OM: <select name='codom' onChange='getRes(this);'>";	
				echo "<option value=''>:-:-:-:  Escolha a OM  :-:-:-:</option>";
				while ($res = mysql_fetch_array($result, MYSQL_NUM)) {
					if($cmdo!=$res[2]) {
						$qrycmdo="select sigla from om where codom='$res[2]'";
						$rescmdo = mysql_query($qrycmdo);
						$cmdo = mysql_fetch_row($rescmdo);
  						echo "<optgroup label='Comando: $cmdo[0]'>";
  						$cmdo=$res[2];
  					}
					echo "<option value='$res[0]'"; if ($res[0]==$_GET['codom']) {	echo "selected";}	echo ">$res[1]</option>";
					if($_GET['codom']=='') {$_GET['codom']=$res[0];}
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
	echo "<a href='index.php?nomeArquivo=grafico_tancagem.php&codom=".$_GET['codom']."&ano=".$_GET['ano']."&mes=".($_GET['mes']-1)."' style='text-decoration:none' title='Mês Anterior - $mes_a'>&nbsp;Anterior&nbsp;</a>&nbsp;
			<font size='4' color='blue'><b>- $mes / ".$_GET[ano]."</font></b> - 
			<a href='index.php?nomeArquivo=grafico_tancagem.php&codom=".$_GET['codom']."&ano=".$_GET['ano']."&mes=".($_GET['mes']+1)."' style='text-decoration:none' title='Próximo Mês - $p_mes'>&nbsp; Próximo&nbsp;</a>";	   


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
	$qry = "SELECT substr(h.dt,1,10) dt, round(ifnull(SUM( ad.qtd_atual ),0),0) qtd, round(ifnull(SUM( d.qtd_atual ),0),0) die
					FROM (SELECT DATE_FORMAT( ADDDATE( '".$_GET['ano']."-".$_GET['mes']."-01' , @num := @num +1 ) ,  '%Y-%m-%d' ) dt 
							FROM auditoria, (SELECT @num :=-1)num
							LIMIT $dias ) h left join
							(SELECT h.data dt, sum(h.qtd_atual) qtd_atual
							FROM estoque e, historico_estoque h
							WHERE e.nr_repasse=h.nr_repasse and e.om_codom=h.om_codom and e.reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='1' and om_codom='".$_GET['codom']."') 
								and h.data between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-".$dias."'
								
							GROUP BY h.data order by 1) ad on (h.dt = ad.dt) 
							left join
							(SELECT h.data dt, sum(h.qtd_atual) qtd_atual
							FROM estoque e, historico_estoque h
							WHERE e.nr_repasse=h.nr_repasse and e.om_codom=h.om_codom and e.reservatorio_codigo in (select codigo from reservatorio where combustivel_codigo='2' and om_codom='".$_GET['codom']."')
								and h.data between '".$_GET['ano']."-".$_GET['mes']."-01' and '".$_GET['ano']."-".$_GET['mes']."-".$dias."'
							  
							GROUP BY h.data order by 1) d on (h.dt = d.dt)
					GROUP BY substr(h.dt,1,10)";
	$consultar = mysql_query($qry);
 
	$i = 1;
	while($resultado = mysql_fetch_array($consultar)){
		$data = $data."','".substr($resultado['dt'],8,2)."/".substr($resultado['dt'],5,2)."/".substr($resultado['dt'],0,4);
		$qtd = $qtd.",".$resultado['qtd'];
		$die = $die.",".$resultado['die'];
		$i++;
	}
	$data = $data."'";

	echo "<script>" . "\n";
	echo "var dias = new Array(".substr($data,2).");";
	echo "var qtd = new Array(".substr($qtd,1).");";
	echo "var die = new Array(".substr($die,1).");";
	echo "</script>"  . "\n";
?>

	<script>
		var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
		var lineChartData = {
			labels : dias,
			datasets : [
 	 			{
					label: "Diesel",
					fillColor : "rgba(255,51,51,0.2)",
					strokeColor : "rgba(255,51,51,1)",
					pointColor : "rgba(255,51,51,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(255,51,51,1)",
					data : die
				},
				{
					label: "Gasolina",
					fillColor : "rgba(102,102,255,0.2)",
					strokeColor : "rgba(102,102,255,1)",
					pointColor : "rgba(102,102,255,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(102,102,255,1)",
					data : qtd
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