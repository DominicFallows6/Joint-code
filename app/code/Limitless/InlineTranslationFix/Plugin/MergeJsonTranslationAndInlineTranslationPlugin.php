<?php

namespace Limitless\InlineTranslationFix\Plugin;

use Closure;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Translate\ResourceInterface;
use Magento\Translation\Model\FileManager;
use Magento\Translation\Model\Inline\CacheManager;

class MergeJsonTranslationAndInlineTranslationPlugin
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;
    /**
     * @var ResourceInterface
     */
    private $translateResource;
    /**
     * @var ResolverInterface
     */
    private $localeResolver;
    /**
     * @var FileManager
     */
    private $fileManager;

    public function __construct(
        ManagerInterface $eventManager,
        ResourceInterface $translateResource,
        ResolverInterface $localeResolver,
        FileManager $fileManager
    ) {
        $this->eventManager = $eventManager;
        $this->translateResource = $translateResource;
        $this->localeResolver = $localeResolver;
        $this->fileManager = $fileManager;
    }

    public function getTranslationsFromJsonFile()
    {
        $file = $this->getTranslationFilePath();
        if (!file_exists($file)) {
            return [];
        }
        $content = file_get_contents($file);
        $translations = json_decode($content, true);
        if (json_last_error()) {
            return [];
        }
        return $translations;
    }

    public function aroundUpdateAndGetTranslations(CacheManager $subject, Closure $proceed)
    {
        $this->eventManager->dispatch('adminhtml_cache_flush_system');
        $translations = $this->translateResource->getTranslationArray(null, $this->localeResolver->getLocale());
        $mergeTranslations = array_merge($this->getTranslationsFromJsonFile(),$translations);
        $this->fileManager->updateTranslationFileContent(json_encode($mergeTranslations));
        return $mergeTranslations;
    }

    /**
     * @return string
     */
    private function getTranslationFilePath(): string
    {
        $method = new \ReflectionMethod($this->fileManager,'getTranslationFileFullPath');
        $method->setAccessible(true);
        return $method->invoke($this->fileManager);
    }
}