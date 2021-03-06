<?php

/**
 * Automatically generated data model
 *
 * This class has been automatically generated16-05-2013 17:22
 */
class Default_Form_Tipodocumento extends App_Form_FormAbstract
{

    public function init()
    {
        $mapperTbPessoa = new Default_Model_Mapper_TbPessoa();
                            $this
                                ->setOptions(array(
                                    "method"   => "post",
                                    "elements" => array(
                                        'idtipodocumento' => array('hidden', array()),
        'nomtipodocumento' => array('text', array(
                'label'        => '',
                'required'     => false,
                'filters'      => array('StringTrim','StripTags'),
                'validators'   => array('NotEmpty',array('StringLength', false, array(0, 30))),
                'attribs'      => array(),
            )),
        'idcadastrador' => array('select', array(
                'label'        => '',
                'required'     => true,
                'multiOptions' => $mapperTbPessoa->fetchPairs(), 
                'filters'      => array('StringTrim','StripTags'),
                'validators'   => array(),
                'attribs'      => array(),
            )),
        'datcadastro' => array('text', array(
                'label'        => '',
                'required'     => true,
                'filters'      => array('StringTrim','StripTags'),
                'validators'   => array(array('StringLength', false, array(0, 4))),
                'attribs'      => array(),
            )),
        'flaativo' => array('text', array(
                'label'        => '',
                'required'     => false,
                'filters'      => array('StringTrim','StripTags'),
                'validators'   => array('NotEmpty',array('StringLength', false, array(0, 1))),
                'attribs'      => array(),
            )),
        
                                    )
                                ));
    }


}

