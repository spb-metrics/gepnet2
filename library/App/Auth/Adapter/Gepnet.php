<?php

/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

class App_Auth_Adapter_Gepnet implements Zend_Auth_Adapter_Interface
{

    protected $_params = array();

    /**
     *
     * @var Zend_Db_Adapter_Abstract 
     */
    protected $_zendDb = null;

    /**
     * $_identity - Identity value
     *
     * @var string
     */
    protected $_identity = null;

    /**
     * $_authenticateResultInfo
     *
     * @var array
     */
    protected $_authenticateResultInfo = null;

    /**
     * $_resultRow - Results of database authentication query
     *
     * @var array
     */
    protected $_resultRow = null;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($db = null, $params)
    {
        $this->_setDbAdapter($db);
        $this->_params = $params;
        $this->setIdentity($params['token']);
        //s$this->_phpbbRoot = $phpbbRootPath;
    }

    /**
     * _setDbAdapter() - set the database adapter to be used for quering
     *
     * @param Zend_Db_Adapter_Abstract
     * @throws Zend_Auth_Adapter_Exception
     * @return Zend_Auth_Adapter_DbTable
     */
    protected function _setDbAdapter(Zend_Db_Adapter_Abstract $zendDb = null)
    {
        $this->_zendDb = $zendDb;

        /**
         * If no adapter is specified, fetch default database adapter.
         */
        if ( null === $this->_zendDb ) {
            require_once 'Zend/Db/Table/Abstract.php';
            $this->_zendDb = Zend_Db_Table_Abstract::getDefaultAdapter();
            if ( null === $this->_zendDb ) {
                require_once 'Zend/Auth/Adapter/Exception.php';
                throw new Zend_Auth_Adapter_Exception('No database adapter present');
            }
        }

        return $this;
    }

    /**
     * Method responsável por validar o token no banco oracle
     * @param type $token
     * @return type
     */
    protected function validaToken($token)
    {
        /************************/
        //GRSF
        $validaDesenv = array();
        $validaDesenv[0] = array('ZEND_AUTH_CREDENTIAL_MATCH' => 1);
        
        return $validaDesenv;
        /************************/
        
       
        //$resource = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getPluginResource('multidb');
        $resource =  $this->_zendDb = Zend_Db_Table_Abstract::getDefaultAdapter();;
        //$db = $resource->getDb('EGPE_MIGRACAO');
        $resultadoUsuario = $this->getUsuario();

        if ( count($resultadoUsuario) <= 0 ) {
            return $resultadoUsuario;
        }

        $sql = "SELECT 1 AS ZEND_AUTH_CREDENTIAL_MATCH
                FROM agepnet200.tb_pessoa t
                WHERE t.token = :token";
        $resultado = $resource->fetchAll($sql, array('token' => $token));

        return $resultado;
    }

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot
     *                                     be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        /*******************/
        //GRSF
        //$this->_params['cpf'] = 61992704104;
        //$this->_params['id'] = 30601;
        //$this->_params['nome'] = '"WENDELL LUIZ FERREIRA LINO"';
        /*******************/
        
        $this->_authenticateSetup();
        $token = $this->_params['token'];
        $resultIdentities = $this->validaToken($token);
        $authResult = $this->_authenticateValidateResultset($resultIdentities);

        if ( $authResult instanceof Zend_Auth_Result ) {
            return $authResult;
        }
        
        $authResult = $this->_authenticateValidateResult(array_shift($resultIdentities));
        
