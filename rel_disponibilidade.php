<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
  include "conexao.php";

?>

<script language="JavaScript" type="text/javascript">
function mascara(dt_ini){
              var mdata = '';
              mdata = mdata + dt_ini;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[0].dt_ini.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[0].dt_ini.value = mdata;
              }
              if (mdata.length == 10){
                  return true;
              }
          }
function mascara1(dt_fim){
              var mdata = '';
              mdata = mdata + dt_fim;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[0].dt_fim.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[0].dt_fim.value = mdata;
              }
              if (mdata.length == 10){
                  return true;
              }
          }
</script>


<html>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<?php
	echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<tr align='center' bgcolor='#4A708B'>";

	if ($_POST['dt_ini'] == ''){
		$data_ini = date(Y)."-".date(m)."-".date(d);
	} else {
		$data_ini = substr($_POST['dt_ini'],6,4)."-".substr($_POST['dt_ini'],3,2)."-".substr($_POST['dt_ini'],0,2);
	}
	$data_fim = date(Y)."-".date(m)."-".date(d)." 23:59";
	echo "<td width='80%'><b><br>MAPA DE COMBUSTÍVEIS <p><i>Período: </b>".substr($data_ini,8,2)."/".substr($data_ini,5,2)."/".substr($data_ini,0,4)."  ".substr($data_fim,8,2)."/".substr($data_fim,5,2)."/".substr($data_fim,0,4)."</td>";

	echo "<form name='frm_autz' method='POST' action='index.php?nomeArquivo=rel_disponibilidade.php' >";
	echo "<td colspan='8' align='center'><I>Nova Pesquisa:<p><font size=2>A partir de: <input type='text' OnKeyUp='mascara(this.value);' size='10' maxlength='10' name='dt_ini'> <p><input type='submit' value= 'Pesquisar'></td>";
	echo "</tr>";
	echo "</form>";

	echo "</table>";


	echo "<table border='0' width='100%' align='center' valign='top' CELLPADDING=0 CELLSPACING=1>";
	echo "<tr bgcolor='#4A708B'><td COLSPAN=8><b> <font color='blue'><i> -:- Situação de Gasolina -:-</td></tr>";
	echo "<tr align='center' bgcolor='#4A708B'><td rowspan=2><b>OM</td><td rowspan=2><b>Saldo<br>Ant</td><td colspan=2><b>Entrada</td><td rowspan=2><b>Consumo</td><td rowspan=2><b>Evapor</td><td rowspan=2><b>Sangria</td><td rowspan=2><b>Saldo<br>Atual</td><td width='20%' bgcolor='#6CA6CD'><center>"; 
?>

<a href='#' onClick="window.open('impr_rel_disponibilidade.php?dt_ini=<?echo $data_ini?>','janela','toolbar=no,location=no,directories=no,status=eys,menubar=no,scrollbars=yes,resizable=yes,width=700,height=650');"><img border="0" src="./imagens/printer.gif" width="30" height="30"><br>Imprimir</a>

