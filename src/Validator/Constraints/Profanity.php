<?php
namespace App\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Profanity extends Constraint
{
    public  string $message = 'The text contains inappropriate language.';
}