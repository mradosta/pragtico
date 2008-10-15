<?php

	if(empty($this->viewVars['archivo']['nombre'])) {
		$this->viewVars['archivo']['nombre'] = "archivo.txt";
	}
	//d($_SERVER['HTTP_USER_AGENT']);
	define('PMA_USR_BROWSER_AGENT', 'Gecko');
	$mime_type = (PMA_USR_BROWSER_AGENT == 'IE' || PMA_USR_BROWSER_AGENT == 'OPERA')
	? 'application/octetstream'
	: 'application/octet-stream';
	header('Content-Type: ' . $mime_type);
	header('Content-Disposition: inline; filename="' . $this->viewVars['archivo']['nombre'] . '"');
	//header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	if (PMA_USR_BROWSER_AGENT == 'IE')
	{
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}
	else {
		header('Pragma: no-cache');
	}
	//echo $this->viewVars[archivo['contenido'];
	echo $content_for_layout;	
?>