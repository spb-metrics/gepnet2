<?php

/**
 * Automatically generated data model
 *
 * This class has been automatically generated based on the dbTable "" @ 16-05-2013
 * 17:22
 */
class Default_Model_DbTable_Statusreport extends Zend_Db_Table_Abstract
{

    protected $_name    = 'tb_statusreport';
    protected $_primary = array('idstatusreport');
    protected $_dependentTables = array();
    protected $_referenceMap = array(
        'Pessoa' => array(
            'refTableClass' => 'tb_pessoa',
            'columns'       => 'idcadastrador',
            'refColumns'    => 'idpessoa'
        ),
        'Marco'         => array(
            'refTableClass' => 'tb_marco',
            'columns'       => 'idmarco',
            'refColumns'    => 'idmarco'
        )
    );

}

