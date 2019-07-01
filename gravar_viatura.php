<?php
	include "verifica_sessao.php";
	/*Conexão ao Banco de Dados*/
	include "conexao.php";
	/*Conexão com arquivo para registrar atividade*/
	include "atividade.php";
	/*Testa a operação se operacao for igual a 1 é insersão se não é alteração*/
	if ($_POST['operacao']==1) {
		$query = "insert into viatura (eb, combustivel_codigo, om_codom, marca, 
					modelo, disponivel, consumo, cap_tanque, situacao)
		values('".$_POST['eb']."','".$_POST['combustivel']."','".$_POST['codom']."',upper('".$_POST['marca']."'),
					upper('".$_POST['modelo']."'),'".$_POST['disponivel']."','".$_POST['consumo']."','".$_POST['capacidade']."','".$_POST['situacao']."')";
		$rc = mysql_query($query);
		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('CADASTROU VIATURA '.$_POST['eb']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Viatura cadastrada com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante o processamento do cadastro! <p>");
			
			if(mysql_errno()==1062) {
				$qryuser = "select om.sigla, v.* 
									from om, viatura v
									where om.codom=v.om_codom 
										and v.eb='".$_POST['eb']."'";
				$resuser = mysql_query($qryuser);
				$vtr = mysql_fetch_row($resuser);
				echo "<table border='0'>";
				echo "<tr><td align='right' colspan='2'>A viatura EB/Placa $vtr[1] já está cadastrada no Sistema.<p></td></tr>";
				echo "<tr><td align='right'><coluna>OM Detentora: </td><td>$vtr[0]</td></tr>";
				echo "<tr><td align='right'><coluna>Marca da Viatura: </td><td>$vtr[4]</td></tr>";
				echo "<tr><td align='right'><coluna>Modelo da Viatura: </td><td>$vtr[5]</td></tr>";
				echo "<tr><td align='right'><coluna>Situação: </td><td>$vtr[9]</td></tr>";
				echo "</table>";
				$_POST['codom']=$user[6];
			} else {
				echo mysql_error()."<p> Se o erro persistir entre em contato com o Administrador.";
			}
			
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU CADASTRAR VIATURA '.$_POST['eb']);
		}
	} else {
		$query = "update viatura set eb='".$_POST['eb']."', combustivel_codigo='".$_POST['combustivel']."', 
						marca=upper('".$_POST['marca']."'), om_codom='".$_POST['codom']."', modelo=upper('".$_POST['modelo']."'), 
						disponivel='".$_POST['disponivel']."', consumo='".$_POST['consumo']."', cap_tanque='".$_POST['capacidade']."', 
						situacao='".$_POST['situacao']."' 
					where eb='".$_POST['eb_ant']."'";
		$rc = mysql_query($query);

		if($rc)	{
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('ALTEROU O CADASTRO DA VIATURA '.$_POST['eb']);
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Viatura alterada com sucesso! ");
		}
		else{
			echo ("<br><IMG SRC='./imagens/atencao.jpg' width='100' height='100' border=0><br><br>ATENÇÃO! Erro durante a alteração da Viatura! ");
			/*Registra atividade executada pelo usuário para auditoria*/
			registrarAtividade('TENTOU ALTERAR O CADASTRO DA VIATURA'.$_POST['eb']);
		}
    }
    echo "<center><a href='index.php?nomeArquivo=cons_viatura.php&listar=".$_POST['codom']."'><br><u><b>VISUALIZAR</b></u></a></center>";

?>

