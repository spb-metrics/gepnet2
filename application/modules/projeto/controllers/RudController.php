<?php
class Projeto_RudController extends Zend_Controller_Action {

    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('index', 'html')
            ->addActionContext('fileTree', 'json')
            ->addActionContext('add', 'json')
            ->addActionContext('addpasta', 'json')
            ->addActionContext('pesquisarjson', 'json')
            ->addActionContext('delete', 'json')
            ->initContext();
    }

    public function indexAction() {
        $service = App_Service_ServiceAbstract::getService('Projeto_Service_Rud');
        $serviceGerencia = App_Service_ServiceAbstract::getService('Projeto_Service_Gerencia');
        $form = $service->getForm();
        $formPasta = $service->getFormPasta();
        $projeto = $serviceGerencia->retornaProjetoPorId(array('idprojeto' => $this->_request->getParam('idprojeto')));
        $this->view->form = $form;
        $this->view->formPasta = $formPasta;
        $this->view->projeto = $projeto;
    }

    public function pesquisarjsonAction() {
        $service = App_Service_ServiceAbstract::getService('Projeto_Service_Rud');
        $paginator = $service->getPastas($this->_request->getParams());
        $this->_helper->json->sendJson($paginator);
    }

    public function fileTreeAction(){
        $service = App_Service_ServiceAbstract::getService('Projeto_Service_Rud');
        $service->fileTree(array('dir' => $this->_request->getParam('dir')));
    }

    public function addAction() {
        $service = App_Service_ServiceAbstract::getService('Projeto_Service_Rud');

        $success = false;
        if ($this->_request->isPost()) {
            $dados = $this->_request->getPost();
//            var_dump($dados);
            $projeto = $service->upload($dados);
//            exit;
            if ($projeto) {
                $success = true; ###### AUTENTICATION SUCCESS
                $msg = App_Service_ServiceAbstract::REGISTRO_CADASTRADO_COM_SUCESSO;
            } else {
                $msg = $service->getErrors();
            }
        }

        if ($this->_request->isPost()) {
            if ($this->_request->isXmlHttpRequest()) {
                $this->view->success = $success;
                $this->view->msg = $service->getNotify();
//                $this->view->msg = array(
//                    'text' => $msg,
//                    'type' => ($success) ? 'success' : 'error',
//                    'hide' => true,
//                    'closer' => true,
//                    'sticker' => false
//                );
            } else {
                if ($success) {
                    $this->_helper->_redirector->gotoSimpleAndExit('projeto', 'rud', 'default');
                }
                $this->_helper->_flashMessenger->addMessage(array('status' => 'error', 'message' => $msg));
            }
        }
    }

    public function addpastaAction() {
//        $this->_helper->layout->disableLayout();
//        $this->_helper->viewRenderer->setNoRender(true);

        $service = App_Service_ServiceAbstract::getService('Projeto_Service_Rud');

        $success = false;
        if ($this->_request->isPost()) {
            $dados = $this->_request->getPost();
//            var_dump($dados);
//            exit;
            $projeto = $service->criarPasta($dados);
            if ($projeto) {
                $success = true; ###### AUTENTICATION SUCCESS
                $msg = App_Service_ServiceAbstract::REGISTRO_CADASTRADO_COM_SUCESSO;
            } else {
                $msg = $service->getErrors();
            }
        }

        if ($this->_request->isPost()) {
            if ($this->_request->isXmlHttpRequest()) {
                $this->view->success = $success;
                $this->view->msg = $service->getNotify();
//                $this->view->msg = array(
//                    'text' => $msg,
//                    'type' => ($success) ? 'success' : 'error',
//                    'hide' => true,
//                    'closer' => true,
//                    'sticker' => false
//                );
            } else {
                if ($success) {
                    $this->_helper->_redirector->gotoSimpleAndExit('projeto', 'rud', 'default');
                }
                $this->_helper->_flashMessenger->addMessage(array('status' => 'error', 'message' => $msg));
            }
        }
    }

    public function deleteAction() {
        $service = App_Service_ServiceAbstract::getService('Projeto_Service_Rud');

        $success = false;
        if ($this->_request->isPost()) {
            $dados = $this->_request->getPost();
//            var_dump($dados);
//            exit;
            $projeto = $service->delete($dados);
            if ($projeto) {
                $success = true; ###### AUTENTICATION SUCCESS
                $msg = App_Service_ServiceAbstract::REGISTRO_EXCLUIDO_COM_SUCESSO;
            } else {
                $msg = $service->getErrors();
            }
        }

//        var_dump($projeto);
//        var_dump($msg);
//        exit;
        if ($this->_request->isPost()) {
            if ($this->_request->isXmlHttpRequest()) {
                $this->view->success = $success;
                $this->view->msg = $service->getNotify();
//                $this->view->msg = array(
//                    'text' => $msg,
//                    'type' => ($success) ? 'success' : 'error',
//                    'hide' => true,
//                    'closer' => true,
//                    'sticker' => false
//                );
            } else {
                if ($success) {
                    $this->_helper->_redirector->gotoSimpleAndExit('projeto', 'rud', 'default');
                }
                $this->_helper->_flashMessenger->addMessage(array('status' => 'error', 'message' => $msg));
            }
        }
    }

    public function downloadAction(){
        $dados = $this->_request->getParams();
//        print "<PRE>";
//        $project = strstr($dados['file'],'-',true);
//        var_dump($dados['file']);
//        var_dump($project);

        $dados['file'] = str_replace(":!","/",$dados['file']);
//        $dados['file'] = urldecode($dados['file']);

        $service = App_Service_ServiceAbstract::getService('Default_Service_Download');
        $file = $service->getDownloadConfigRud($dados);

//        var_dump($dados);
        $filename = str_replace(" ","-",basename($file->path));
//        var_dump(str_replace(" ","-",basename($file->path)));
//        var_dump(mime_content_type ($file->path)); exit;

        header('Content-Description: File Transfer');
        header('Content-Type: '.mime_content_type ($file->path));
        header('Content-Disposition: attachment; filename='.$filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file->path));
        ob_clean();
        flush();
        echo readfile($file->path);
        exit;

    }
}