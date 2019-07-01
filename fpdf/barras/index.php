<?php
require_once('barcode.inc.php'); 
$code_number = "103487512458";
#new barCodeGenrator($code_number,0,'hello.gif'); 
#new barCodeGenrator($code_number,0,'hello.gif', 190, 130, true);
new barCodeGenrator($code_number,0,'hello.gif', 200, 100, true);
?> 