<?php

namespace Onecode\ShopFlixConnector\Model;

use Magento\Framework\Data\OptionSourceInterface;

class Website implements OptionSourceInterface
{

    /**
     * @var Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    protected $websiteCollectionFactory;
 
    /**
     * Class constructor.
     * @param Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
    )
    {
        $this->websiteCollectionFactory = $websiteCollectionFactory;
    }

    /**
     * Converts to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->websiteCollectionFactory as $website) {
            $options[] = [
                'label' => $website->getName(),
                'value' => $website->getWebsiteId(),
            ];
        }

        return $options;
    }
}