<?php

/**
 * Automatically generated data model
 *
 * This class has been automatically generated based on the dbTable "" @ 16-05-2013
 * 17:21
 */
class Default_Model_DbTable_Ata extends Zend_Db_Table_Abstract
{

    protected $_name    = 'tb_ata';
    protected $_primary = array('idata');
    protected $_dependentTables = array();
    protected $_referenceMap = array('Pessoa' => array(
            'refTableClass' => 'tb_pessoa',
            'columns'       => 'idcadastrador',
            'refColumns'    => 'idpessoa'
        ));

}

