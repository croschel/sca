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

<font size=5><br><b>Consulta de Abastecimentos Realizados</b></font> <br>
<?php
if ($_GET['dt_ini'] == '') {
  $data_ini = date('Y-m-d', mktime(0, 0, 0, date(m), date(d)-30, date(Y)));
  $data_fim = date('Y-m-d');
}
else {
  $data_ini = substr($_GET['dt_ini'],6,4)."-".substr($_GET['dt_ini'],3,2)."-".substr($_GET['dt_ini'],0,2);
  $data_fim = substr($_GET['dt_fim'],6,4)."-".substr($_GET['dt_fim'],3,2)."-".substr($_GET['dt_fim'],0,2);
}

$data_fim = $data_fim;
$dt_inicio = substr($data_ini,8,2)."/".substr($data_ini,5,2)."/".substr($data_ini,0,4);
$dt_final = (substr($data_fim,8,2))."/".substr($data_fim,5,2)."/".substr($data_fim,0,4);
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

	echo "<form name='frm_autz' method='GET' action='index.php?nomeArquivo=cons_abast.php' >";
	echo "<input type='hidden' name='nomeArquivo' value='cons_abast.php'>";
	echo "<input type='hidden' name='ordem' value='".$_GET['ordem']."'>";
	echo "<tr align='center' >";
		echo "<td colspan='8' ><br> <b><I>Período de: </b></i><input type='text' OnKeyUp='mascara(this.value);' size='10' maxlength='10' name='dt_ini' id='dt_ini' value='$dt_inicio'><b><I>  </b></I> <input type='text' OnKeyUp='mascara1(this.value);' size='10' maxlength='10' name='dt_fim' id='dt_fim' value='$dt_final'> <input type='submit' value= 'Pesquisar'><br><br>";
	echo "</td>";
	echo "</tr>";
	echo "</form>";
	echo "<tr align='center' bgcolor='#4A708B'>";
		echo "<td width='20%' ><b><a href='index.php?nomeArquivo=cons_abast.php&&ordem="; if ($_GET['ordem']=='1'){echo "1 desc";}else{echo "1";} echo "&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."'>OM</td>";
		echo "<td width='10%' ><b><a href='index.php?nomeArquivo=cons_abast.php&&ordem="; if ($_GET['ordem']=='3'){echo "3 desc";}else{echo "3";} echo "&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."'>EB Viatura</td>";
		echo "<td width='8%' ><b><a href='index.php?nomeArquivo=cons_abast.php&&ordem="; if ($_GET['ordem']=='2'){echo "2 desc";}else{echo "2";} echo "&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."'>Data</td>";
		echo "<td width='7%' ><b><a href='index.php?nomeArquivo=cons_abast.php&&ordem="; if ($_GET['ordem']=='4'){echo "4 desc";}else{echo "4";} echo "&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."'>Comb</td>";
		echo "<td width='5%' ><b><a href='index.php?nomeArquivo=cons_abast.php&&ordem="; if ($_GET['ordem']=='6'){echo "6 desc";}else{echo "6";} echo "&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."'>Qtd</td>";
		echo "<td width='10%' ><b><a href='index.php?nomeArquivo=cons_abast.php&&ordem="; if ($_GET['ordem']=='8'){echo "8 desc";}else{echo "8";} echo "&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."'>Local</td>";
		echo "<td width='20%' ><b><a href='index.php?nomeArquivo=cons_abast.php&&ordem="; if ($_GET['ordem']=='10'){echo "10 desc";}else{echo "10";} echo "&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."'>Missão</td>";
		echo "<td width='20%' ><b><a href='index.php?nomeArquivo=cons_abast.php&&ordem="; if ($_GET['ordem']=='7'){echo "7 desc";}else{echo "7";} echo "&dt_ini=".$_GET['dt_ini']."&dt_fim=".$_GET['dt_fim']."'>Motorista</td>";

	echo "</tr>";

	echo "<tr bgcolor='#4A708B'>";
	    
        /*Conexão ao Banco de Dados*/
    	include "conexao.php";
    	/*Seleciona a relação de abastecimentos*/
	if (($_SESSION['perfil']=='ADMINISTRADOR') or ($_SESSION['perfil']=='FISCAL DA OM')){
		$query = "SELECT o.sigla, a.dt_abastecimento, a.viatura_eb, c.nome, a.qtd_autorizada, a.qtd_abastecida, a.motorista, a.disponibilidade_reservatorio_codigo, a.usuario_autz, a.missao, a.local
				FROM abastecimento a, om o, combustivel c, viatura v
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND a.dt_abastecimento between '".$data_ini."' and '".$data_fim." 23:59' 
				AND v.om_codom = '".$_SESSION['codom']."'
				AND a.qtd_abastecida is not null 
				
				union
				
				SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, a.disponibilidade_reservatorio_codigo, a.qtd_abastecida, a.usuario_autz, a.missao, a.disponibilidade_om_codom, a.local
				FROM abastecimento a, om o, combustivel c, viatura v
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND o.codom<>'".$_SESSION['codom']."'
				AND a.dt_abastecimento between '".$data_ini." 00:00' and '".$data_fim." 23:59'
				AND a.qtd_abastecida is not null
				AND a.disponibilidade_reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')

				order by ".$_GET['ordem'];

	} 
	else{
		$query = "SELECT o.sigla, a.dt_abastecimento, a.viatura_eb, c.nome, a.qtd_autorizada, a.qtd_abastecida, a.motorista, a.disponibilidade_reservatorio_codigo, a.usuario_autz, a.missao, a.local
				FROM abastecimento a, om o, combustivel c, viatura v
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND a.dt_abastecimento between '".$data_ini."' and '".$data_fim." 23:59' 
				AND a.disponibilidade_reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')
				AND a.qtd_abastecida is not null 
				
				union
				
				SELECT o.sigla, a.viatura_eb, a.dt_abastecimento, c.nome, a.disponibilidade_reservatorio_codigo, a.qtd_abastecida, a.usuario_autz, a.missao, a.disponibilidade_om_codom, a.local
				FROM abastecimento a, om o, combustivel c, viatura v
				WHERE o.codom = v.om_codom
				AND c.codigo = v.combustivel_codigo
				AND v.eb = a.viatura_eb
				AND o.codom<>'".$_SESSION['codom']."'
				AND a.dt_abastecimento between '".$data_ini." 00:00' and '".$data_fim." 23:59'
				AND a.qtd_abastecida is not null
				AND a.disponibilidade_reservatorio_codigo in (select codigo from reservatorio where om_codom='".$_SESSION['codom']."')

				order by ".$_GET['ordem'];
	} 

 	$result = mysql_query($query);
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		$ano=substr($row[1],0,4);
		$mes=substr($row[1],5,2);
		$dia=substr($row[1],8,2);
		$hora=substr($row[1],11,5);
		$dt_nota = $dia."/".$mes."/".$ano;
		/*Seeleciona a OM do reservatorio*/
		$busca="select o.sigla from om o, reservatorio r where o.codom=r.om_codom and r.codigo='".$row[7]."'";
		$resultado = mysql_query($busca);
		$local = mysql_fetch_row($resultado);
		if($local[0]=='') {
			$local[0]=$row[10];
		}
		echo"<tr bgcolor='#7EC0EE' align='center'><td align='left'><lin>$row[0]</td><td><lin>$row[2]</td><td><lin>$dt_nota</td><td><lin>$row[3]</td><td><lin>$row[5] L</td><td><lin>$local[0]</td><td><lin>$row[9] </td><td align='left'><lin>$row[6]</td></tr>";
   }
   $num_rows = mysql_num_rows($result);
   mysql_free_result($result);

	echo "</tr>";
echo "</table>";

?>
