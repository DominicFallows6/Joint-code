<?php

namespace Limitless\VarnishInterceptor\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\PageCache\Model\Cache\Server;
use Zend\Uri\Uri;
use Zend\Uri\UriFactory;

class AddVarnishCacheHostsToGetUris
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundGetUris(Server $subject, \Closure $proceed)
    {
        $servers = [];

        $awsOutput = shell_exec($this->scopeConfig->getValue('system/full_page_cache/varnish/varnish_cli_command', ScopeInterface::SCOPE_STORE));
        $awsHosts = explode("\n",$awsOutput);

        foreach ($awsHosts as $host) {
            if ($host != '') {
                $servers[] = UriFactory::factory('')
                    ->setHost(explode("\t", $host)[1])
                    ->setPort(isset($host['port']) ? $host['port'] : Server::DEFAULT_PORT);
            }
        }

        return $servers;
    }
}
