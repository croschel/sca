<?php
	include "verifica_sessao.php";
	//Conexão ao Banco de Dados
?>
<head>
	<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
	echo "<br><center><b><p></font><P> <IMG SRC='./imagens/interrogacao.jpeg'><p>";
	echo "<BLINK><font color='red' size='5'>ATENÇÃO USUÁRIO!</BLINK>";
	echo "</font>  <p> Você tem certeza que deseja remover  ".$_GET['registro']."? </b></center>";
	echo "<form method='POST' action='index.php?nomeArquivo=".$_GET['arquivo']."' name='om'>";
	/*Indica o nome da tabela*/
	echo "<input type='hidden' name='tab' value='".$_GET['tab']."'><br>";
	/*Indica o nome do atributo chave*/
	echo "<input type='hidden' name='ch' value='".$_GET['ch']."'>";
	/*Valor do Atributo Chave da tabela*/
	echo "<input type='hidden' name='valor' value='".$_GET['valor']."'>";
	/*Indica o nome do outro atributo chave quando existir*/
	echo "<input type='hidden' name='ch1' value='".$_GET['ch1']."'>";
	/*Indica o valor do outro atributo chave quando existir*/
	echo "<input type='hidden' name='valor1' value='".$_GET['valor1']."'>";
	/*Indica o nome do outro atributo chave quando existir*/
	echo "<input type='hidden' name='ch2' value='".$_GET['ch2']."'>";
	/*Indica o valor do outro atributo chave quando existir*/ 
	echo "<input type='hidden' name='valor2' value='".$_GET['valor2']."'>";
	/*Indica o valor do outro atributo chave quando existir*/ 
	echo "<input type='hidden' name='valor3' value='".$_GET['valor3']."'>";
	/*Indica o valor da ancora*/ 
	echo "<input type='hidden' name='ancora' value='".$_GET['ancora']."'>";
	/*Valor da descricao da atividade para gravar na auditoria*/
	echo "<input type='hidden' name='descricao' value='".$_GET['registro']."'>";
	echo "<input type='hidden' name='reserv' value='".$_GET['reserv']."'>";
	echo "<input type='hidden' name='qtd' value='".$_GET['qtd']."'>";
	echo "<input type='hidden' name='qtd_atual' value='".$_GET['qtd_atual']."'>";
	echo "<table>";
	echo "<tr><td align='right'><input type='submit' value= '     Sim     '></td><td><input type='button' value= '     Não     ' onclick='history.go(-1)'></td></tr>";
	echo "</table>";
	echo "</form>";

?>
