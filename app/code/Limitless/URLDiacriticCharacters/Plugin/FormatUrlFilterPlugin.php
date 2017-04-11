<?php

namespace Limitless\URLDiacriticCharacters\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filter\TranslitUrl;

class FormatUrlFilterPlugin
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param TranslitUrl $subject
     * @param mixed $string - should be string but could be null
     * @return array
     */
    public function beforeFilter(TranslitUrl $subject, $string)
    {
        $translationListString = $this->getTranslationLists();
        if ($translationListString) {
            $string = $this->replaceChars($string, $translationListString);
        }

        return [$string];
    }

    /**
     * @param string $inputString
     * @param string $translationListRaw
     * @return mixed
     */
    private function replaceChars(string $inputString, string $translationListRaw)
    {
        $termSplit = ':';
        $multipleSplit = ',';

        $translateFrom = $translateTo = [];

        $convertTerms = explode($multipleSplit, $translationListRaw);

        foreach ($convertTerms as $term)
        {
            if (strpos($term, $termSplit) !== false)
            {
                $conversionParams = explode($termSplit, $term, 2);
                if (isset($conversionParams[0]) && isset($conversionParams[1]))
                {
                    $translateFrom[] = trim($conversionParams[0]);
                    $translateTo[] = trim($conversionParams[1]);
                }
            }
        }
        return str_replace($translateFrom, $translateTo, $inputString);
    }

    /**
     * @return mixed
     */
    private function getTranslationLists()
    {
        return $this->scopeConfig->getValue(
            'general/limitless_url_character_convert/character_convert_list'
        );
    }

}