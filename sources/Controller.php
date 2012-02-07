<?php

class XYZ_Controller extends Zend_Controller_Action
{
    public function addAction(SomeInterface $xyz)
    {
        $json = array(12, 13);
        $json[3] = 'somevar';
        $json2 = (object)array(
            'uncle'=> array('bob', 'joe'),
            'names'=> array('first'=> 'walter'),
        );

        $something = new Myclass();
        $form = new MyForm;

        $isPost = $this->getRequest()->isPost();
        if ($isPost && $form->isValid($this->getAllParams())) {
            $something->setValues($form->getValues())->save();
        }

        foreach ($json2 as $key => $value) {
            echo $key;
            $this->$key = $value;
        }

        if (ctype_alnum($json[0])) {
            dosomething();
        }

        elseif (false) {
            echo MY_CONSTANT;
            echo PHP_EOL;
        }

    }

    /**
     * some other comment 
     */
    public function someOtherFunction()
    {
        donothing();
        if (ctype_alnum($json[0])) {
            dosomething();
            
        }

    }

}

