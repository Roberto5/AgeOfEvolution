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
     * impostazione del traduttore
     */
    protected function _initLanguage ()
    {
        $t = new Zend_Translate_Adapter_Csv(
        array('content' => APPLICATION_PATH . '/language/en.csv', 
        'locale' => 'en', 'delimiter' => '@'));
        $t->addTranslation(
        array('content' => APPLICATION_PATH . '/language/it.csv', 
        'locale' => 'it', 'delimiter' => '@'));
        try {
        $t->setLocale("browser");
        }
        catch (Exception $e)
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
    protected function _initAutoload ()
    {
        // Add autoloader empty namespace
        $autoLoader = Zend_Loader_Autoloader::getInstance();
        $resourceLoader = new Zend_Loader_Autoloader_Resource(
        array('basePath' => APPLICATION_PATH, 'namespace' => '', 
        'resourceTypes' => array(
        'form' => array('path' => 'forms/', 'namespace' => 'Form_'), 
        'model' => array('path' => 'models/DbTable/', 'namespace' => 'Model_'), 
        'plugin' => array('path' => 'plugin/', 'namespace' => 'plugin_'))));
        // viene restituto l'oggetto per essere utilizzato e memorizzato nel bootstrap
        return $autoLoader;
    }
    /**
     * inizializza l'autenticazione
     */
    protected function _initAuth ()
    {
        $this->bootstrap("db");
        
        $db = $this->getPluginResource('db')->getDbAdapter();
        $adp = new Zend_Auth_Adapter_DbTable($db);
        $adp->setTableName(USERS_TABLE)
            ->setIdentityColumn('username')
            ->setCredentialColumn('user_pass')
            ->setCredentialTreatment('md5(?)');
        require_once APPLICATION_PATH . '/models/session.php';
        
        $storage = new Sessions(false, $db);
        $auth = Zend_Auth::getInstance();
        $auth->setStorage($storage);
        $this->bootstrap("log");
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $user = $identity->user_id;
        } else
            $user = 1;
        $user = new Model_user($user);
        Zend_Registry::set("user", $user);
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
     * applica i plugin acl
     * 
     */
    protected function _initController ()
    {
        require_once 'application/plugin/acl_controller.php';
        $acl = null;
        include_once (APPLICATION_PATH . "/models/acl.php");
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new plugin_acl_controller($acl));
        Zend_Registry::set("acl", $acl);
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
    /**
     * init log
     */
    protected function _initLog ()
    {
        $this->bootstrap('db');
        $this->bootstrap("Controller");
        //$this->bootstrap("Auth");
        $acl = Zend_Registry::get("acl");
        $db = $this->getPluginResource('db')->getDbAdapter();
        $log = new Zend_Log();
        $firebug = new Zend_Log_Writer_Firelog();
        $file = new Zend_Log_Writer_Stream(
        APPLICATION_PATH . "/log/log" . date("Ymd") . ".txt");
        $file->addFilter(new Zend_Log_Filter_Priority(Zend_Log::DEBUG,"!="));
        $role = Model_role::getRole();
        $formatter = new Zend_Log_Formatter_Xml();
		$file->setFormatter($formatter);
        if ((APPLICATION_ENV != "production") || ($acl->isAllowed($role, "debug"))) {
            $log->addWriter($firebug);
            /*/profilazione query
            $profiler = new Zend_Db_Profiler_Firelog(
            'All DB Queries');
            $profiler->setEnabled(true);
            $db->setProfiler($profiler);//*/
        } /*else {
            $filter = new Zend_Log_Filter_Priority(Zend_Log::INFO);
            $log->addFilter($filter);
        }*/
        $log->addWriter($file);
        $log->addPriority("movement", 8);
        $log->addPriority("build", 9);
        $log->addPriority("market", 10);
        $log->addPriority("train", 11);
        $log->addPriority("startMovement", 12);
        Zend_Registry::set('log', $log);
        return $log;
    }
    /**
     * inizializza helper
     */
    protected function _initView ()
    {
        // Initialize view
        $view = new Zend_View();
        //$view->addHelperPath(APPLICATION_PATH."/views/helpers/","Zend_View_Helper");
        include_once APPLICATION_PATH . "/views/helpers/image.php";
        include_once APPLICATION_PATH . "/views/helpers/template.php";
        include_once APPLICATION_PATH . "/views/helpers/MyMenu.php";
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
}

