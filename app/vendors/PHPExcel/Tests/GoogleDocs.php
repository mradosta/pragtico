<?php
/** PHPExcel */
require_once 'PHPExcel.php';

/** PHPExcel_IWriter */
require_once 'PHPExcel/Writer/IWriter.php';

/** PHPExcel_Writer_Excel5 */
require_once 'PHPExcel/Writer/Excel5.php';

/** Zend_Gdata */
require_once 'Zend/Gdata.php';

/** Zend_Gdata_ClientLogin */
require_once 'Zend/Gdata/ClientLogin.php';

/** Zend_Gdata_App_Util */
require_once 'Zend/Gdata/App/Util.php';

/** Zend_Gdata_Docs */
require_once 'Zend/Gdata/Docs.php';

/**
 * PHPExcel_Writer_GoogleDocs
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_GoogleDocs
 */
class PHPExcel_Writer_GoogleDocs extends PHPExcel_Writer_Excel5 implements PHPExcel_Writer_IWriter {
	/**
	* Username
	*
	* @var string
	*/
	private $_username;

	/**
	* Password
	*
	* @var string
	*/
	private $_password;
	
	/**
	 * Create a new PHPExcel_Writer_GoogleDocs
	 *
	 * @param	PHPExcel	$phpExcel	PHPExcel object
	 */
	public function __construct(PHPExcel $phpExcel) {
		parent::__construct($phpExcel);
	}

	/**
	 * Save PHPExcel to file
	 *
	 * @param	string		$pFileName
	 * @throws	Exception
	 */
	public function save($pFilename = null) {
		parent::save($pFilename);
		
		$googleDocsClient = Zend_Gdata_ClientLogin::getHttpClient($this->_username, $this->_password, Zend_Gdata_Docs::AUTH_SERVICE_NAME);
		$googleDocsService = new Zend_Gdata_Docs($googleDocsClient);
		$googleDocsService->uploadFile($pFilename, basename($pFilename), null, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI);

		@unlink($pFilename);
	}
	
	/**
	* Set credentials
	*
	* @param string $username Username
	* @param string $password Password
	*/
	public function setCredentials($username, $password) {
		$this->_username = $username;
		$this->_password = $password;
	}
}
