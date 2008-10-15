<?php
	//d("X");
	//d($dbError);
	$return = "";
	if(!empty($dbError['errorDescripcion'])) {
		$return .= "<h1><span class='color_rojo'>" . $dbError['errorDescripcion'] . "</span></h1>";
	}
	if(!empty($dbError['errorDescripcionAdicional'])) {
		$return .= "<h2>" . $dbError['errorDescripcionAdicional'] . "</h2>";
	}
	echo $return;
?>