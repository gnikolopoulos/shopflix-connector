<?php
/**
 * Sync.php
 *
 * @copyright Copyright © 2022 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

use Exception;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Order;
use Onecode\ShopFlixConnector\Helper\ExportOrders;
use Onecode\ShopFlixConnector\Helper\ImportOrders;

class Sync extends Order implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::actions_sync_orders';

    /**
     * @var Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    protected $_websiteCollectionFactory;

    /**
     * Class constructor.
     * @param Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        WebsiteCollectionFactory $websiteCollectionFactory
    )
    {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        foreach ($this->_websiteCollectionFactory->create() as $website) {
            $this->_coreRegistry->register('shopflix_website_id', $website->getWebsiteId());

            try {
                $syncOrdersFromShopFlix = $this->_objectManager->create(ImportOrders::class);
                $syncOrdersFromShopFlix->import();
                $syncOrdersToShopFlix = $this->_objectManager->create(ExportOrders::class);
                $syncOrdersToShopFlix->export();
                $this->messageManager->addSuccessMessage(__("We have successfully synced the orders from SHOPFLIX"));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->logger->critical($e);
            }
        }

        $this->_coreRegistry->unregister('shopflix_website_id');
        return $resultRedirect->setPath('shopflix/*/');
    }
}
