<?php
/**
 * Authors form
 *
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/17/12
 * Time: 3:39 PM
 */
class Newslog_Forms_Authors extends Zend_Form {

    public function init() {

        $this->setMethod(Zend_Form::METHOD_POST)
            ->setDecorators(array(
                    'FormElements',
                    'Form'
                )
            );

        $this->addElement(new Zend_Form_Element_Text(array(
            'name'     => 'gplusProfile',
            'label'    => 'Default Google+ profile',
            'filters'  => array('StringTrim')
        )));

        $this->addElement(new Zend_Form_Element_Submit(array(
            'name'   => 'saveProfile',
            'label'  => 'Save profile',
            'class'  => 'grid_3',
            'ignore' => 'true'
        )));

        $this->setElementDecorators(array('ViewHelper'));
    }

}
