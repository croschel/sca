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
<font size=5><br><b>Relatório de Abastecimentos Autorizados e Não Executados</b></font> <br>
<?php
if ($_GET['dt_ini'] == '') {
  $_GET['dt_ini'] = date('Y-m-d', mktime(0, 0, 0, date(m), date(d)-30, date(Y)));
  $_GET['dt_fim'] = date('Y-m-d');
} else {
	$_GET['dt_ini'] = substr($_GET['dt_ini'],6,4)."-".substr($_GET['dt_ini'],3,2)."-".substr($_GET['dt_ini'],0,2);
	$_GET['dt_fim'] = (substr($_GET['dt_fim'],6,4))."-".substr($_GET['dt_fim'],3,2)."-".substr($_GET['dt_fim'],0,2);
}

$dt_inicio = substr($_GET['dt_ini'],8,2)."/".substr($_GET['dt_ini'],5,2)."/".substr($_GET['dt_ini'],0,4);
$dt_final = substr($_GET['dt_fim'],8,2)."/".substr($_GET['dt_fim'],5,2)."/".substr($_GET['dt_fim'],0,4);

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

	echo "<tr align='center'>";
		echo "<form name='frm_autz' method='GET' action='index.php?nomeArquivo=rel_autz.php&&ordem=".$_GET['ordem']."' >";
		echo "<td colspan='10' align='center'>";
			echo "<input type='hidden' name='nomeArquivo' value='rel_autz.php'>";
			echo "<input type='hidden' name='ordem' value='".$_GET['ordem']."'>";
			echo "<br><b><I>Período de: </b></i><input type='text' OnKeyUp='mascara(this.value);' size='10' maxlength='10' name='dt_ini' id='dt_ini' value=".$dt_inicio."><b><I>  </b></I> <input type='text' size='10' maxlength='10' name='dt_fim' id='dt_fim' value=".$dt_final."> <input type='submit' value= 'Pesquisar'>";
			if($_SESSION['perfil']=='ABASTECEDOR') {
				echo "&nbsp;&nbsp;&nbsp;<a href='./imp_rel_autz.php?cma=$cma&oc=$oc_geral&codom=".$_SESSION['codom']."&sigla=".$_SESSION['unidade']."&ordem=".$_GET['ordem']."&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."' title='Imprimir Relação'><img src='./imagens/printer.gif' width='30' height='30' border='0'></a>";
			}
		echo "</td>";
		echo "</form>";
	echo "</tr>";
	echo "<tr align='center' bgcolor='$cor1'>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='1'){echo "1 desc";}else{echo "1";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>OM</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='2'){echo "2 desc";}else{echo "2";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>EB</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='10'){echo "10 desc";}else{echo "10";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>Viatura</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='3'){echo "3 desc";}else{echo "3";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>Data</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='4'){echo "4 desc";}else{echo "4";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>Comb</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='5'){echo "5 desc";}else{echo "5";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>Local</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='6'){echo "6 desc";}else{echo "6";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>Qtd</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='8'){echo "8 desc";}else{echo "8";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>Cota</font></td>";
		echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='7'){echo "7 desc";}else{echo "7";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>Missão</font></td>";
/*		if($_SESSION['perfil']=='ABASTECEDOR') {
			echo "<td><b><a href='index.php?nomeArquivo=rel_autz.php&&ordem="; if ($_GET['ordem']=='9'){echo "9 desc";}else{echo "9";} echo "&dt_ini=".$dt_inicio."&dt_fim=".$dt_final."' style='text-decoration:none'><font color='$cor_submenu'>Código</font></td>";
		}
	echo "</tr>";*/
        /*Conexão ao Banco de Dados*/
    	include "conexao.php";
    	/*Seleciona a relação de abastecimentos*/
	if ($_SESSION['perfil']=='ABASTECEDOR')	{		
		$query ="SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, r.codigo, a.qtd_autorizada, a.missao, a.cota_om, a.cod_seguranca, concat(v.marca,' - ', v.modelo), a.tp_abastecimento
					FROM combustivel c, viatura v, abastecimento a, om o, reservatorio r
					WHERE o.codom=v.om_codom
						and v.eb=a.viatura_eb
						and c.codigo = v.combustivel_codigo
						and r.codigo=a.reservatorio_codigo
						and a.dt_autorizacao between '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'
						and a.qtd_abastecida is null 
						and r.om_codom = '".$_SESSION['codom']."'
					order by ".$_GET['ordem'];
						
	} elseif ($_SESSION['perfil']=='FISCAL DA OM') {
		$query ="SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, r.codigo, a.qtd_autorizada, a.missao, a.cota_om, a.cod_seguranca, concat(v.marca,' - ', v.modelo), a.tp_abastecimento
					FROM combustivel c, viatura v, abastecimento a, om o, reservatorio r
					WHERE o.codom=v.om_codom
						and v.eb=a.viatura_eb
						and c.codigo = v.combustivel_codigo
						and r.codigo=a.reservatorio_codigo
						and a.dt_autorizacao between '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'
						and a.qtd_abastecida is null 
						and a.cota_om = '".$_SESSION['codom']."'
					order by ".$_GET['ordem'];
	} elseif (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='CONSULTOR')){
		$query = "SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, a.disponibilidade_reservatorio_codigo, a.qtd_autorizada, a.missao, a.disponibilidade_om_codom
				FROM abastecimento a, om o, combustivel c, viatura v
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND (o.oc='".$_SESSION['codom']."' or o.codom='".$_SESSION['codom']."')
				AND a.dt_abastecimento between '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'
				AND a.qtd_abastecida is null 
				union
				SELECT o.sigla, concat(a.viatura_eb,' - Cisterna'), a.dt_autorizacao, c.nome, a.disponibilidade_reservatorio_codigo, a.qtd_autorizada, a.missao, a.disponibilidade_om_codom 
				FROM combustivel c, reservatorio r, om o, disponibilidade d, cisterna a
				WHERE c.codigo = r.combustivel_codigo
				and r.codigo=d.reservatorio_codigo
				and o.codom=d.om_codom
				and d.om_codom=a.disponibilidade_om_codom
				and d.reservatorio_codigo=a.disponibilidade_reservatorio_codigo
				and (o.codom = '".$_SESSION['codom']."' or o.oc = '".$_SESSION['codom']."')
				AND a.dt_autorizacao between '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'
				and a.qtd_carregada is null
				order by ".$_GET['ordem'];
		$query ="SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, r.codigo, a.qtd_autorizada, a.missao, a.cota_om, a.cod_seguranca, concat(v.marca,' - ', v.modelo), a.tp_abastecimento
					FROM combustivel c, viatura v, abastecimento a, om o, reservatorio r
					WHERE o.codom=r.om_codom
						and v.eb=a.viatura_eb
						and c.codigo = v.combustivel_codigo
						and r.codigo=a.reservatorio_codigo
						and a.dt_autorizacao between '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'
						and a.qtd_abastecida is null 
						and o.oc = '".$_SESSION['codom']."'
					order by ".$_GET['ordem'];
	} else {
		$query ="SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, r.codigo, a.qtd_autorizada, a.missao, a.cota_om, a.cod_seguranca, concat(v.marca,' - ', v.modelo), a.tp_abastecimento
					FROM combustivel c, viatura v, abastecimento a, om o, reservatorio r
					WHERE o.codom=r.om_codom
						and v.eb=a.viatura_eb
						and c.codigo = v.combustivel_codigo
						and r.codigo=a.reservatorio_codigo
						and a.dt_autorizacao between '".$_GET['dt_ini']." 00:00' and '".$_GET['dt_fim']." 23:59'
						and a.qtd_abastecida is null 
					order by ".$_GET['ordem'];
	}

		$result = mysql_query($query);
    	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$ano=substr($row[2],0,4);
		$mes=substr($row[2],5,2);
		$dia=substr($row[2],8,2);
		$hora=substr($row[2],11,5);
		$dt_nota = $dia."/".$mes."/".$ano;
		/*Seeleciona a OM que saira o combustivel*/
		$busca="select sigla from om where codom='$row[7]'";
		$resultado = mysql_query($busca,$conexao);
		$cota = mysql_fetch_row($resultado);
		/*Seeleciona a OM do reservatorio*/
		$busca="select o.sigla from om o, reservatorio r where o.codom=r.om_codom and r.codigo='$row[4]'";
		$resultado = mysql_query($busca,$conexao);
		$local = mysql_fetch_row($resultado);
		/*Marca como cisterna*/ 
		if($row[10]=='Carregamento de Cisterna') {
			$row[9]=$row[10];
		}
		echo "<tr bgcolor='$cor3' align='center' class='marcar_linha'>
			<td align='left'><font size='2'>$row[0]</td><td><font size='2'>$row[1]</td><td><font size='2'>$row[9]</td><td><font size='2'>$dt_nota</td><td><font size='2'>$row[3]</td><td><font size='2'>$local[0]</td>
			<td align='right'><font size='2'>$row[5] lts&nbsp;</td><td align='left'><font size='2'>$cota[0]</td><td align='left'><font size='2'>$row[6] </td>";
/*			if($_SESSION['perfil']=='ABASTECEDOR') {
				echo "<td><font size='2'>$row[8]</td>";
			}*/
			echo "</tr>";
    	}
    	$num_rows = mysql_num_rows($result);
    	mysql_free_result($result);

	echo "</tr>";
echo "</table>";
?>