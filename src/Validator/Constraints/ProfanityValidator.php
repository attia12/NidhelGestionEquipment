<?php
namespace App\Validator\Constraints;

use App\Service\ProfanityFilterService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;


class ProfanityValidator extends ConstraintValidator
{
    private ProfanityFilterService $profanityFilterService;

    public function __construct(ProfanityFilterService $profanityFilterService)
    {
        $this->profanityFilterService = $profanityFilterService;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Profanity) {
            throw new UnexpectedTypeException($constraint, Profanity::class);
        }

        // Filter the text and check if it contains any bad words
        $filteredText = $this->profanityFilterService->filterText($value);
        if ($filteredText !== $value) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
