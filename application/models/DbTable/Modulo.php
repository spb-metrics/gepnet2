<?php

/**
 * Automatically generated data model
 *
 * This class has been automatically generated based on the dbTable "" @ 16-05-2013
 * 17:22
 */
class Default_Model_DbTable_Modulo extends Zend_Db_Table_Abstract
{

    protected $_name    = 'tb_modulo';
    protected $_primary = array('idmodulo');
    protected $_dependentTables = array();
    protected $_referenceMap = array('Modulo' => array(
            'refTableClass' => 'tb_modulo',
            'columns'       => 'idmodulopai',
            'refColumns'    => 'idmodulo'
        ));

}

