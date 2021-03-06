<?php
namespace Codem\OneTime;

use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;

class NoValueTextField extends TextField implements NoValueFieldInteface
{
    protected $fieldHolderTemplate = "NoValueTextField_holder";

    /**
     * {@inheritdoc}
     */
    public function Type()
    {
        return 'text';
    }

    public function __construct($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        parent::__construct($name, $title, '', $maxLength, $form);
    }

    public function setCheckbox(CheckboxField $checkbox)
    {
        $this->checkbox = $checkbox;
        $this->checkbox->setFieldHolderTemplate('CheckboxField_holder_small');
    }

    public function Checkbox()
    {
        return $this->checkbox;
    }

    /**
     * We don't need no value
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value, $data = null)
    {
        $this->value = "";
        return $this;
    }

    /**
     * Does not return a value
     */
    public function getPartialValue($value, $filter = '')
    {
        return "";
    }

    public function supportsPartialValueDisplay() {
        return false;
    }
}
