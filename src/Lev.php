<?php

namespace Northbridg3\Levtowords;

class Lev
{
    private string $amount;

    public function __construct($amount, string $decimalSeparator = '.')
    {
        $this->amount = (string) $amount;
        $this->amount = str_replace(',', '.', $this->amount);

        $parts = explode('.', $this->amount);
        $wholePart = reset($parts);

        if (strlen($wholePart) > 9) {
            throw new \LengthException('Numbers equal to or bigger than 1 billion are not supported.');
        }
    }

    public function toWords()
    {
        $decimalSeparatorPos = strpos($this->amount, '.');

        if ($decimalSeparatorPos === false) {
            $hasCoins = false;
            $amount = $this->amount;
        } else {
            $hasCoins = true;
            $parts = explode('.', $this->amount);
            $coins = str_pad(array_pop($parts), 2, '0', STR_PAD_RIGHT);
            $amount = array_pop($parts);
        }

        $chunks = array_reverse(array_map(fn ($x) => strrev($x), str_split(strrev($amount), 3)));

        $levsWords = [];
        $thousandsWords = [];
        $millionsWords = [];

        $levs = str_pad(array_pop($chunks), 3, '0', STR_PAD_LEFT);
        $levWords = $this->tripletToWords($levs);
        $levString = $this->glueMoneyWords($levWords);

        if (!empty($chunks)) {
            $thousands = str_pad(array_pop($chunks), 3, '0', STR_PAD_LEFT);
            $standAloneThousand = $thousands === '001';
            $thousandsWords = $this->tripletToWords($thousands, true, $standAloneThousand);
            $thousandsString = $this->glueMoneyWords($thousandsWords, $standAloneThousand ? '' : 'хиляди');
        }

        if (!empty($chunks)) {
            $millions = str_pad(array_pop($chunks), 3, '0', STR_PAD_LEFT);
            $standAloneMillion = $millions === '001';
            $millionsWords = $this->tripletToWords($millions);
            $millionsString = $this->glueMoneyWords($millionsWords, $standAloneMillion ? 'милион' : 'милиона');
        }

        $allFilledParts = array_filter([$millionsString ?? null, $thousandsString ?? null, $levString], fn ($x) => !empty($x));

        $lastPart = array_pop($allFilledParts);
        $remainingParts = implode(' ', $allFilledParts);

        $result = empty($remainingParts) ? $lastPart : (strpos($lastPart, ' и ') === false ? $remainingParts . ' и ' . $lastPart : $remainingParts . ' ' . $lastPart);
        $result .= $amount === '1' ? ' лев' : ' лева';
        $result = $amount !== '0' ? $result : '';

        if ($hasCoins and $coins !== '000') {
            $coinsWords = $this->tripletToWords($coins, true);
            $coinsString = $this->glueMoneyWords($coinsWords);
            $result .= empty($result) ? $coinsString : ' и '. $coinsString;
            $result .= $coins === '01' ? ' стотинка' : ' стотинки';
        }

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

    private function tripletToWords(string $levs, bool $thousands = false, bool $standAlone = false): array
    {
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
                case '1' : $onesText = (($thousands and $standAlone) ? 'хиляда' : ($thousands ? 'една' : 'един')); break;
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