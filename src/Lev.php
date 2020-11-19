<?php

namespace Northbridg3\Levtowords;

class Lev
{
    private int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    public function toWords()
    {
        $stringAmount = (string) $this->amount;
        $chunks = array_reverse(array_map(fn ($x) => strrev($x), str_split(strrev($stringAmount), 3)));

        $levsWords = [];
        $thousandsWords = [];
        $millionsWords = [];

        $levs = array_pop($chunks);
        $levWords = $this->tripletToWords($levs);

        if (!empty($chunks)) {
            $thousandsWords = $this->tripletToWords(array_pop($chunks), true, $this->amount < 2000);
        }

        if (!empty($chunks)) {
            $millionsWords = $this->tripletToWords(array_pop($chunks));
        }

        $levString = $this->glueMoneyWords($levWords);
        $thousandsString = $this->glueMoneyWords($thousandsWords, $this->amount >= 2000 ? 'хиляди' : '');
        $millionsString = $this->glueMoneyWords($millionsWords, 'милиона');

        $allFilledParts = array_filter([$millionsString, $thousandsString, $levString], fn ($x) => !empty($x));

        $lastPart = array_pop($allFilledParts);
        $remainingParts = implode(' ', $allFilledParts);

        $result = empty($remainingParts) ? $lastPart : (strpos($lastPart, ' и ') === false ? $remainingParts . ' и ' . $lastPart : $remainingParts . ' ' . $lastPart);
        $result .= $this->amount === 1 ? ' лев' : ' лева';

        return $result;
    }

    private function glueMoneyWords(array $words, ?string $positionIdentifier = null): string {
        $hundreds = array_shift($words);
        $tens = array_shift($words);
        $ones = array_shift($words);

        $string = '';

        if (!empty($hundreds)) {
            $string .= $hundreds;
        }

        if (!empty($tens) or !empty($ones)) {
            if (!empty($tens) and !empty($ones)) {
                $string .= empty($string) ? $tens . ' и ' . $ones : ' ' . $tens . ' и ' . $ones;
            } elseif (empty($tens)) {
                $string .= empty($string) ? $ones : ' и ' . $ones;
            } elseif (empty($ones)) {
                $string .= empty($string) ? $tens : ' и ' . $tens;
            }
        }

        if (!empty($string)) {
            return empty($positionIdentifier) ? $string : $string . ' ' . $positionIdentifier;
        } else {
            return '';
        }
    }

    private function tripletToWords(string $levs, bool $thousands = false, bool $standAloneThousand = false): array
    {
        $words = '';
        $hundreds = null;
        $tens = null;
        $ones = null;
        $parts = str_split($levs, 1);

        $ones = array_pop($parts);

        if (!empty($parts)) {
            $tens = array_pop($parts);
        }
        if (!empty($parts)) {
            $hundreds = array_pop($parts);
        }

        $hundredsText = null;
        $tensText = null;
        $onesText = null;

        switch ($hundreds) {
            case '1' : $hundredsText = 'сто'; break;
            case '2' : $hundredsText = 'двеста'; break;
            case '3' : $hundredsText = 'триста'; break;
            case '4' : $hundredsText = 'четиристотин'; break;
            case '5' : $hundredsText = 'петстотин'; break;
            case '6' : $hundredsText = 'шестстотин'; break;
            case '7' : $hundredsText = 'седемстотин'; break;
            case '8' : $hundredsText = 'осемстотин'; break;
            case '9' : $hundredsText = 'деветстотин'; break;
        }

        switch ($tens) {
            // In case of 1, look below - tens must be handled separately
            case '1' :
                switch ($ones) {
                    case '0' : $tensText = 'десет'; break;
                    case '1' : $tensText = 'единадесет'; break;
                    case '2' : $tensText = 'дванадесет'; break;
                    case '3' : $tensText = 'тринадесет'; break;
                    case '4' : $tensText = 'четиринадесет'; break;
                    case '5' : $tensText = 'петнадесет'; break;
                    case '6' : $tensText = 'шестнадесет'; break;
                    case '7' : $tensText = 'седемнадесет'; break;
                    case '8' : $tensText = 'осемнадесет'; break;
                    case '9' : $tensText = 'деветнадесет'; break;
                }
                break;
            case '2' : $tensText = 'двадесет'; break;
            case '3' : $tensText = 'тридесет'; break;
            case '4' : $tensText = 'четиридесет'; break;
            case '5' : $tensText = 'петдесет'; break;
            case '6' : $tensText = 'шестдесет'; break;
            case '7' : $tensText = 'седемдесет'; break;
            case '8' : $tensText = 'осемдесет'; break;
            case '9' : $tensText = 'деветдесет'; break;
        }

        if ($tens !== '1') {
            switch ($ones) {
                case '1' : $onesText = (($thousands and $standAloneThousand) ? 'хиляда' : ($thousands ? 'една' : 'един')); break;
                case '2' : $onesText = $thousands ? 'две' : 'два'; break;
                case '3' : $onesText = 'три'; break;
                case '4' : $onesText = 'четири'; break;
                case '5' : $onesText = 'пет'; break;
                case '6' : $onesText = 'шест'; break;
                case '7' : $onesText = 'седем'; break;
                case '8' : $onesText = 'осем'; break;
                case '9' : $onesText = 'девет'; break;
            }
        }

        return [$hundredsText, $tensText, $onesText];
    }
}