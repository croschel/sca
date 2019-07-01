<?php
  if(empty($_SESSION['usuario']))
    include "verifica_sessao.php";
?>
<script language="JavaScript" type="text/javascript">
function mascara(dt_abast){
              var mdata = '';
              mdata = mdata + dt_abast;
              if (mdata.length == 2){
                  mdata = mdata + '/';
                  document.forms[1].dt_abast.value = mdata;
              }
              if (mdata.length == 5){
                  mdata = mdata + '/';
                  document.forms[1].dt_abast.value = mdata;
              }
              if (mdata.length == 10){
                  return true;
              }
          }

</script>

<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<center>
<br>
<font size='5'><b>Abastecimento na Cota de Outra OM </font>
<hr><br>
<table border="0" width="60%" align="left" valign="top">
<form name="frm_autz" method="POST" action="index.php?nomeArquivo=cota_outra_om.php" >
	<tr><td align="right"><b><I>Combustível na Cota da OM: </td><td><select name="codom">
					<option value="" selected> </option>
						<?php
						//Conexão ao Banco de Dados
						include "conexao.php";
						//Seleciona a relaçao de unidades
						$result = mysql_query("select codom, sigla from om where tipo<>'Não Integrante' and oc='".$_SESSION['oc']."' or codom='".$_SESSION['oc']."' order by sigla");
						while ($om = mysql_fetch_array($result, MYSQL_NUM)) {
							$codom1 = $om[0];
							$sigla = $om[1];?>
							<option value=<?echo $codom1;?> <? if ($_POST['codom']==$codom1){ echo "SELECTED";} ?> > <? echo $sigla; ?> </option> <?
							
						}
						?> </select></td></tr>
	<tr><td align="right"><b><I>Digite o EB da Viatura: </I></td><td><input type="text" size="10" maxlength="10" name="eb"  value="<?if ($_POST['eb'] == '') {echo $_POST['eb'];} else {echo $_POST['eb'];}?>">&nbsp;<input type="submit" value= "Localizar"></td></tr>
	<tr><td COLSPAN=2><hr></td></tr>

</form>
<form name="frm_autz_vale" method="POST" action="index.php?nomeArquivo=gravar_cota_outra_om.php" >
	<?php    
	//Conexão ao Banco de Dados
	include "conexao.php";
	//Seleciona a Viatura
	$query = "SELECT o.codom, o.sigla, v.marca, v.modelo, c.nome, v.eb, c.codigo
			FROM om o, viatura v, combustivel c
			WHERE o.codom=v.om_codom
			and c.codigo=v.combustivel_codigo
			and v.eb='".$_POST['eb']."'";
//echo $query;
	$result = mysql_query($query,$conexao);
//	if ($_POST['eb'] != '')
	if ($_POST['eb'] != '')
		$row = mysql_fetch_row($result);
	?>
	<tr><td align="right"><b><I>OM da Viatura: </I></td><td> <font color="blue"> <?echo $row[1]?></td></tr>
	<tr><td align="right"><b><I>EB da Viatura: </I></td><td> <font color="blue"><?echo $row[5]?></td></tr>
	<input type="hidden" name="ebv" value="<?echo $row[5]?>">
	<tr><td align="right"><b><I>Viatura: </I></td><td> <font color="blue"><?echo $row[2]." - ".$row[3]?></td></tr>
	<tr><td align="right"><b><I>Combustível: </I></td><td> <font color="blue"><?echo $row[4]?></td></tr>
	<tr><td align="right"><b><I>Combustível na Cota da OM: </td><td>
								<?	if ($_POST['eb']!=''){
									$query="select sigla from om where codom='".$_POST['codom']."'";
									$result = mysql_query($query,$conexao);
									$om = mysql_fetch_row($result);

									}
//echo $query;								?>
								<font color="blue"><?echo $om[0]?>
						</td></tr>



	<input type="hidden" name="codom" value="<?echo $_POST['codom']?>">
	<?//Seleciona o Reservatório
	$query = "SELECT round(d.qtd_atual,1), d.reservatorio_codigo, d.om_codom
			FROM disponibilidade d, reservatorio r
			WHERE r.codigo=d.reservatorio_codigo
			and d.om_codom = '".$_POST['codom']."'
			and r.om_codom = '".$_SESSION['posto']."'
			and r.combustivel_codigo = '".$row[6]."'";
	$result = mysql_query($query,$conexao);

	if ($_POST['eb'] != '')
		$comb = mysql_fetch_row($result);
	?>
	<tr><td align="right"><b><I>Disponibilidade: </I></td><td> <font color="red"><?echo str_replace(".", ",",$comb[0])?> Lts</td></tr>
	<input type="hidden" name="reservatorio" value="<?echo $comb[1]?>">
	<tr><td align="right"><b><I>Autorizado por: </td><td><select name="mil_autz">
	<?	//Seleciona quem autorizou
	$result = mysql_query("SELECT idt, post_grad, nome_guerra
			FROM usuario
			WHERE om_codom = '".$_POST['codom']."'
			and (perfil = 'FISCAL DA OM'
			or perfil = 'CONSULTOR')
			and situacao = 'PRONTO NA OM'");
			while ($fisc = mysql_fetch_array($result, MYSQL_NUM)) {?>
				<option value="<?echo $fisc[0];?>" > <? echo $fisc[1]." ".$fisc[2];?></option>
			<?}
			?> </select></td></tr>

	<tr><td align="right"><b><I>Data da Autorização: </td><td><input type="text" OnKeyUp="mascara(this.value);" size="10" maxlength="10" name="dt_abast"><I>"dd/mm/aaaa"</I></td></tr>
	<tr><td align="right"><b><I>Quantidade Autorizada: </I></td><td> <input type="text" size="5" maxlength="5" name="qtd_autz"> Lts</td></tr>
	<tr><td align="right"><b><I>Quantidade Abastecida: </I></td><td> <input type="text" size="5" maxlength="5" name="qtd_abast"> Lts</td></tr>
	<tr><td align="right"><b><I>Motorista: </I></td><td> <input type="text" size="30" maxlength="30" name="motorista"></td></tr>
	<tr><td align="right"><b><I>Odômetro Atual: </I></td><td> <input type="text" size="10" maxlength="10" name="odometro"></td></tr>
	<tr><td align="right"><b><I>Missão: </td><td><input type="text" size="30" maxlength="50" name="missao"></td></tr>
	<tr><td COLSPAN=2>&nbsp;</td></tr>
	<tr><td align="right"><input type="submit" value= "Confirmar">&nbsp;&nbsp;</td><td>&nbsp;&nbsp;<input type="reset" value= "  Cancelar  "></td></tr>	
	<tr><td COLSPAN=2><hr></td></tr>
</form>
</table>
<p>

