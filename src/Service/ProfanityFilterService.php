<?php


namespace App\Service;

use ConsoleTVs\Profanity\Builder;

class ProfanityFilterService
{
    public function filterText(string $text): string
    {
        
        return Builder::blocker($text)->filter();
    }
}
