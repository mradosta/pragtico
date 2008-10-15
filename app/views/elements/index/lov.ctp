<?php

/**
* Seteo las opcion para el caso que se la grilla se comporte como una lov.
*/

//if($this->layout == "lov" && !empty($retornarA) && !empty($camposRetorno)) {
if(!empty($retornarA) && !empty($camposRetorno)) {
	
	//$seteos[] = $formulario->input("Formulario.layout", array("type"=>"hidden", "id"=>"layout", "value"=>$this->layout));
	$seteos[] = $formulario->input("Formulario.retornarA", array("type"=>"hidden", "id"=>"retornarA", "value"=>$retornarA));
	$seteos[] = $formulario->input("Formulario.separadorRetorno", array("type"=>"hidden", "id"=>"separadorRetorno", "value"=>$separadorRetorno));
	$seteos[] = $formulario->input("Formulario.camposRetorno", array("type"=>"hidden", "id"=>"camposRetorno", "value"=>$camposRetorno));

	echo $formulario->bloque($seteos, array("div"=>array("id"=>"botones", "class"=>"botones")));
}

?>