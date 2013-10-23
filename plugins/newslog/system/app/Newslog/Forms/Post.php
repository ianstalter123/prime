<?php
/**
 * Post
 * @author: iamne <eugene@seotoaster.com> Seotoaster core team
 * Date: 7/19/12
 * Time: 4:17 PM
 */
class Newslog_Forms_Post extends Zend_Form {

    public function init() {

        $this->setAttribs(array(
            'id'     => 'newslog-frm-newpost',
            'method' => Zend_Form::METHOD_POST,
            'action' => 'javascript:;'
        ));

        $this->setDecorators(array(
            'Form',
            'FormElements'
        ));

        $this->addElement(new Zend_Form_Element_Text(array(
            'name'        => 'h1',
            'label'       => 'News page h1 tag',
            'required'    => true,
            'placeholder' => 'News page h1 tag',
            'data-destination' => 'property',
            'data-meta' => true
        )));

        $this->addElement(new Zend_Form_Element_Text(array(
            'name'        => 'title',
            'label'       => 'News page title',
            'required'    => true,
            'placeholder' => 'Display in browser title as',
            'data-destination' => 'property'
        )));

        $this->addElement(new Zend_Form_Element_Text(array(
            'name'        => 'navName',
            'id'          => 'nav-name',
            'label'       => 'News page navigation',
            'required'    => true,
            'placeholder' => 'Display in navigation',
            'data-destination' => 'property',
            'data-meta' => true
        )));

        $this->addElement(new Zend_Form_Element_Text(array(
            'name'        => 'url',
            'label'       => 'News page url',
            'required'    => true,
            'placeholder' => 'News page url',
            'data-destination' => 'property',
            'data-meta' => true
        )));

        $this->addElement(new Zend_Form_Element_Textarea(array(
            'name'        => 'teaserText',
            'id'          => 'teaser-text',
            'label'       => 'News page intro text',
            'placeholder' => 'Teaser text / Meta description',
            'data-destination' => 'property'
        )));

        $this->addElement(new Zend_Form_Element_Textarea(array(
            'name'        => 'metaKeywords',
            'id'          => 'meta-keywords',
            'label'       => 'News page meta keywords',
            'placeholder' => 'News page meta keywords',
            'data-destination' => 'property',
            'data-meta' => true
        )));

        $this->setElementDecorators(array(
            'ViewHelper',
            'Label',
            array('HtmlTag', array('tag' => 'div'))
        ));

    }

}
