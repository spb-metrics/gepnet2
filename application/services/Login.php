<?php

class Default_Service_Login extends App_Service_ServiceAbstract
{

    const LDAP_CONTA_ATIVA = 'A'; //phpgwaccountstatus
    const LDAP_CONTA_BLOQUEADA = 'B'; //phpgwaccountstatus
    const LDAP_SENHA_EXPIRADA = 0;//phpgwlastpasswdchange
    const LDAP_SENHA_ATIVA = 1;//phpgwlastpasswdchange
    
    /**
     * @var array 
     */
    public $errors = array();

    public function init()
    {
        
    }

    public function getFormPerfil()
    {
        return $this->_getForm('Default_Form_PerfilLogin');
    }
    
    // Funçao para apresentar o formulário na index
    public function getFormLogarUsuario()
    {
        return $this->_getForm('Default_Form_LogarUsuario');
    }
    /////////////////////////////////////////////////////////
    

    public function getFormMudarPerfil()
    {
        return $this->_getForm('Default_Form_PerfilLogin', array('submit'));
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function retornaUsuarioLogado()
    {
        return Zend_Auth::getInstance()->getIdentity();
    }

    public function autenticar($desemail,$token)
    {
        $translate = new Zend_Translate(array(
            'adapter' => 'csv',
            'content' => APPLICATION_PATH . '/data/auth.csv',
            'locale'  => 'pt'
        ));

         // Conectando ao banco
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
        // Campos de login
        $authAdapter->setTableName('agepnet200.tb_pessoa')
                ->setIdentityColumn('desemail')
                ->setCredentialColumn('token')
                ->setCredentialTreatment('MD5(?)');
        // Campo do formulario de login
         $authAdapter->setIdentity($desemail)
                ->setCredential($token);
        //Efetua o login
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        $auth        = Zend_Auth::getInstance();
        $result      = $auth->authenticate($authAdapter);
        // Se a autenticação estiver ok
        if ( $result->isValid() ) {
            // Retira o campo senha  e guarda o restante na sessao
            $data          = $authAdapter->getResultRowObject(null, array('token'));
            $servicePessoa = App_Service::getService('Default_Service_Pessoa');
            $servicePerfil = App_Service::getService('Default_Service_Perfil');
            /* @var $pessoa Default_Model_Pessoa */
            $pessoa        = $servicePessoa->getByEmail($data->desemail);
            $perfis        = $servicePerfil->retornaPorPessoa(array(
                'idpessoa' => $pessoa->idpessoa
            ));
            $data->idpessoa        = $pessoa->idpessoa;
            $data->desmail         = $pessoa->desemail;
            $data->desfuncao       = $pessoa->desfuncao;
            $data->domcargo        = $pessoa->domcargo;
            $data->flaagenda       = $pessoa->flaagenda;
            $data->nompessoa       = $pessoa->nompessoa;
            $data->sigla           = $pessoa->unidade->sigla;
            $data->perfis          = $perfis;
            $data->escritorioAtivo = null;
            $data->perfilAtivo     = null;

            $auth->getStorage()->write($data);
            
            return true;
        }else{
        
        $msn            = $result->getMessages();
        $message        = $translate->_($msn[0]);
        $this->errors[] = $message;
        
        return false;
        }
    }

    public function retornaPerfilAtivo()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            return Zend_Auth::getInstance()->getIdentity()->perfilAtivo;
        }
        return false;
    }

    public function selecionarPerfil($params)
    {
        $servicePerfil      = new Default_Service_Perfil();
        $usuario            = $this->retornaUsuarioLogado();
        $params['idpessoa'] = $usuario->idpessoa;
        $perfil             = $servicePerfil->retornaPorIdEPessoa($params);
        $auth              = Zend_Auth::getInstance();
        $data              = $auth->getStorage()->read();
        $p                 = new stdClass();
        $p->idperfil       = $perfil['idperfil'];
        $p->nomperfil      = $perfil['nomperfil'];
        $p->idescritorio   = $perfil['idescritorio'];
        $p->nomescritorio  = $perfil['nomescritorio'];
        $data->perfilAtivo = $p;
        $auth->getStorage()->write($data);
        return true;
    }

    public function retornaPerfisDoUsuarioLogado()
    {
        return Zend_Auth::getInstance()->getIdentity()->perfis;
    }

    public function logout()
    {
        return Zend_Auth::getInstance()->clearIdentity();
    }
    
}

?>
