<?php

namespace Limitless\SubMenuStaticLinks\Plugin\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Topmenu extends Template

{

    const LINK_SPLIT_DELIMITER = '%%%';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * Topmenu constructor.
     * @param Context $context
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        NodeFactory $nodeFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->nodeFactory = $nodeFactory;
    }

    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {

        $linksBlock = $this->getLinkBlockValue();
        $linksBlockArray = explode("\n", $linksBlock);
        $children = $subject->getMenu()->getChildren();
        $categoryLinksArray = [];

        foreach($linksBlockArray as $link)
        {
            $linkParts = explode(self::LINK_SPLIT_DELIMITER, $link, 4);
            if (count($linkParts) == 4) {
                $categoryLinksArray[trim($linkParts[3])][] = ['name' => $linkParts[0], 'url' => $linkParts[1], 'id' => $linkParts[2]];
            }
        }

        foreach($children as $child)
        {
            $childTree = $child->getTree();
            $childId = (preg_replace('/[^0-9]/', '', $child->getId()));

            if (isset($categoryLinksArray[$childId])) {
                $menuLinks = $categoryLinksArray[$childId];
            } else {
                $menuLinks = [];
            }

            foreach($menuLinks as $link)
            {
                $node = new Node($link, 'id', $childTree, $child);
                $childTree->addNode($node, $child);
            }
        }

        return array($outermostClass, $childrenWrapClass, $limit);
    }

    private function getScopeConfigValue($path)
    {
        return $this->scopeConfig->getValue('general/limitless_sub_menu_static_links/' . $path, ScopeInterface::SCOPE_STORE);
    }

    private function getLinkBlockValue()
    {
        return $this->getScopeConfigValue('menu_links_block');
    }

}