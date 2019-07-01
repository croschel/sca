<?php
ob_start();
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
  include "conexao.php";
?>

<script language="JavaScript" type="text/javascript">
 window.print();
</script>

<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<style type="text/css">
	t1 {font-weight: bold; font-size:28; font-family:verdana, sans-serif,helvetica;}
	t2 {font-style: italic; font-size:16; font-family:verdana, sans-serif,helvetica;}
	t3 {font-style: italic; font-size:14; font-family:verdana, sans-serif,helvetica;}
	coluna {font-style: italic; font-weight: bold; font-size:16; font-family:verdana, sans-serif,helvetica; color: #000000;}
	lin {font-size:14; font-family: arial, helvetica, serif; color: #000000;}
</style>

<table border="0" width="650" align="center" valign="top">
	<tr align="center">
	<?

		if ($_GET['dt_ini'] == ''){
			$data_ini = date(Y)."-".date(m)."-".date(d);
		} else {
		$data_ini = $_GET['dt_ini'];
		}
		$data_fim = date(Y)."-".date(m)."-".date(d)." 23:59";
		echo "<td><b><br>MAPA DE COMBUSTÍVEIS <p><i>Período: </b>".substr($data_ini,8,2)."/".substr($data_ini,5,2)."/".substr($data_ini,0,4)."  ".substr($data_fim,8,2)."/".substr($data_fim,5,2)."/".substr($data_fim,0,4)."</td>";
	?>
	</tr>
</form>

</table>

<table border="1" width="650" valign="top" CELLPADDING=0 CELLSPACING=0>
	<tr><td COLSPAN=8><b> <font color='blue'><i> -:- Situação de Gasolina -:-</td></tr>
	<tr align='center'><td ><b>OM</td><td ><coluna>Saldo<br>Ant</td><td ><coluna>Entrada</td><td ><coluna>Consumo</td><td ><coluna>Evapor</td><td ><coluna>Sangria</td><td ><coluna>Saldo<br>Atual</td></tr>
	<?php    
   //Conexão ao Banco de Dados
   include "conexao.php";
	if($_SESSION['perfil']=='FISCAL DA OM') {	
		$query=("SELECT distinct o.codom, o.sigla FROM om o, disponibilidade d
					where o.codom=d.om_codom 
					and o.tipo<>'Não Integrante' and d.reservatorio_codigo=(select codigo from reservatorio where om_codom='".$_SESSION['codom']."' and combustivel_codigo='1') 
					ORDER BY sigla");		
	} elseif (($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='ADMINISTRADOR')) {
		$query=("SELECT distinct o.codom, o.sigla FROM om o, disponibilidade d
					where o.codom=d.om_codom 
					and o.tipo<>'Não Integrante' and (o.oc='".$_SESSION['codom']."' or o.codom='".$_SESSION['codom']."') ORDER BY sigla");		
	} 
	$resultado = mysql_query("$query");
	while ($om = mysql_fetch_array($resultado, MYSQL_NUM)) {
		echo "<tr align='left'><td><b><font size=2> $om[1] </td>";
				//busca a quantidade total de gasolina recebida
					$query = ("SELECT round(sum(c.qtd_destinada),0)
						FROM recebimento r, cotas c
						WHERE r.nr_nota_fiscal=c.recebimento_nr_nota_fiscal
						and r.combustivel_codigo=c.recebimento_combustivel_codigo
						and r.dt_rec between '".$data_ini."' and '".$data_fim."'
						and c.recebimento_combustivel_codigo='1'
						and c.om_codom='".$om[0]."'");
//echo $query;
				$rec = mysql_query($query,$conexao);
				$total_rec = mysql_fetch_row($rec);
				//Busca a quantidade gasolina abastecida
				$data_final = $data_fim;//.' 23:59:00'
					$query = ("SELECT sum( a.qtd_abastecida )
						FROM reservatorio r, disponibilidade d, abastecimento a
						WHERE r.codigo = d.reservatorio_codigo
						AND d.om_codom = a.disponibilidade_om_codom
						AND d.reservatorio_codigo = a.disponibilidade_reservatorio_codigo
						and a.dt_abastecimento between '".$data_ini."' and '".$data_final."'
						AND r.combustivel_codigo = '1'
						AND d.om_codom = '".$om[0]."'");
//echo $query;
				$res1 = mysql_query($query,$conexao);
				$consumo = mysql_fetch_row($res1);
				if ($consumo[0]=='') {
					$consumo[0] = 0;
				}
				//Busca a quantidade gasolina evaporada
					$query = ("SELECT e.disponibilidade_om_codom, round(sum(e.qtd_evaporada),0)
						FROM reservatorio r, disponibilidade d, evaporacao e
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom=e.disponibilidade_om_codom
						and d.reservatorio_codigo=e.disponibilidade_reservatorio_codigo
						and e.data_registro between '".$data_ini."' and '".$data_fim."'
						and r.combustivel_codigo='1'
						and e.disponibilidade_om_codom='".$om[0]."'
						group by e.disponibilidade_om_codom");
				$res2 = mysql_query($query,$conexao);
				$evaporado = mysql_fetch_row($res2);
				$evaporacao = str_replace(".", ",",$evaporado[1]);
				if ($evaporacao=='') {
					$evaporacao = 0;
				}
				//Busca a quantidade gasolina retirada por sangria
					$query = ("SELECT s.disponibilidade_om_codom, round(sum(s.qtd_retirada),0)
						FROM reservatorio r, disponibilidade d, sangria s
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom=s.disponibilidade_om_codom
						and d.reservatorio_codigo=s.disponibilidade_reservatorio_codigo
						and r.combustivel_codigo='1'
						and s.data_sangria between '".$data_ini."' and '".$data_fim."'
						and s.disponibilidade_om_codom='".$om[0]."'
						group by disponibilidade_om_codom");
				$res3 = mysql_query($query,$conexao);
				$sangria = mysql_fetch_row($res3);
				$qtd_sangria = str_replace(".", ",",$sangria[1]);
				if ($qtd_sangria=='') {
					$qtd_sangria = 0;
				}
				//Busca a quantidade atual gasolina
					$query = ("SELECT d.om_codom, round(sum(d.qtd_atual),0)
						FROM reservatorio r, disponibilidade d
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom='".$om[0]."'
						and r.combustivel_codigo='1'
						group by om_codom");
				$res4 = mysql_query($query,$conexao);
				$atual = mysql_fetch_row($res4);
				if ($atual[1] < 0) {
					$qtd_ant = 0;
				} else {
					$qtd_ant = ($atual[1] + $sangria[1] + $evaporado[1] + $consumo[0] - $total_rec[0]);
				}
//				echo $qtd_ant." ";
				//Busca a quantidade atual gasolina
				$qtd_atual = str_replace(".", ",",$atual[1]);
				if ($qtd_atual=='') {
					$qtd_atual = 0;
				}
		echo "<td align='right'><font size=2> $qtd_ant &nbsp;</td>";
		if ($total_rec[0]==''){
			$total_rec[0]=0;
		}
		echo "<td align='right'><font size=2> $total_rec[0] &nbsp;</td>";
		echo "<td align='right'><font size=2> $consumo[0] &nbsp;</td><td align='right'><font size=2> $evaporacao &nbsp;</td><td align='right'><font size=2> $qtd_sangria &nbsp;</td><td align='right'><font size=2> $qtd_atual &nbsp;</td></tr>";

   	}

	?> 

</table><p>

<table border="1" width="650" valign="top" CELLPADDING=0 CELLSPACING=0>
	<tr ><td COLSPAN=8><b> <font color='blue'><i> -:- Situação de Diesel -:-</td></tr>
	<tr align='center' ><td ><coluna>OM</td><td ><coluna>Saldo<br>Ant</td><td ><coluna>Entrada</td><td ><coluna>Consumo</td><td ><coluna>Evapor</td><td ><coluna>Sangria</td><td ><coluna>Saldo<br>Atual</td></tr>
	<?php    
        //Conexão ao Banco de Dados
    	include "conexao.php";
	if($_SESSION['perfil']=='FISCAL DA OM') {	
		$query=("SELECT distinct o.codom, o.sigla FROM om o, disponibilidade d
					where o.codom=d.om_codom 
					and o.tipo<>'Não Integrante' and d.reservatorio_codigo=(select codigo from reservatorio where om_codom='".$_SESSION['codom']."' and combustivel_codigo='2') ORDER BY sigla");		
	} elseif (($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='ADMINISTRADOR')){
		$query=("SELECT distinct o.codom, o.sigla FROM om o, disponibilidade d
					where o.codom=d.om_codom 
					and o.tipo<>'Não Integrante' and (o.oc='".$_SESSION['codom']."' or o.codom='".$_SESSION['codom']."') ORDER BY sigla");		
	}
				
	$resultado = mysql_query($query);

	while ($om = mysql_fetch_array($resultado, MYSQL_NUM)) {
		echo "<tr align='left'><td><b><font size=2> $om[1] </td>";
				//busca a quantidade total de diesel recebida
					$query = ("SELECT round(sum(c.qtd_destinada),0)
						FROM recebimento r, cotas c
						WHERE r.nr_nota_fiscal=c.recebimento_nr_nota_fiscal
						and r.combustivel_codigo=c.recebimento_combustivel_codigo
						and r.dt_rec between '".$data_ini."' and '".$data_fim."'
						and c.recebimento_combustivel_codigo='2'
						and c.om_codom='".$om[0]."'");
				$rec = mysql_query($query,$conexao);
				$total_rec = mysql_fetch_row($rec);
				//Busca a quantidade diesel abastecida
					$query = ("SELECT sum( a.qtd_abastecida )
						FROM reservatorio r, disponibilidade d, abastecimento a
						WHERE r.codigo = d.reservatorio_codigo
						AND d.om_codom = a.disponibilidade_om_codom
						AND d.reservatorio_codigo = a.disponibilidade_reservatorio_codigo
						and a.dt_abastecimento between '".$data_ini."' and '".$data_final."'
						AND r.combustivel_codigo = '2'
						AND d.om_codom = '".$om[0]."'");
				$res1 = mysql_query($query,$conexao);
				$consumo = mysql_fetch_row($res1);
				if ($consumo[0]=='') {
					$consumo[0] = 0;
				}
				//Busca a quantidade diesel evaporado
					$query = ("SELECT e.disponibilidade_om_codom, round(sum(e.qtd_evaporada),0)
						FROM reservatorio r, disponibilidade d, evaporacao e
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom=e.disponibilidade_om_codom
						and d.reservatorio_codigo=e.disponibilidade_reservatorio_codigo
						and e.data_registro between '".$data_ini."' and '".$data_fim."'
						and r.combustivel_codigo='2'
						and e.disponibilidade_om_codom='".$om[0]."'
						group by e.disponibilidade_om_codom");
				$res2 = mysql_query($query,$conexao);
				$evaporado = mysql_fetch_row($res2);
				$evaporacao = str_replace(".", ",",$evaporado[1]);
				if ($evaporacao=='') {
					$evaporacao = 0;
				}
				//Busca a quantidade diesel retirado por sangria
					$query = ("SELECT s.disponibilidade_om_codom, round(sum(s.qtd_retirada),0)
						FROM reservatorio r, disponibilidade d, sangria s
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom=s.disponibilidade_om_codom
						and d.reservatorio_codigo=s.disponibilidade_reservatorio_codigo
						and r.combustivel_codigo='2'
						and s.data_sangria between '".$data_ini."' and '".$data_fim."'
						and s.disponibilidade_om_codom='".$om[0]."'
						group by disponibilidade_om_codom");
				$res3 = mysql_query($query,$conexao);
				$sangria = mysql_fetch_row($res3);
				$qtd_sangria = str_replace(".", ",",$sangria[1]);
				if ($qtd_sangria=='') {
					$qtd_sangria = 0;
				}
				//Busca a quantidade atual de diesel
					$query = ("SELECT d.om_codom, round(sum(d.qtd_atual),0)
						FROM reservatorio r, disponibilidade d
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom='".$om[0]."'
						and r.combustivel_codigo='2'
						group by om_codom");
				$res4 = mysql_query($query,$conexao);
				$atual = mysql_fetch_row($res4);
				if ($atual[1] < 0) {
					$qtd_ant = 0;
				} else {
					$qtd_ant = ($atual[1] + $sangria[1] + $evaporado[1] + $consumo[0] - $total_rec[0]);
				}
//				echo $qtd_ant." ";
				//Busca a quantidade atual de diesel
				$qtd_atual = str_replace(".", ",",$atual[1]);
				if ($qtd_atual=='') {
					$qtd_atual = 0;
				}
			echo "<td align='right'><font size=2> $qtd_ant &nbsp;</td>";
			if ($total_rec[0]=='') {
					$total_rec[0] = 0;
				}
			echo "<td align='right'><font size=2> $total_rec[0] &nbsp;</td>";
		echo "<td align='right'><font size=2> $consumo[0] &nbsp;</td><td align='right'><font size=2> $evaporacao &nbsp;</td><td align='right'><font size=2> $qtd_sangria &nbsp;</td><td align='right'><font size=2> $qtd_atual &nbsp;</td></tr>";
   	}
	?>

</table><p>


<table border="1" width="650" valign="top" CELLPADDING=0 CELLSPACING=0>
	<tr ><td COLSPAN=8><b> <font color='blue'><i> -:- Situação dos Reservatórios -:-</td></tr>
	<tr align='center'><td><coluna>Reservatório</td><td ><coluna>Combustível</td><td ><coluna>Capacidade</td><td ><coluna>Qtd Atual</td><td ><coluna>Livre</td></tr>
	
	<?php    
        //Conexão ao Banco de Dados
    	include "conexao.php";
$capac_total = 0;
$atual_total = 0;
$nd_total = 0;
$res_de_total = 0;
$res_bda_total = 0;
$livre_total = 0;
//Seleciona a relação de reservatorios de gasolina
	if  (($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='ADMINISTRADOR')) {
		$resultado = mysql_query("SELECT r.codigo, o.sigla, c.nome, r.capacidade
				FROM om o, combustivel c, reservatorio r 
				where o.codom=r.om_codom
				and c.codigo=r.combustivel_codigo
				and r.combustivel_codigo='1'
				and (o.oc='".$_SESSION['codom']."' or r.codigo in (select distinct d.reservatorio_codigo from om o, reservatorio r, disponibilidade d where o.codom=d.om_codom and r.codigo=d.reservatorio_codigo and r.combustivel_codigo='1' and o.oc='".$_SESSION['codom']."'))
				ORDER BY c.nome");
		} elseif ($_SESSION['perfil']=='FISCAL DA OM') {
		$resultado = mysql_query("SELECT r.codigo, o.sigla, c.nome, r.capacidade
				FROM om o, combustivel c, reservatorio r 
				where o.codom=r.om_codom
				and c.codigo=r.combustivel_codigo
				and r.combustivel_codigo='1'
				and r.om_codom='".$_SESSION['posto']."'
				ORDER BY c.nome");
		}
	while ($reservatorio = mysql_fetch_array($resultado, MYSQL_NUM)) {
		echo "<tr align='left'><td><font size=2> Nº $reservatorio[0] - $reservatorio[1] </td><td><font size=2> $reservatorio[2] </td><td align='right'><font size=2> $reservatorio[3]  </td>";
			echo "<td align='right'><font size=2>";
				$query = ("SELECT sum(round(qtd_atual,0))
					FROM disponibilidade
					WHERE reservatorio_codigo='".$reservatorio[0]."'
					");
				$res6 = mysql_query($query,$conexao);
				$atual = mysql_fetch_row($res6);
				$qtd_atual = str_replace(".", ",",$atual[0]);
				if ($qtd_atual!='') {
					echo $qtd_atual." ";
				} else {
					echo "0 ";
				}
			echo "</td>";



/*			echo "<td align='right'><font size=2>";
				$query = ("SELECT round(sum(qtd_atual),0) 
					FROM disponibilidade
					WHERE reservatorio_codigo='".$reservatorio[0]."'
					and om_codom ='023804'");
				$res9 = mysql_query($query,$conexao);
				$qtd_de = mysql_fetch_row($res9);

				$qtd_de = str_replace(".", ",",$qtd_de[0]);
				if ($qtd_de!='') {
					echo $qtd_de." ";
				} else {
					echo "0 ";
				}
			echo "</td>";

			echo "<td align='right'><font size=2>";
				$query = ("SELECT round(sum(qtd_atual),0) 
					FROM disponibilidade
					WHERE reservatorio_codigo='".$reservatorio[0]."'
					and om_codom ='024208'");
				$res10 = mysql_query($query,$conexao);
				$qtd_bda = mysql_fetch_row($res10);

				$qtd_bda = str_replace(".", ",",$qtd_bda[0]);
				if ($qtd_bda!='') {
					echo $qtd_bda." ";
				} else {
					echo "0 ";
				}
			echo "</td>";*/

			echo "<td align='right'><font size=2>";
				$livre = $reservatorio[3] - $qtd_atual - $qtd_nao_dist;
				$qtd_livre = str_replace(".", ",",$livre);
				if ($qtd_livre!='') {
					echo $qtd_livre." ";
				} else {
					echo "0 ";
				}
			echo "</td>";
		echo "</tr>";
		$capac_total = $capac_total + $reservatorio[3];
		$atual_total = $atual_total + $qtd_atual;
		$nd_total = $nd_total + $qtd_nao_dist;		
		$res_de_total = $res_de_total + $qtd_de;		
		$res_bda_total = $res_bda_total + $qtd_bda;		
		$livre_total = $livre_total + $qtd_livre;		


    	}
		echo "<tr align='left'><td colspan='2' align='right'><font size=2><b><i> Somatório de Gasolina&nbsp;&nbsp;</td><td align='right'><font size=2><b><i> $capac_total </td><td align='right'><font size=2><b><i> $atual_total  </td><td align='right'><font size=2><b><i> $livre_total  </td>";

$capac_total = 0;
$atual_total = 0;
$nd_total = 0;
$res_de_total = 0;
$res_bda_total = 0;
$livre_total = 0;

    	//Seleciona a relação de reservatorios de diesel
	if  (($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='ADMINISTRADOR')) {
		$resultado1 = mysql_query("SELECT r.codigo, o.sigla, c.nome, r.capacidade
				FROM om o, combustivel c, reservatorio r 
				where o.codom=r.om_codom
				and c.codigo=r.combustivel_codigo
				and r.combustivel_codigo='2'
				and (o.oc='".$_SESSION['codom']."' or r.codigo in (select distinct d.reservatorio_codigo from om o, reservatorio r, disponibilidade d where o.codom=d.om_codom and r.codigo=d.reservatorio_codigo and r.combustivel_codigo='2' and o.oc='".$_SESSION['codom']."'))
				ORDER BY c.nome");
			} elseif ($_SESSION['perfil']=='FISCAL DA OM') {
		$resultado1 = mysql_query("SELECT r.codigo, o.sigla, c.nome, r.capacidade
				FROM om o, combustivel c, reservatorio r 
				where o.codom=r.om_codom
				and c.codigo=r.combustivel_codigo
				and r.combustivel_codigo='2'
				and r.om_codom='".$_SESSION['posto']."'
				ORDER BY c.nome");
			}
	while ($reservatorio = mysql_fetch_array($resultado1, MYSQL_NUM)) {
		echo "<tr align='left'><td><font size=2> Nº $reservatorio[0] - $reservatorio[1] </td><td><font size=2> $reservatorio[2] </td><td align='right'><font size=2> $reservatorio[3]  </td>";
			echo "<td align='right'><font size=2>";
				$query = ("SELECT sum(round(qtd_atual,0))
					FROM disponibilidade
					WHERE reservatorio_codigo='".$reservatorio[0]."'
					");
				$res6 = mysql_query($query,$conexao);
				$atual = mysql_fetch_row($res6);
				$qtd_atual = str_replace(".", ",",$atual[0]);
				if ($qtd_atual!='') {
					echo $qtd_atual." ";
				} else {
					echo "0 ";
				}
			echo "</td>";


/*			echo "<td align='right'><font size=2>";
				$query = ("SELECT round(sum(qtd_atual),0) 
					FROM disponibilidade
					WHERE reservatorio_codigo='".$reservatorio[0]."'
					and om_codom ='023804'");
				$res9 = mysql_query($query,$conexao);
				$qtd_de = mysql_fetch_row($res9);

				$qtd_de = str_replace(".", ",",$qtd_de[0]);
				if ($qtd_de!='') {
					echo $qtd_de." ";
				} else {
					echo "0 ";
				}
			echo "</td>";

			echo "<td align='right'><font size=2>";
				$query = ("SELECT round(sum(qtd_atual),0) 
					FROM disponibilidade
					WHERE reservatorio_codigo='".$reservatorio[0]."'
					and om_codom ='024208'");
				$res10 = mysql_query($query,$conexao);
				$qtd_bda = mysql_fetch_row($res10);

				$qtd_bda = str_replace(".", ",",$qtd_bda[0]);
				if ($qtd_bda!='') {
					echo $qtd_bda." ";
				} else {
					echo "0 ";
				}
			echo "</td>";*/

			echo "<td align='right'><font size=2>";
				$livre = $reservatorio[3] - $qtd_atual - $qtd_nao_dist;
				$qtd_livre = str_replace(".", ",",$livre);
				if ($qtd_livre!='') {
					echo $qtd_livre." ";
				} else {
					echo "0 ";
				}
			echo "</td>";
		echo "</tr>";
		$capac_total = $capac_total + $reservatorio[3];
		$atual_total = $atual_total + $qtd_atual;
		$nd_total = $nd_total + $qtd_nao_dist;		
		$res_de_total = $res_de_total + $qtd_de;		
		$res_bda_total = $res_bda_total + $qtd_bda;		
		$livre_total = $livre_total + $qtd_livre;		
    	}
		echo "<tr align='left'><td colspan='2' align='right'><font size=2><b><i> Somatório de Diesel&nbsp;&nbsp;</td><td align='right'><font size=2><b><i> $capac_total </td><td align='right'><font size=2><b><i> $atual_total  </td><td align='right'><font size=2><b><i> $livre_total  </td>";


	?>

</table><p>

</html>

