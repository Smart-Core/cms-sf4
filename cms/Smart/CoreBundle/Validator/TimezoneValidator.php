<?php

namespace Smart\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TimezoneValidator extends ConstraintValidator
{
    public function validate($timezone, Constraint $constraint)
    {
        if (!in_array($timezone, \DateTimeZone::listIdentifiers())) {
            $this->context->buildViolation($constraint->message)
                ->setCode('45e3467sdye5467ds4fkbwrfgybsfg')
                ->addViolation();
        }
    }
}
