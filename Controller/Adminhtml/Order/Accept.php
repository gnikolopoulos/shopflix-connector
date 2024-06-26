<?php
/**
 * Accept.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Adminhtml\Order;


use Exception;
use GuzzleHttp\Exception\RequestException;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Onecode\ShopFlixConnector\Controller\Adminhtml\Order;

class Accept extends Order implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Onecode_ShopFlixConnector::accept';

    /**
     * Cancel order
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->isValidPostRequest()) {
            $this->messageManager->addErrorMessage(__('You have not accept the item.'));
            return $resultRedirect->setPath('shopflix/*/');
        }
        $order = $this->_initOrder();
        if ($order) {
            $this->_coreRegistry->register('shopflix_website_id', $order->getWebsiteId());
            try {
                $this->orderManagement->accept($order->getEntityId() , true);
                $this->messageManager->addSuccessMessage(__('You accepted the order.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RequestException $e){
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->critical($e);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('You have not accepted the item.'));
                $this->logger->critical($e);
            }

            $this->_coreRegistry->unregister('shopflix_website_id');
            return $resultRedirect->setPath('shopflix/order/view', ['order_id' => $order->getId()]);
        }
        return $resultRedirect->setPath('shopflix/*/');
    }
}