<?php
	echo "</td></tr>";
	echo "<tr align='center' bgcolor='#4A708B' ><td><b>Remessa</td><td><b>Qtd</td></tr>";
    
	//Conexão ao Banco de Dados
   include "conexao.php";
   //Seleciona a relação de om
	if($_SESSION['perfil']=='FISCAL DA OM') {	
		$query=("SELECT  distinct o.codom, o.sigla FROM om o, disponibilidade d
					where o.codom=d.om_codom 
					and o.tipo<>'Não Integrante' and d.reservatorio_codigo=(select codigo from reservatorio where om_codom='".$_SESSION['codom']."' and combustivel_codigo='1') 
					ORDER BY sigla");		
	} elseif (($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='ADMINISTRADOR')) {
		$query=("SELECT  distinct o.codom, o.sigla FROM om o, disponibilidade d
					where o.codom=d.om_codom 
					and o.tipo<>'Não Integrante' and (o.oc='".$_SESSION['codom']."' or o.codom='".$_SESSION['codom']."') ORDER BY sigla");		
	} 
//	echo $query;			
	$resultado = mysql_query($query);

	while ($om = mysql_fetch_array($resultado, MYSQL_NUM)) {
		echo "<tr bgcolor='#7EC0EE' align='left'><td><b><font size=2> $om[1] </td>";
				//busca a quantidade total de gasolina recebida
					$query = ("SELECT round(sum(c.qtd_destinada),0)
						FROM recebimento r, cotas c
						WHERE r.nr_nota_fiscal=c.recebimento_nr_nota_fiscal
						and r.combustivel_codigo=c.recebimento_combustivel_codigo
						and r.dt_rec between '".$data_ini."' and '".$data_fim."'
						and c.recebimento_combustivel_codigo='1'
						and c.om_codom='".$om[0]."'");
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
					$query = ("SELECT e.disponibilidade_om_codom, e.disponibilidade_reservatorio_codigo, round(sum(e.qtd_evaporada),0)
						FROM reservatorio r, disponibilidade d, evaporacao e
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom=e.disponibilidade_om_codom
						and d.reservatorio_codigo=e.disponibilidade_reservatorio_codigo
						and e.data_registro between '".$data_ini."' and '".$data_fim."'
						and r.combustivel_codigo='1'
						and e.disponibilidade_om_codom='".$om[0]."'
						group by e.disponibilidade_om_codom, e.disponibilidade_reservatorio_codigo");
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
					$query = "SELECT d.om_codom, round(sum(d.qtd_atual),0)
						FROM reservatorio r, disponibilidade d
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom='".$om[0]."'
						and r.combustivel_codigo='1' ";
						if ($_SESSION['perfil']=='FISCAL DA OM') {
							$query=$query."and r.codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."') ";
						$query=$query."group by om_codom, reservatorio_codigo";
						}
						$query=$query."group by om_codom";
//echo $query;
				$res4 = mysql_query($query,$conexao);
				$atual = mysql_fetch_row($res4);
				if ($atual[1] < 0) {
					$qtd_ant = 0;
				} else {
					$qtd_ant = ($atual[1] - ($total_rec[0] - $consumo[0] - $evaporado[1] - $sangria[1]));
				}
//				echo "(".$atual[1]." - (".$total_rec[0]." - ".$consumo[0]." - ".$evaporado[1]." - ".$sangria[1]."))";
				//Busca a quantidade atual gasolina
				$qtd_atual = str_replace(".", ",",$atual[1]);
				if ($qtd_atual=='') {
					$qtd_atual = 0;
				}
			echo "<td align='right'><font size=2> $qtd_ant &nbsp;</td>";
			echo "<td colspan=2>" ;
				//busca a quantidade gasolina recebida
//				$total_rec=0;
				echo "<table border='0' width='100%' align='center' valign='top'>";
					$res = mysql_query("SELECT c.qtd_destinada, c.nr_remessa
						FROM recebimento r, cotas c
						WHERE r.nr_nota_fiscal=c.recebimento_nr_nota_fiscal
						and r.combustivel_codigo=c.recebimento_combustivel_codigo
						and r.dt_rec between '".$data_ini."' and '".$data_fim."'
						and c.recebimento_combustivel_codigo='1'
						and c.om_codom='".$om[0]."'");
				while ($qtd = mysql_fetch_array($res, MYSQL_NUM)) {
					echo "<tr bgcolor='#7EC0EE'><td width='65%'><font size=2><i> Nº $qtd[1]</td><td align='right' width='35%'><font size=2><i> $qtd[0] &nbsp;</td></tr>";
				}
				if ($total_rec[0]=='') {
					$total_rec[0] = 0;
				}
			echo "<tr bgcolor='#7EC0EE'><td><font size=2><b> Total </td><td align='right'><font size=2><b> $total_rec[0] &nbsp;</td></tr></table>";
			echo "</td>";
			echo "<td align='right'><font size=2> $consumo[0] &nbsp;</td><td align='right'><font size=2> $evaporacao &nbsp;</td><td align='right'><font size=2> $qtd_sangria &nbsp;</td><td align='right'><font size=2> $qtd_atual &nbsp;</td></tr>";

   	}

	?> 

</table><p>

<table border="0" width="100%" align="center" valign="top">
	<tr bgcolor='#4A708B'><td COLSPAN=8><b> <font color='blue'><i> -:- Situação de Diesel -:-</td></tr>
	<tr align='center' bgcolor='#4A708B'><td rowspan=2><b>OM</td><td rowspan=2><b>Saldo<br>Ant</td><td colspan=2><b>Entrada</td><td rowspan=2><b>Consumo</td><td rowspan=2><b>Evapor</td><td rowspan=2><b>Sangria</td><td rowspan=2><b>Saldo<br>Atual</td><td width="20%" bgcolor="#6CA6CD">&nbsp;</td></tr>
	<tr align='center' bgcolor='#4A708B' ><td><b>Remessa</td><td><b>Qtd</td></tr>
	<?php    
   //Conexão ao Banco de Dados
   include "conexao.php";
   //Seleciona a relação de om
	if($_SESSION['perfil']=='FISCAL DA OM') {	
		$query=("SELECT  distinct o.codom, o.sigla FROM om o, disponibilidade d
					where o.codom=d.om_codom 
					and o.tipo<>'Não Integrante' and d.reservatorio_codigo=(select codigo from reservatorio where om_codom='".$_SESSION['codom']."' and combustivel_codigo='2') ORDER BY sigla");		
	} elseif (($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='ADMINISTRADOR')){
		$query=("SELECT  distinct o.codom, o.sigla FROM om o, disponibilidade d
					where o.codom=d.om_codom 
					and o.tipo<>'Não Integrante' and (o.oc='".$_SESSION['codom']."' or o.codom='".$_SESSION['codom']."') ORDER BY sigla");		
	}
//echo $query;				
	$resultado = mysql_query($query);

	while ($om = mysql_fetch_array($resultado, MYSQL_NUM)) {
		echo "<tr bgcolor='#7EC0EE' align='left'><td><b><font size=2> $om[1] </td>";
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
					$query = "SELECT d.om_codom, round(sum(d.qtd_atual),0)
						FROM reservatorio r, disponibilidade d
						WHERE r.codigo=d.reservatorio_codigo
						and d.om_codom='".$om[0]."'
						and r.combustivel_codigo='2' ";
						if ($_SESSION['perfil']=='FISCAL DA OM') {
							$query=$query."and r.codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."') ";
							$query=$query."group by om_codom, reservatorio_codigo";
						}
						$query=$query."group by om_codom";
				$res4 = mysql_query($query,$conexao);
				$atual = mysql_fetch_row($res4);
				if ($atual[1] < 0) {
					$qtd_ant = 0;
				} else {
					$qtd_ant = ($atual[1] + $sangria[1] + $evaporado[1] + $consumo[0] - $total_rec[0]);
				}
				//Busca a quantidade atual de diesel
				$qtd_atual = str_replace(".", ",",$atual[1]);
				if ($qtd_atual=='') {
					$qtd_atual = 0;
				}
			echo "<td align='right'><font size=2> $qtd_ant &nbsp;</td>";
			echo "<td colspan=2>" ;
				//busca a quantidade de diesel
				echo "<table border='0' width='100%' align='center' valign='top'>";
					$res = mysql_query("SELECT c.qtd_destinada, c.nr_remessa
						FROM recebimento r, cotas c
						WHERE r.nr_nota_fiscal=c.recebimento_nr_nota_fiscal
						and r.combustivel_codigo=c.recebimento_combustivel_codigo
						and r.dt_rec between '".$data_ini."' and '".$data_fim."'
						and c.recebimento_combustivel_codigo='2'
						and c.om_codom='".$om[0]."'");
				while ($qtd = mysql_fetch_array($res, MYSQL_NUM)) {
					echo "<tr bgcolor='#7EC0EE'><td width='65%'><font size=2><i> Nº $qtd[1]</td><td align='right' width='35%'><font size=2><i> $qtd[0] &nbsp;</td></tr>";
				}
				if ($total_rec[0]=='') {
					$total_rec[0] = 0;
				}
		echo "<tr bgcolor='#7EC0EE'><td><font size=2><b> Total </td><td align='right'><font size=2><b> $total_rec[0] &nbsp;</td></tr></table>";
			echo "</td>";
		echo "<td align='right'><font size=2> $consumo[0] &nbsp;</td><td align='right'><font size=2> $evaporacao &nbsp;</td><td align='right'><font size=2> $qtd_sangria &nbsp;</td><td align='right'><font size=2> $qtd_atual &nbsp;</td></tr>";
    	}
	?>

</table><p>


<table border="0" width="100%" align="center" valign="top">
	<tr bgcolor='#4A708B'><td COLSPAN=6><b> <font color='blue'><i> -:- Situação dos Reservatórios -:-</td></tr>
	<tr align='center' bgcolor='#4A708B'><td><b>Reservatório</td><td><b>Combustível</td><td><b>Capacidade</td><td><b>Quantidade<br>Atual</td><td><b>Tancagem<br>Livre</td></tr>
	<!--tr align='center' bgcolor='#4A708B' ><td><b>Atual</td><td><b>Não Dist</td><td width="20%" bgcolor="#6CA6CD">&nbsp;</td></tr-->
	<?php    
        //Conexão ao Banco de Dados
    	include "conexao.php";
$capac_total = 0;
$atual_total = 0;
$nd_total = 0;
$res_de_total = 0;
$res_bda_total = 0;
$livre_total = 0;
$gas_qtd_atual = array();
$gas_n_dist = array();
$gas_qtd_de = array();
$gas_qtd_bda = array();
$gas_qtd_livre = array();
//Seleciona a relação de reservatorios de gasolina
	if  (($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR') or ($_SESSION['perfil']=='ADMINISTRADOR')) {
		$qryres = "SELECT r.codigo, o.sigla, c.nome, r.capacidade
				FROM om o, combustivel c, reservatorio r 
				where o.codom=r.om_codom
				and c.codigo=r.combustivel_codigo
				and r.combustivel_codigo='1'
				and (o.oc='".$_SESSION['codom']."' or r.codigo in (select distinct d.reservatorio_codigo 
																					from om o, reservatorio r, disponibilidade d 
																					where o.codom=d.om_codom and r.codigo=d.reservatorio_codigo and 
																					r.combustivel_codigo='1' and o.oc='".$_SESSION['codom']."'))
				ORDER BY c.nome";
	} elseif ($_SESSION['perfil']=='FISCAL DA OM') {
			$qryres = "SELECT r.codigo, o.sigla, c.nome, r.capacidade
				FROM om o, combustivel c, reservatorio r 
				where o.codom=r.om_codom
				and c.codigo=r.combustivel_codigo
				and r.combustivel_codigo='1'
				and r.om_codom='".$_SESSION['posto']."'
				ORDER BY c.nome";
	}
//	echo $qryres;
	$resultado = mysql_query($qryres);
	while ($reservatorio = mysql_fetch_array($resultado, MYSQL_NUM)) {
		echo "<tr bgcolor='#7EC0EE' align='left'><td><font size=2> Nº $reservatorio[0] - $reservatorio[1] </td><td><font size=2> $reservatorio[2] </td><td align='right'><font size=2> $reservatorio[3]  </td>";
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

				$query = ("SELECT sum(round(qtd_rec,0)) 
					FROM recebimento
					WHERE reservatorio_codigo='".$reservatorio[0]."'
					");
//					and dt_rec between '".$data_ini."' and '".$data_fim."'
				$res7 = mysql_query($query,$conexao);
				$qtd_rec = mysql_fetch_row($res7);

				$query = ("SELECT sum(round(c.qtd_destinada,0)) 
					FROM recebimento r, cotas c
					WHERE r.nr_nota_fiscal=c.recebimento_nr_nota_fiscal
					and r.combustivel_codigo=c.recebimento_combustivel_codigo
					and r.reservatorio_codigo='".$reservatorio[0]."'
					");
//					and r.dt_rec between '".$data_ini."' and '".$data_fim."'
				$res8 = mysql_query($query,$conexao);
				$qtd_destinada = mysql_fetch_row($res8);

				$qtd_nao_dist = $qtd_rec[0] - $qtd_destinada[0];

				$qtd_nao_dist = str_replace(".", ",",$qtd_nao_dist);
				if ($qtd_nao_dist!='') {
					echo $qtd_nao_dist." ";
				} else {
					echo "0 ";
				}
			echo "</td>";
*/
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

array_push($gas_qtd_atual,$qtd_atual);
array_push($gas_n_dist,$qtd_nao_dist);
array_push($gas_qtd_de,$qtd_de);
array_push($gas_qtd_bda,$qtd_bda);
array_push($gas_qtd_livre,$qtd_livre);

    	}
		echo "<tr bgcolor='#7EC0EE' align='left'><td colspan='2' align='right'><font size=2><b><i> Somatório de Gasolina&nbsp;&nbsp;</td><td align='right'><font size=2><b><i> $capac_total </td><td align='right'><font size=2><b><i> $atual_total  </td><td align='right'><font size=2><b><i> $livre_total  </td>";

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
		echo "<tr bgcolor='#7EC0EE' align='left'><td><font size=2> Nº $reservatorio[0] - $reservatorio[1] </td><td><font size=2> $reservatorio[2] </td><td align='right'><font size=2> $reservatorio[3]  </td>";
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

				$query = ("SELECT sum(round(qtd_rec,0)) 
					FROM recebimento
					WHERE reservatorio_codigo='".$reservatorio[0]."'
					");
//					and dt_rec between '".$data_ini."' and '".$data_fim."'
				$res7 = mysql_query($query,$conexao);
				$qtd_rec = mysql_fetch_row($res7);

				$query = ("SELECT sum(round(c.qtd_destinada,0)) 
					FROM recebimento r, cotas c
					WHERE r.nr_nota_fiscal=c.recebimento_nr_nota_fiscal
					and r.combustivel_codigo=c.recebimento_combustivel_codigo
					and r.reservatorio_codigo='".$reservatorio[0]."'
					");
//					and r.dt_rec between '".$data_ini."' and '".$data_fim."'
				$res8 = mysql_query($query,$conexao);
				$qtd_destinada = mysql_fetch_row($res8);

				$qtd_nao_dist = $qtd_rec[0] - $qtd_destinada[0];

				$qtd_nao_dist = str_replace(".", ",",$qtd_nao_dist);
				if ($qtd_nao_dist!='') {
					echo $qtd_nao_dist." ";
				} else {
					echo "0 ";
				}
			echo "</td>";
*/
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
		echo "<tr bgcolor='#7EC0EE' align='left'><td colspan='2' align='right'><font size=2><b><i> Somatório de Diesel&nbsp;&nbsp;</td><td align='right'><font size=2><b><i> $capac_total </td><td align='right'><font size=2><b><i> $atual_total  </td><td align='right'><font size=2><b><i> $livre_total  </td>";


	?>

</table><p>

</html>

