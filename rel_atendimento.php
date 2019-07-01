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
<t1><br>Relatório Periódico de Abastecimentos</t1></font> <br>
<?php 
if ($_GET['dt_ini'] == '') {
  $data_ini = date('Y-m-d', mktime(0, 0, 0, date(m), date(d)-10, date(Y)));
  $data_fim = date('Y-m-d');
}
else {
  $data_ini = substr($_GET['dt_ini'],6,4)."-".substr($_GET['dt_ini'],3,2)."-".substr($_GET['dt_ini'],0,2);
  $data_fim = substr($_GET['dt_fim'],6,4)."-".substr($_GET['dt_fim'],3,2)."-".substr($_GET['dt_fim'],0,2);
}

$data_fim = $data_fim;
$dt_inicio = substr($data_ini,8,2)."/".substr($data_ini,5,2)."/".substr($data_ini,0,4);
$dt_final = (substr($data_fim,8,2))."/".substr($data_fim,5,2)."/".substr($data_fim,0,4);

	if($_GET['codom']=='') {$_GET['codom']='Todas';}
	if($_GET['cota']=='') {$_GET['cota']='Todas';}

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
  
 <?php 

	echo "<table border='0' width='100%' align='center' valign='top'>";
	echo "<form name='frm_autz' method='GET' action='index.php?nomeArquivo=rel_atendimento.php&ordem=".$_GET['ordem']."' >";
	echo "<input type='hidden' name='nomeArquivo' value='rel_atendimento.php'>";
	echo "<input type='hidden' name='ordem' value='".$_GET['ordem']."'>";
	/*/Relação de OM PA ou PDA*/
	if(($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *')) {
		$qryom = "select distinct om.codom, om.sigla, om.nome, om.oc 
					from om, reservatorio r
					where om.codom=r.om_codom
					order by om.oc desc, om.sigla";
	} elseif(($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
		$qryom = "select distinct om.codom, om.sigla, om.nome, om.oc  
					from om, reservatorio r
					where om.codom=r.om_codom
						and om.subordinacao='".$_SESSION['codom']."'
					order by om.oc, om.sigla ";
	} elseif((($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']=='SIM')) or ($_SESSION['perfil']=='ABASTECEDOR')) {
		$qryom = "select distinct om.codom, om.sigla, om.nome 
					from om, reservatorio r
					where om.codom=r.om_codom
						and om.codom='".$_SESSION['codom']."'
		";
	} elseif(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
		$qryom = "select distinct codom, sigla, nome 
					from om
					where codom in (select om_codom from reservatorio where codigo in (SELECT distinct `reservatorio_codigo` FROM `estoque` WHERE om_codom='".$_SESSION['codom']."'))
		";
	}
//echo $qryom;
	$resom = mysql_query($qryom);
	echo "<tr align='center' >
				<td colspan='8' ><br><b><I>OM Abastecedora: </b></i>";
			echo "<select name='codom' >";
			if(($_SESSION['perfil']=='ADMINISTRADOR *') or ($_SESSION['perfil']=='CONSULTOR *') or ($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')) {
				echo "<option value='Todas' >TODAS</option>";
			} 
			while ($om = mysql_fetch_array($resom, MYSQL_NUM)) {
				echo "<option value='$om[0]' "; if($_GET['codom']==$om[0]) {echo "selected"; $sigla=$om[1]; $nome=$om[2];} echo "> $om[1] </option>";
				$codom=$codom."','".$om[0];
			}
			echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
			
			
	/*/Relação de OM que tiveram abastecimento na sua cota */
	if(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
		$qrycota = "SELECT codom, sigla
					FROM om
					where om.codom='".$_SESSION['codom']."' 
					order by om.oc, om.sigla";
	} else {
		$qrycota = "SELECT distinct om.codom, om.sigla
					FROM om, (select * from reservatorio where om_codom in('".$codom."')) r, abastecimento a
					where om.codom=a.cota_om
						and r.codigo=a.reservatorio_codigo
						AND a.dt_abastecimento between '".$data_ini." 00:00' and '".$data_fim." 23:59'
						AND a.qtd_abastecida is not null 
					order by om.sigla";
	}

	$rescota = mysql_query($qrycota);
			echo "<b><I>OM Abastecida: </b></i>";
			echo "<select name='cota' >";
			if(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
	//			echo "<option value='Todas' ></option>";
			} else {
				echo "<option value='Todas' >TODAS</option>";
			}
			while ($cota = mysql_fetch_array($rescota, MYSQL_NUM)) {
				echo "<option value='$cota[0]' "; if($_GET['cota']==$cota[0]) {echo "selected";} echo "> $cota[1] </option>";
			}
			echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			echo "<b><I>Período de: </b></i>
					<input type='text' OnKeyUp='mascara(this.value);' size='10' maxlength='10' name='dt_ini' id='dt_ini' value='".$dt_inicio."'><b><I>  </b></I> 
					<input type='text' OnKeyUp='mascara1(this.value);' size='10' maxlength='10' name='dt_fim' id='dt_fim' value='".$dt_final."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
					<input type='submit' value= 'Pesquisar'><br>
				</td>
			</tr>";
	echo "</form>";

	$query = "SELECT distinct r.om_codom, a.cota_om 
						FROM abastecimento a, historico_abastecimento h, reservatorio r
						WHERE r.codigo=a.reservatorio_codigo
							and a.dt_abastecimento=h.dt_abastecimento
							and a.viatura_eb=h.viatura_eb
							and h.qtd_abastecida > 0
							and h.dt_abastecimento between  '".$data_ini." 00:00' and '".$data_fim." 23:59' ";
	if(($_GET['codom']!='Todas') and ($_GET['codom']!='')) {
		$query=$query."and a.reservatorio_codigo in(select codigo from reservatorio where om_codom='".$_GET['codom']."') ";	
	} else {	
		$query=$query."and a.reservatorio_codigo in(select codigo from reservatorio where om_codom in ('".$codom."')) ";	
	}
	if(($_GET['cota']!='Todas') and ($_GET['cota']!='')) {
		$query=$query."AND a.cota_om='".$_GET['cota']."' ";
	}
	$query=$query."order by 1,2";

	if(($_SESSION['perfil']=='FISCAL DA OM') and ($_SESSION['gerente']!='SIM')) {
	} elseif(($_GET['codom']!='Todas') and ($_GET['codom']!='')) {
		echo "<tr align='center' bgcolor='$cor_fundo'>";
			echo "<td colspan='10'><b><a href='impr_rel_atendimento.php?cma=$cma&oc=$oc_geral&sigla=".$sigla."&om=".$nome."&codom=".$_GET['codom']."&dt_ini=".$data_ini."&dt_fim=".$data_fim."&oc_geral&cota=".$_GET['cota']."' style='text-decoration:none' target='_blank' title='Gerar relatório em PDF'><img src='./imagens/pdf.jpg' width='35' height='40' border='0'></a></td>";
		echo "</tr>";
	}
	echo "<tr align='center' bgcolor='$cor1'>";
		echo "<td rowspan='2'><coluna>OM Abastecedora</td>";
		echo "<td rowspan='2'><coluna>OM Atendida</td>";
		echo "<td colspan='4'><coluna>Óleo Diesel</td>";
		echo "<td colspan='4'><coluna>Gasolina</td>";
		echo "<td rowspan='2'><coluna>Total</td>";
	echo "</tr>";

	echo "<tr align='center' bgcolor='$cor1'>";
		echo "<td><coluna>Nota Fiscal</td>";
		echo "<td><coluna>Consumo<br>(litros)</td>";
		echo "<td><coluna>Valor<br>Unitário<br>(R$)</td>";
		echo "<td><coluna>Subtotal</td>";
		echo "<td><coluna>Nota Fiscal</td>";
		echo "<td><coluna>Consumo<br>(litros)</td>";
		echo "<td><coluna>Valor<br>Unitário<br>(R$)</td>";
		echo "<td><coluna>Subtotal</td>";
	echo "</tr>";

	$result = mysql_query($query);
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		/*Seleciona a sigla da OM do reservatorio*/
		$busca="select o.sigla from om o, reservatorio r where o.codom='".$row[0]."'";
		$resultado = mysql_query($busca,$conexao);
		$local = mysql_fetch_row($resultado);
		
		/*Seeleciona a OM que saiu o combustivel*/
		$busca="select sigla from om where om.codom='".$row[1]."'";
		$resultado = mysql_query($busca,$conexao);
		$abast = mysql_fetch_row($resultado);

		echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'>";
				echo "<td align='left'><lin>$local[0]</td>";
				echo "<td align='left'><lin>$abast[0]</td>";
				
				echo "<td colspan='4'>";
   			$qryod = "SELECT r.nr_nota_fiscal, r.reservatorio_codigo, sum(h.qtd_abastecida), r.valor, sum(h.qtd_abastecida)*r.valor ,e.combustivel_codigo
						FROM recebimento r, estoque e, abastecimento a, historico_abastecimento h
						WHERE r.nr_nota_fiscal=e.nr_nota_fiscal
							and r.combustivel_codigo=e.combustivel_codigo
							and e.nr_repasse=h.nr_repasse
							and e.om_codom=h.om_codom
							and a.dt_abastecimento=h.dt_abastecimento
							and a.viatura_eb=h.viatura_eb
							and h.qtd_abastecida > 0
							and h.dt_abastecimento between  '".$data_ini." 00:00' and '".$data_fim." 23:59' 
							and e.om_codom = '$row[1]' 
							and e.combustivel_codigo = '2' 
							and r.reservatorio_codigo in( select codigo from reservatorio where om_codom='$row[0]')
						group by r.nr_nota_fiscal, r.reservatorio_codigo, e.om_codom ";

				$resod = mysql_query($qryod);
				echo "<table border='0' width='100%' align='center' valign='top' >";
   			while ($od = mysql_fetch_array($resod, MYSQL_NUM)) {
					echo "<tr>";
					echo "<td width='25%'><lin>$od[0]</td>";
					echo "<td width='25%' align='right'><lin>".number_format($od[2], 0, ',', '.')."&nbsp;Lts&nbsp;</td>";
					echo "<td width='25%' align='right'><lin>".number_format($od[3], 2, ',', '.')."&nbsp;</td>";
					echo "<td width='25%' align='right'><lin>".number_format($od[4], 2, ',', '.')."&nbsp;</td>";
					echo "</tr>";
					$die = $die + $od[4];
				}
				if(mysql_num_rows($resod)==0) {
					echo "<tr><td width='25%'>-</td><td width='25%' align='right'>-&nbsp;</td><td width='25%' align='right'>-&nbsp;</td><td width='25%' align='right'>-&nbsp;</td></tr>";
				}
				echo "</table>";
				echo "</td>";

				echo "<td colspan='4'>";
   			$qryg = "SELECT r.nr_nota_fiscal, r.reservatorio_codigo, sum(h.qtd_abastecida), r.valor, sum(h.qtd_abastecida)*r.valor ,e.combustivel_codigo
						FROM recebimento r, estoque e, abastecimento a, historico_abastecimento h
						WHERE r.nr_nota_fiscal=e.nr_nota_fiscal
							and r.combustivel_codigo=e.combustivel_codigo
							and e.nr_repasse=h.nr_repasse
							and e.om_codom=h.om_codom
							and a.dt_abastecimento=h.dt_abastecimento
							and a.viatura_eb=h.viatura_eb
							and h.qtd_abastecida > 0
							and h.dt_abastecimento between  '".$data_ini." 00:00' and '".$data_fim." 23:59' 
							and e.om_codom = '$row[1]'
							and e.combustivel_codigo = '1' 
							and r.reservatorio_codigo in( select codigo from reservatorio where om_codom='$row[0]')
						group by r.nr_nota_fiscal, r.reservatorio_codigo, e.om_codom ";

				$resg = mysql_query($qryg);

				echo "<table border='0' width='100%' align='center' valign='top'>";
	 			while ($g = mysql_fetch_array($resg, MYSQL_NUM)) {
					echo "<tr>";
					echo "<td width='25%'><lin>$g[0]</td>";
					echo "<td width='25%' align='right'><lin>".number_format($g[2], 0, ',', '.')."&nbsp;Lts&nbsp;</td>";
					echo "<td width='25%' align='right'><lin>".number_format($g[3], 2, ',', '.')."&nbsp;</td>";
					echo "<td width='25%' align='right'><lin>".number_format($g[4], 2, ',', '.')."&nbsp;</td>";
					echo "</tr>";
					$gas = $gas + $g[4];
				}
				if(mysql_num_rows($resg)==0) {
					echo "<tr><td width='25%'>-</td><td width='25%' align='right'>-&nbsp;</td><td width='25%' align='right'>-&nbsp;</td><td width='25%' align='right'>-&nbsp;</td></tr>";
				}
				echo "</table>";
				echo "</td>";
				echo "<td align='right'><lin>".number_format(($gas + $die), 2, ',', '.')."&nbsp;</td>";
				$gas = $die = 0;
		echo "</tr>";
   }
    	$num_rows = mysql_num_rows($result);
    	mysql_free_result($result);

	echo "</tr>";
echo "</table>";
?>