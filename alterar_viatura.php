<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<script language="JavaScript" type="text/javascript">
function number_format( number, decimals, dec_point, thousands_sep ) {
	var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
	var d = dec_point == undefined ? "," : dec_point;
	var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
	var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}
</script>

<?php
	include "verifica_sessao.php";
	/*Conexão ao Banco de Dados*/
	include "conexao.php";
	/*Seeleciona a viatura*/
	$query="select v.eb, v.marca, v.modelo, v.combustivel_codigo, v.om_codom, o.sigla, c.nome, v.consumo, v.disponivel, v.cap_tanque, v.situacao
		from viatura v, om o, combustivel c 
		where o.codom=v.om_codom and c.codigo=v.combustivel_codigo and eb='".$_GET['eb']."'";

	$result = mysql_query($query);
	$row = mysql_fetch_row($result);


	echo "<br><t1>Alteração de Viatura</t1> </center><p>";
	echo "<form method='POST' action='index.php?nomeArquivo=gravar_viatura.php' name='viatura'>";
	echo "<table>";
   echo "<input type='hidden' name='eb_ant' value='$row[0]'>";
	echo "<tr><td align='right'>EB ou Placa da Viatura:* </td><td><input type='text' size='10' maxlength='10' name='eb' value='$row[0]' required ></td></tr>";
	echo "<tr><td align='right'>Marca da Viatura:* </td><td><input type='text' size='20' maxlength='20' name='marca' value='$row[1]' required ></td></tr>";
	echo "<tr><td align='right'>Modelo da Viatura:* </td><td><input type='text' size='20' maxlength='20' name='modelo' value='$row[2]' required ></td></tr>";
	echo "<tr><td align='right'>Combustível:* </td><td><select name='combustivel'>";

  						/*/Seleciona a relação de alunos*/
  						$result = mysql_query("select * from combustivel order by nome desc");
  						while ($combustivel = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value='$combustivel[0]' ";
							if ($combustivel[0]==$row[3])
							{
								echo "selected";
							}
							echo ">$combustivel[1] </option>";
						}
					echo "</select></td></tr>";
	echo "<tr><td align='right'>Capacidade do Tanque:* </td><td><input type='text' size='4' maxlength='4' name='capacidade' value='$row[9]' dir='rtl' required > <i>L</td></tr>";
	echo "<tr><td align='right'>Consumo Médio:* </td><td><input type='text' size='4' maxlength='5' name='consumo' value='$row[7]' dir='rtl' required > <i>Km/L</td></tr>";

 						/*Seleciona a relação de alunos*/
						if ($_SESSION['perfil']=='ADMINISTRADOR *'){ 
							$result = mysql_query("select * from om order by tipo, sigla");
						} elseif($_SESSION['perfil']=='ADMINISTRADOR') {
							$result = mysql_query("select * from  om where oc=".$_SESSION['codom']." or tipo='Não Integrante' order by tipo, sigla");
						} else {
							$result = mysql_query("select * from om where codom=".$_SESSION['codom']." or tipo='Não Integrante' or (tipo<>'OC' and oc is null) order by tipo, sigla");
						} 
 
 	$query = "select om.codom, om.sigla sigla, cmdo.codom, om.oc, cmdo.sigla cmd
			from om cmdo, om 
			where om.subordinacao=cmdo.codom ";
									
			/*/Seleciona a relação de Unidades */
			if ($_SESSION['perfil']=='FISCAL DA OM'){
				$query .= "and om.codom='".$_SESSION['codom']."' ";
			} elseif ($_SESSION['perfil']=='ADMINISTRADOR'){
				$query .= "and (cmdo.codom='".$_SESSION['codom']."' or om.codom='".$_SESSION['codom']."') ";
			} 
			$query .= "UNION 
							SELECT codom, sigla cmd, sigla, sigla, ifnull(subordinacao,'UNIDADES NãO INTEGRANTES')  
							from om 
							where tipo='Não Integrante'
							order by abs(cmd), cmd, abs(sigla) ";
			$result = mysql_query($query);
	echo "<tr><td align='right'>OM da Viatura:* </td><td><select name='codom'>";
						echo "<option value='' selected disabled>:-: ESCOLHA A OM :-:</option>";
  						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
  							if($tipo!=$om[4]) {
  								echo "<optgroup label=''>";
  								echo "<optgroup label=':. $om[4] .:'>";
  								$tipo=$om[4];
  							}
							echo "<option value='$om[0]'"; if ($om[0]==$row[4]) { echo "selected"; } echo ">$om[1]</option>";
						}
    					$num_rows = mysql_num_rows($result);
 
 
    					mysql_free_result($result);
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Situação:* </td><td><select name='situacao' size='1' required >";
            						echo "<option value='Em Carga' "; if ($row[10]=='Em Carga'){ echo "selected";} echo " > Em Carga </option>";
            						echo "<option value='Descarregada' "; if ($row[10]=='Descarregada'){ echo "selected";} echo "> Descarregada </option>";
          					echo "</select></td></tr>";
	echo "<tr><td align='right'>Disponível:* </td><td><select name='disponivel' size='1' required >";
            						echo "<option value='SIM' "; if ($row[8]=='SIM'){ echo "selected";} echo " >SIM</option>";
            						echo "<option value='NÃO' "; if ($row[8]!='SIM'){ echo "selected";} echo " >NÃO</option>";
          					echo "</select></td></tr>";
  	echo "<input type='hidden' name='operacao' value='2'>";
   echo "<tr><td></td><td></td></tr>";
   echo "<tr><td align='right'><input type='submit' value= 'Gravar'></td><td><input type='reset' value= '  Cancelar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
   echo "</table>";


echo "</form>";

?>
