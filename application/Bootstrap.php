<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	private $request;
	/**
	 * configurazione parametri sito
	 */
	protected function _initConfig ()
	{
		//error_reporting(E_ERROR | E_WARNING | E_PARSE);
		$config = new Zend_Config($this->getOptions());
		Zend_Registry::set('config', $config->evolution);
		//carico le costanti del server
		return $config;
	}
	/**
	 * set language of application
	 */
	protected function _initLanguage ()
	{
		$t = new Zend_Translate_Adapter_Csv(
				array('content' => APPLICATION_PATH . '/language/en.csv',
					'locale' => 'en', 'delimiter' => '@'));
		$t->addTranslation(
			array('content' => APPLICATION_PATH . '/language/it.csv',
				'locale' => 'it', 'delimiter' => '@'
	 				,'disableNotices'=>true
	 		)
		);
		if ($_GET['locale']) {
	 		setcookie('locale',$_GET['locale'],time()+604800,'/');$_COOKIE['locale']=$_GET['locale'];
	 	}
		Zend_Registry::set('langnotsup', false);
		try {
	 		if (($_COOKIE['locale']=='browser')||!$_COOKIE['locale'])
	 			$t->setLocale("browser");
	 		elseif (in_array($_COOKIE['locale'], $t->getList())) $t->setLocale($_COOKIE['locale']);
	 		else {
	 			$t->setLocale("en");
	 			Zend_Registry::set('langnotsup', true);
	 		}
	 	}
	 	catch (Zend_Translate_Exception $e)
	 	{
	 		$t->setLocale("en");
	 	}
		Zend_Validate_Abstract::setDefaultTranslator($t);
		Zend_Form::setDefaultTranslator($t);
		Zend_Registry::set('translate', $t);
		return $t;
	}
	/**
	 * caricamento modelli, form, plugin
	*/
	protected function _initAutoload()
	{
		// Add autoloader empty namespace
		$autoLoader = Zend_Loader_Autoloader::getInstance();
		$resourceLoader = new Zend_Loader_Autoloader_Resource(
				array('basePath' => APPLICATION_PATH, 'namespace' => '',
						'resourceTypes' => array(
								'form' => array('path' => 'forms/', 'namespace' => 'Form_'),
								'model' => array('path' => 'models/', 'namespace' => 'Model_'),
								'plugin' => array('path' => 'plugin/', 'namespace' => 'Plugin_'))));
		// viene restituto l'oggetto per essere utilizzato e memorizzato nel bootstrap
		return $autoLoader;
	}
	/**
	 * inizializza l'autenticazione
	 */
	protected function _initAuth ()
	{
		$this->bootstrap("db");
		$this->bootstrap("Autoload");
		require_once 'application/models/Session.php';
		$db = $this->getPluginResource('db')->getDbAdapter();
		$adp = new Zend_Auth_Adapter_DbTable($db);
		$adp->setTableName(USERS_TABLE)
		->setIdentityColumn('username')
		->setCredentialColumn('password')
		->setCredentialTreatment('sha1(?)');
		$storage = new Model_Sessions(false, $db);
		$auth = Zend_Auth::getInstance();
		$auth->setStorage($storage);
		//$this->bootstrap('log');$log=$this->getResource('log');
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$user = intval($identity->user_id);
		} else
			$user = 1;
		$user = new Model_user($user);
	}
	/**
	* init log
	*/
	protected function _initLog () {
		$this->bootstrap('db');
		$this->bootstrap("Controller");
		$this->bootstrap("Auth");
		$this->bootstrap('Autoload');
		//$this->bootstrap('view');
		$this->bootstrap('layout');
		$this->bootstrap('config');
		$acl = Zend_Registry::get("acl");
		$db = $this->getPluginResource('db')->getDbAdapter();
		$log = new Zend_Log();
		$web=new Plugin_Logweb();
		$formatter = new Zend_Log_Formatter_Xml();
		$file = new Zend_Log_Writer_Stream(APPLICATION_PATH . "/log/log" . date("Ymd") . ".txt");
		if ($this->getResource('config')->evolution->debug) {
			$file2 = new Zend_Log_Writer_Stream(APPLICATION_PATH . "/log/debug" . date("Ymd") . ".txt");
			$file2->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG,"=="));
			$file2->setFormatter($formatter);
			$log->addWriter($file2);
		}
		$file->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG,"!="));
		include_once 'application/models/role.php';
		$role = Model_Role::getRole();
		$file->setFormatter($formatter);
		if ((APPLICATION_ENV != "production") || ($acl->isAllowed($role, "debug"))) {
			$log->addWriter($web);
			//profilazione query
		} 
		$log->addWriter($file);
		Zend_Registry::set('log', $log);
		$view=new Zend_View();
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
				'ViewRenderer');
		$view->log=$log;
		$viewRenderer->setView($view);
		//delete olg log
		@unlink(APPLICATION_PATH.'/log/debug'.date('Ymd',strtotime('-1 day')));
		return $log;
	}
	/**
	 * applica i plugin acl
	 *
	 */
	protected function _initController ()
	{
		require_once APPLICATION_PATH.'/plugin/acl_controller.php';
		require_once APPLICATION_PATH.'/plugin/myTmpEng.php';
		$acl = null;
		include_once (APPLICATION_PATH . "/models/acl.php");
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new plugin_acl_controller($acl))->registerPlugin(new plugin_myTmpEng(Zend_Controller_Action_HelperBroker::getStaticHelper(
				'ViewRenderer')));
		Zend_Registry::set("acl", $acl);
	}
	/**
	 * inizializza helper
	 */
	protected function _initView()
	{
		// Initialize view
		$view = new Zend_View();
		include_once APPLICATION_PATH . "/views/helpers/Image.php";
		include_once APPLICATION_PATH . "/views/helpers/Template.php";
		include_once APPLICATION_PATH.'/plugin/Tmpeng.php';
		include_once APPLICATION_PATH . "/views/helpers/MyMenu.php";
		$view->addFilter('Tmpeng')->addFilterPath(APPLICATION_PATH.'/plugin');
		$img = new Zend_View_Helper_image();
		$tmp = new Zend_View_Helper_template();
		$mymenu=new Zend_View_Helper_MyMenu();
		$view->registerHelper($img, "image");
		$view->registerHelper($tmp, "template");
		$view->registerHelper($mymenu, "MyMenu");
		$this->bootstrap("log")->bootstrap("Language")->bootstrap("costomlayout");
		$layout=$this->getResource("costomlayout");

		$view->t = $this->getResource("Language");
		$view->log = $this->getResource("log");
		// Add it to the ViewRenderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
				'ViewRenderer');
		$layout->setView($view);
		$viewRenderer->setView($view);
		// Return it, so that it can be stored by the bootstrap
		return $view;
	}
	/**
	 * inizializza il server di gioco
	 */
	protected function _initserver ()
	{
		global $server;
		$this->bootstrap("db");
		$this->bootstrap("View");
		$module = "default";
		foreach ($server as $value) {
			if (strpos($_SERVER['REQUEST_URI'], $value)) {
				$module = $value;
			}
		}
		if (strpos($_SERVER['REQUEST_URI'], 'admin')) $module='admin';

		if (($_COOKIE['server']) && ($module == "admin")) {
			require_once "includes/" . $_COOKIE['server'] . "_constants.php";
			setcookie("server", $_COOKIE['server'], mktime() + 3600, "/");
			$param = new Model_params();
			Zend_Registry::set("param", $param);
		}
		$this->bootstrap('log');
		if (($module != "default")&& ($module != "admin")) {
			require_once "includes/" . $module . "_constants.php";
			Zend_Registry::set("server", $module);
			$event = new Model_event();

			//carichiamo le informazioni sulla civiltà
			$user = Zend_Registry::get("user");
			$cid = $user->getCiv($module);
			$civ = new Model_civilta($cid, $user->option, $event);
			Zend_Registry::set("civ", $civ);
			Zend_Registry::set("age", $civ->getAge());
			//parametri del server
			$param = new Model_params();
			//controllo stato E.P
			if (($param->get("epla")<(time()-30))&&!$_GET['ep']) {
				$this->getResource("log")->emerg("Last E.P.'s attivity is ".date("H:i:s d/m/Y",$param->get("epla")));
				$param->set("epon", 0);
				sleep(1);
			}
			if (!$param->get("epon")&&!$_GET['ep']) {
				/*$this->getResource("log")->emerg("restart event processor!");
				 $client = new Zend_Http_Client();
				if (APPLICATION_ENV!="production") $uri=Zend_Uri_Http::factory("http://localhost/evolution/$module/processing?ep=1");
				else $uri=Zend_Uri_Http::factory("http://ageofevolution.altervista.org/$module/processing?ep=1");
				$client->setUri($uri);
				try{
				$content=$client->request()->getBody();
				$this->getResource("log")->debug($content);
				}
				catch (Exception $e) {
				$this->getResource("log")->debug($e);
				}*/
				$epflag=true;
			}
			Zend_Registry::set("param", $param);
		}
	}
	
	/**
	 * inizializza il layout
	 */
	protected function _initcostomlayout ()
	{
		global $server;
		$module = "default";
		foreach ($server as $value) {
			if (strpos($_SERVER['REQUEST_URI'], $value)) {
				$module = $value;
			}
		}
		$this->bootstrap("layout");
		$layout = Zend_Layout::getMvcInstance();
		if ($_POST['nolayout']||$_GET['nolayout']) $layout->disableLayout();
		elseif (($_POST['ajax'])||($_GET['ajax'])) $layout->setLayout("ajax");
		elseif ($module!='default') $layout->setLayout("game");
		return $layout;
	}
	
	
}

