<?php
     include "verifica_sessao.php";
?>

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

	echo "<form name='viatura' method='POST' action='index.php?nomeArquivo=gravar_viatura.php' >";
	echo "<center><t1><br>Cadastro de Viatura <p></t1> </center>";
	echo "<table>";
	echo "<tr><td align='right'>EB ou Placa da Viatura:* </td><td><input type='text' size='10' maxlength='10' name='eb' required ></td></tr>";
	echo "<tr><td align='right'>Marca da Viatura:* </td><td><input type='text' size='20' maxlength='20' name='marca' required ></td></tr>";
	echo "<tr><td align='right'>Modelo da Viatura:* </td><td><input type='text' size='20' maxlength='20' name='modelo' required ></td></tr>";
	echo "<tr><td align='right'>Combustível:* </td><td><select name='combustivel' required >";
						
						/*Seleciona a relacao de om*/
						$result = mysql_query("select * from combustivel order by nome desc");
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							echo "<option value=\"".$row[0]."\" >".$row[1];
						}
						 echo "</select></td></tr>";
	echo "<tr><td align='right'>Capacidade do Tanque:* </td><td><input type='text' size='4' maxlength='4' name='capacidade' dir='rtl' required > L</td></tr>";
	echo "<tr><td align='right'>Consumo Médio:* </td><td><input type='text' size='4' maxlength='5' name='consumo' dir='rtl' onchange='number_format(this.value,2, ',', '.')' required > Km/L</td></tr>";
						/*Seleciona a relação de alunos*/
						if ($_SESSION['perfil']=='ADMINISTRADOR *'){ 
							$query="select codom, sigla, nome, tipo, oc, subordinacao from om order by tipo, sigla";
						} elseif ($_SESSION['perfil']=='ADMINISTRADOR') {
							$query="select * from om where oc='".$_SESSION['codom']."' or tipo='Não Integrante' order by tipo, sigla";
						} elseif ($_SESSION['perfil']=='FISCAL DA OM') {
							$query="select * from om where tipo='Não Integrante' or codom='".$_SESSION['codom']."' or (tipo<>'OC' and oc is null) order by tipo, sigla";
						} else{
							$query="select * from  om where codom=".$_SESSION['codom']." order by oc desc, sigla";
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
							SELECT codom, sigla cmd, sigla, sigla, ifnull(subordinacao,'UNIDADES NÃO INTEGRANTES')  
							from om 
							where tipo='Não Integrante'
							order by abs(cmd), cmd, abs(sigla) ";
						$result = mysql_query($query);
	echo "<tr><td align='right'>OM da Viatura:* </td><td><select name='codom' required >";
						echo "<option value='' selected disabled>:-: ESCOLHA A OM :-:</option>";
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							if($cmdo!=$row[4]) {
  								echo "<optgroup label=''>";
  								echo "<optgroup label=':. $row[4] .:'>";
  								$cmdo=$row[4];
  							}
							echo "<option value='$row[0]'"; if ($row[0]==$_SESSION['codom']){ echo "selected";} echo ">$row[1]</option>";
						}
						echo "</select></td></tr>";
	echo "<tr><td align='right'>Situação:* </td><td><select name='situacao' size='1' required >";
            						echo "<option value='Em Carga' selected>Em Carga</option>";
										echo "<option value='Descarregada'>Descarregada</option>";
          					echo "</select></td></tr>";
	echo "<tr><td align='right'>Disponível:* </td><td><select name='disponivel' size='1' required >";
            						echo "<option value='SIM' selected>SIM</option>";
            						echo "<option value='NÃO'>NÃO</option>";
          					echo "</select></td></tr>";
	echo "<input type='hidden' name='operacao' value='1'>";
	echo "<tr><td>&nbsp;</td><td></td></tr>";
	echo "<tr><td align='right'><input type='submit' value= 'Cadastrar'></td><td><input type='reset' value= '  Limpar  '>&nbsp;<input type='button' value= 'Retornar' onclick='history.go(-1)'></td></tr>";
  	echo "</table>";
echo "</form>";

?>
