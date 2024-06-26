<?php
/**
 * Feed
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Controller\Products;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;
use Onecode\Base\Helper\SimpleXMLExtended;
use Onecode\ShopFlixConnector\Helper\Data;
use Onecode\ShopFlixConnector\Helper\ExportProductData;


/**
 * Class Feed
 * @package Onecode\ShopFlixConnector\Controller\Products
 */
class Feed implements HttpGetActionInterface
{


    private $exportProductData;
    private $resultFactory;
    private $helper;
    private $request;
    private $storeManager;
    private $context;

    /**
     * @param ExportProductData $exportProductData
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     * @param Data $helper
     */
    public function __construct(Context               $context,
                                ExportProductData     $exportProductData,
                                ResultFactory         $resultFactory,
                                RequestInterface      $request,
                                StoreManagerInterface $storeManager,
                                Data                  $helper)
    {

        $this->exportProductData = $exportProductData;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->context = $context;

    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException|LocalizedException
     */
    public function execute()
    {


        if ($this->helper->canGenerateXml($this->request->getParam("token", ""))) {

            return $this->resultFactory->create(ResultFactory::TYPE_RAW)
                ->setHeader('Content-Type', 'application/xml')
                ->setContents($this->arrayToXml($this->exportProductData->isSimpler(
                    (bool)$this->request->getParam("simple", false)
                )->exportData()));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('defaultNoRoute');


    }


    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function arrayToXml($array, $xml = false, $level = 0)
    {


        if ($xml === false) {
            $xml = new SimpleXMLExtended("<store name='{$this->storeManager->getStore()->getFrontendName()}' url='{$this->context->getUrl()->getBaseUrl()}' encoding='utf8'/>");
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($level == 1 && is_numeric($key)) {
                    $key = "product";
                }
                if ($level == 3 && is_numeric($key)) {
                    $key = "child";
                }

                $this->arrayToXml($value, $xml->addChild($key), $level + 1);
            } else {
                if (in_array($key, ['description', 'npm', 'category', 'name'])) {
                    $xml->addChild($key)->addCData($value);
                } else {
                    $xml->addChild($key, htmlspecialchars(!is_null($value) ? $value : ''));
                }


            }
        }
        return $xml->asXML();
    }


}
