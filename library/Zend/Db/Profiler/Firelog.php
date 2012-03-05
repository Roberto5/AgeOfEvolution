<?php

/** Zend_Db_Profiler */
require_once 'Zend/Db/Profiler.php';

/** firelog library */
require_once 'includes/firelogger.php';

class Zend_Db_Profiler_Firelog extends Zend_Db_Profiler
{
    /**
     * log writer
     * @var FireLogger
     */
    protected $_flog = null;

    /**
     * The label template for this profiler
     * @var string
     */
    protected $_label_template = '%label% (%totalCount% @ %totalDuration% sec)';

    /**
     * The message envelope holding the profiling summary
     * @var array
     */
    protected $_message = array();

    /**
     * The total time taken for all profiled queries.
     * @var float
     */
    protected $_totalElapsedTime = 0;

    /**
     * Constructor
     *
     * @param string $label OPTIONAL Label for the profiling info.
     * @return void
     */
    public function __construct($label = null)
    {
    	if (!$label) $label="profiler";
        $this->_flog=new FireLogger($label);
    }

    /**
     * Enable or disable the profiler.  If $enable is false, the profiler
     * is disabled and will not log any queries sent to it.
     *
     * @param  boolean $enable
     * @return Zend_Db_Profiler Provides a fluent interface
     */
    public function setEnabled($enable)
    {
        parent::setEnabled($enable);
        return $this;
    }

    /**
     * Intercept the query end and log the profiling data.
     *
     * @param  integer $queryId
     * @throws Zend_Db_Profiler_Exception
     * @return void
     */
    public function queryEnd($queryId)
    {
        $state = parent::queryEnd($queryId);

        if (!$this->getEnabled() || $state == self::IGNORED) {
            return;
        }
        $profile = $this->getQueryProfile($queryId);

        $this->_totalElapsedTime += $profile->getElapsedSecs();

        $this->_flog->log('debug',array('time'=>(string)round($profile->getElapsedSecs(),5),
        		'query'=>$profile->getQuery(),
        		'params'=>($params=$profile->getQueryParams())?$params:null));

    }
}
