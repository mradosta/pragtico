<?php

/**
* Seteo las opcion para el caso que se la grilla se comporte como una lov.
*/

//if($this->layout == "lov" && !empty($retornarA) && !empty($camposRetorno)) {
if(!empty($retornarA) && !empty($camposRetorno)) {
	
	//$seteos[] = $appForm->input("Formulario.layout", array("type"=>"hidden", "id"=>"layout", "value"=>$this->layout));
	$seteos[] = $appForm->input("Formulario.retornarA", array("type"=>"hidden", "id"=>"retornarA", "value"=>$retornarA));
	$seteos[] = $appForm->input("Formulario.separadorRetorno", array("type"=>"hidden", "id"=>"separadorRetorno", "value"=>$separadorRetorno));
	$seteos[] = $appForm->input("Formulario.camposRetorno", array("type"=>"hidden", "id"=>"camposRetorno", "value"=>$camposRetorno));

	echo $appForm->bloque($seteos, array("div"=>array("id"=>"botones", "class"=>"botones")));
}

?>