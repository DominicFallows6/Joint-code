<?php

namespace Limitless\Seo\Model\ResourceModel\Cms;

use Magento\Framework\DataObject;

class Page extends \Magento\Sitemap\Model\ResourceModel\Cms\Page
{

    protected function _prepareObject(array $data)
    {
        $object = new DataObject();
        $object->setId($data[$this->getIdFieldName()]);
        if ($data['url'] == 'home') {
            $data['url'] = '';
        }
        $object->setUrl($data['url']);
        $object->setUpdatedAt($data['updated_at']);

        return $object;
    }

}