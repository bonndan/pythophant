<?php /** generated by PythoPhant on 2012/03/05 23:06:51 from sources/Controller.pp #a862317d27ce51057ff2e411809ba9e1 */

class XYZ_Controller extends Zend_Controller_Action
{
    const MY_CLASS_CONST = 'const';

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
        if ($isPost and $form->isValid($this->getAllParams())) {
            $something->setValues($form->getValues())->save();
        }

        foreach ($json2 as $key => $value) {
            echo $key;
            $this->$key = $value;
        }

        if (ctype_alnum($something->getInt())) {
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
        donothing(self::MY_CLASS_CONST);
        if (! ctype_alnum($this->json[0])) {
            dosomething();
        }

        elseif (is_file('my.file')) {
            unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'my.file');
            $myString = "a string " . "b string" . $myVar;
            
        }

    }

}

