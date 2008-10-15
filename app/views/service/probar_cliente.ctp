<?php

echo $formulario->input("Prueba.wsdl", array("style"=>"width:100%; height:400px;", "type"=>"textarea", "value"=>$pruebas['wsdl']));
echo $formulario->bloque("", array("div"=>array("class"=>"clear")));
echo $formulario->input("Prueba.retorno", array("style"=>"width:100%; height:400px;", "type"=>"textarea", "value"=>$pruebas['retorno']));
echo $formulario->bloque("", array("div"=>array("class"=>"clear")));













?>