        return $authResult;
    }

    protected function _authenticateSetup()
    {
        $exception = null;
        //Zend_Debug::dump($this->_params); exit;

        if ( $this->_params['token'] == '' ) {
            $exception = 'Token inválido';
        }

        if ( null !== $exception ) {
            /**
             * @see Zfb_Auth_Adapter_Exception
             */
            require_once 'App/Auth/Adapter/Exception.php';
            throw new App_Auth_Adapter_Exception($exception);
        }

        $this->_authenticateResultInfo = array(
            'code'     => Zend_Auth_Result::FAILURE,
            'identity' => $this->_identity,
            'messages' => array()
        );

        return true;
    }

    /**
     * getResultRowObject() - Returns the result row as a stdClass object
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return stdClass|boolean
     */
    public function getResultRowObject($returnColumns = null, $omitColumns = null)
    {
        if ( !$this->_resultRow ) {
            return false;
        }

        $returnObject = new stdClass();

        if ( null !== $returnColumns ) {

            $availableColumns = array_keys($this->_resultRow);
            foreach ( (array) $returnColumns as $returnColumn )
            {
                if ( in_array($returnColumn, $availableColumns) ) {
                    $returnObject->{$returnColumn} = $this->_resultRow[$returnColumn];
                }
            }
            return $returnObject;
        } elseif ( null !== $omitColumns ) {

            $omitColumns = (array) $omitColumns;
            foreach ( $this->_resultRow as $resultColumn => $resultValue )
            {
                if ( !in_array($resultColumn, $omitColumns) ) {
                    $returnObject->{$resultColumn} = $resultValue;
                }
            }
            return $returnObject;
        } else {

            foreach ( $this->_resultRow as $resultColumn => $resultValue )
            {
                $returnObject->{$resultColumn} = $resultValue;
            }
            return $returnObject;
        }
    }

    /**
     * _authenticateValidateResultSet() - This method attempts to make certian that only one
     * record was returned in the result set
     *
     * @param array $resultIdentities
     * @return true|Zend_Auth_Result
     */
    protected function _authenticateValidateResultSet(array $resultIdentities)
    {
        if ( count($resultIdentities) < 1 ) {
            $this->_authenticateResultInfo['code']       = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $this->_authenticateResultInfo['messages'][] = 'A record with the supplied identity could not be found.';
            return $this->_authenticateCreateAuthResult();
        } elseif ( count($resultIdentities) > 1 ) {
            $this->_authenticateResultInfo['code']       = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticateResultInfo['messages'][] = 'More than one record matches the supplied identity.';
            return $this->_authenticateCreateAuthResult();
        }

        return true;
    }

    /**
     * _authenticateValidateResult() - This method attempts to validate that the record in the
     * result set is indeed a record that matched the identity provided to this adapter.
     *
     * @param array $resultIdentity
     * @return Zend_Auth_Result
     */
    protected function _authenticateValidateResult($resultIdentity)
    {
        if ( $resultIdentity['ZEND_AUTH_CREDENTIAL_MATCH'] != '1' ) {
            $this->_authenticateResultInfo['code']       = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $this->_authenticateResultInfo['messages'][] = 'Supplied credential is invalid.';
            return $this->_authenticateCreateAuthResult();
        }

        unset($resultIdentity['ZEND_AUTH_CREDENTIAL_MATCH']);
        $this->_resultRow = $this->_params;

        $this->_authenticateResultInfo['code']       = Zend_Auth_Result::SUCCESS;
        $this->_authenticateResultInfo['messages'][] = 'Authentication successful.';
        return $this->_authenticateCreateAuthResult();
    }

    /**
     * _authenticateCreateAuthResult() - This method creates a Zend_Auth_Result object
     * from the information that has been collected during the authenticate() attempt.
     *
     * @return Zend_Auth_Result
     */
    protected function _authenticateCreateAuthResult()
    {
        return new Zend_Auth_Result(
            $this->_authenticateResultInfo['code'], $this->_authenticateResultInfo['identity'], $this->_authenticateResultInfo['messages']
        );
    }

    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param  string $value
     * @return Zfb_Auth_Adapter_Doctrine Provides a fluent interface
     */
    public function setIdentity($value)
    {
        $this->_identity = $value;
        return $this;
    }

    public function getUsuario()
    {
        $sql = "select
                    idpessoa, nompessoa, numcpf, desobs,
                    numfone, numcelular, desemail, nummatricula,
                    id_unidade, flaagenda, domcargo
                from agepnet200.tb_pessoa pes
                where pes.desemail = :desemail";
        
        $resultado = $this->_zendDb->fetchRow($sql, array(
            'desemail' => $this->_params['desemail']
        ));
        return $resultado;
    }

}