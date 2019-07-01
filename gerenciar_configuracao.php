<?php
$arquivo = fopen('configuracao.php','r');
if ($arquivo == false) die('Não foi possível criar o arquivo.');
while(!feof($arquivo)) {
	$linha = fgets($arquivo, 1000);
	if($i==19) {
		echo "<input type='checkbox' size='4' value='".substr($linha,18,1)."' name='qtd_atd' "; if(substr($linha,18,1)=='s') { echo "checked";} echo ">O Sitema permite abastecimento com saldo negativo <br />";
	}
	$i++;
}
fclose($arquivo);

?>