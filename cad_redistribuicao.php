<?php
  if(empty($_SESSION['usuario']))
     include "verifica_sessao.php";
?>
<Script Language="JavaScript">
function getStates(what) {
   if (what.selectedIndex != '') {
      var cod_om = what.value;
      document.location=('index.php?nomeArquivo=cad_redistribuicao.php&cod_om=' + cod_om);
   }
}
</Script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<table align="left" width='100%' border="0">
	<tr><td align="center" colspan="5" ><font size=5><p><b>REDISTRIBUIÇÃO DE COMBUSTÍVEIS<p></font> </b></td></tr>
	<tr><td></td><td align="center" colspan="3" ><hr></td><td></td></tr>
	<tr><td></td><td colspan="3" width='80%'><b><i>OM Detentora:* <select name="codom" onChange="getStates(this);">
						<option value="" selected></option>
						<?php
						//Conexão ao Banco de Dados
						include "conexao.php";
						//Seleciona a relação de Unidades
//						if ($_SESSION['perfil']=='ADMINISTRADOR'){ 
						if ($_SESSION['gerente']=='SIM'){
							$result = mysql_query("select * from om where subordinada='SIM' order by sigla");
						}
						while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
							$codom = $row[0];
							$sigla = $row[1]?>
							<option value=<?echo $codom;?> <? if ($cod_om==$codom){ echo "SELECTED";} ?> > <? echo $sigla; ?> </option> <?
						}
						?> </select></td><td></td></tr>

	<tr><td></td><td align="center" colspan="3" ><hr></td><td></td></tr>

	<tr align="center"><td></td><td bgcolor='#7EC0EE'><b><i>Combustível</td><td bgcolor='#7EC0EE'><b><i>Local de Armazenamento</td><td width='20%' bgcolor='#7EC0EE'><b><i>Quantidade</td></tr>
	<?php    
        //Conexão ao Banco de Dados
    	include "conexao.php";
    	//Seleciona a Disponibilidade de combustíveis
    	$result = mysql_query("select c.nome, d.reservatorio_codigo, o.sigla, d.qtd_atual
				from reservatorio r, combustivel c, disponibilidade d, om o
				where o.codom=r.om_codom
				and c.codigo=r.combustivel_codigo 
				and r.codigo=d.reservatorio_codigo 
				and d.om_codom='".$cod_om."' 
				and r.om_codom='".$_SESSION['posto']."'
				order by d.reservatorio_codigo");
    	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		echo"<tr'><td width='10%'></td><td bgcolor='#7EC0EE' width='20%'>&nbsp;$row[0]</td><td bgcolor='#7EC0EE' width='40%'>&nbsp;Reservatório nº $row[1] do $row[2] </td><td bgcolor='#7EC0EE' width='20%' align='right'>$row[3] Lts&nbsp;</td><td width='10%'> </td></tr>";
    	}
//    	$num_rows = mysql_num_rows($result);
//    	mysql_free_result($result);
	?>

	<tr><td></td><td align="center" colspan="3" ><hr></td><td></td></tr>

<form name="viatura" method="POST" action="index.php?nomeArquivo=gravar_redistribuicao.php" >
	<tr><td></td><td bgcolor='#7EC0EE' align="right"><b><i>Reservatório: </td><td bgcolor='#7EC0EE'>
										<select name="reservatorio">
										<option value="" selected></option>
										<?php
										//Conexão ao Banco de Dados
										//include "conexao.php";
										//Seleciona a relação de Unidades
										$result = mysql_query("select d.reservatorio_codigo, o.sigla
											from reservatorio r, disponibilidade d, om o
											where o.codom=r.om_codom
											and r.codigo=d.reservatorio_codigo 
											and d.om_codom='".$cod_om."' 
											and r.om_codom='".$_SESSION['posto']."'
											order by d.reservatorio_codigo");
										while ($local = mysql_fetch_array($result, MYSQL_NUM)) {
											echo "<option value=\"".$local[0]."\" >Nº ".$local[0]." do(a) ".$local[1];
											}
											?> </select>
										</td><td></td><td></td></tr>
	<tr><td></td><td bgcolor='#7EC0EE' align="right"><b><i>OM Beneficiada:</td><td bgcolor='#7EC0EE'>
											<select name="om_atd">
											<option value="" selected></option>
											<?php
											//Conexão ao Banco de Dados
											include "conexao.php";
											//Seleciona a relação de Unidades
											$result = mysql_query("select * from om where codom !='".$cod_om."' and subordinada='SIM' order by sigla");
											while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
											echo "<option value=\"".$om[0]."\" >".$om[1];
											}
											?> </select>

												</td><td></td><td></td></tr>
	<tr><td></td><td bgcolor='#7EC0EE' align="right"><b><i>Qtd a Redistribuir:</td><td bgcolor='#7EC0EE'><input type="text" size="6" maxlength="6" name="qtd_atd"> Lts</td><td></td><td></td></tr>
	<input type="hidden" name="om_ret" value="<?echo $cod_om?>">

	<tr><td></td><td align="center" colspan="3" >&nbsp;</td><td></td></tr>
	<tr><td></td><td align="right"><input type="submit" value= "   Gravar   "></td><td><input type="reset" value= "  Limpar  "></td><td></td><td></td></tr>

</form>

</table>

</div>
