<?php
/**
 * Data.php
 *
 * @copyright Copyright © 2021 Onecode P.C. All rights reserved.
 * @author    Spyros Bodinis {spyros@onecode.gr}
 */

namespace Onecode\ShopFlixConnector\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Registry;

/**
 * Class Data
 * @package Onecode\ShopFlixConnector\Helper
 */
class Data extends AbstractHelper implements ScopeInterface
{

    /**
     * Enabled Path
     */
    const ENABLE_PATH = 'shopflix/settings/enable';

    /**
     * Api Url
     */
    const API_URL = 'shopflix/settings/api_url';

    /**
     * Username path
     */
    const USERNAME_PATH = 'shopflix/settings/username';

    /**
     * Api Key path
     */
    const API_KEY_PATH = 'shopflix/settings/api_key';

    const CONVERT_SHOPFLIX_ORDER_TO_MAGENTO = 'shopflix/settings/to_order';

    const SHOPFLIX_TIME_MODIFIER = 'shopflix/settings/time_modifier';

    const SHOPFLIX_VOUCHER_PRINT_FORMAT = 'shopflix/settings/tracking_voucher_pdf_format';

    const INVOICE_ON_ACCEPTANCE = 'shopflix/settings/invoice';

    /**
     * Selected products types
     */
    const SELECTED_PRODUCTS_PATH = 'shopflix/settings/supported_products';

    /**
     * Generate products xml
     */
    const GENERATE_XML_PATH = 'shopflix/xml_setting/enable';

    /**
     * MPN Attribute Path
     */
    const MPN_ATTRIBUTE_PATH = 'shopflix/xml_setting/mpn';

    /**
     * EAN Attribute Path
     */
    const EAN_ATTRIBUTE_PATH = 'shopflix/xml_setting/ean';

    /**
     * Title Attribute Path
     */
    const TITLE_ATTRIBUTE_PATH = 'shopflix/xml_setting/title';

    /**
     * Description Attribute Path
     */
    const DESCRIPTION_ATTRIBUTE_PATH = 'shopflix/xml_setting/description';

    /**
     * Export Category Path
     */
    const EXPORT_CATEGORY_PATH = 'shopflix/xml_setting/export_category_tree';

    /**
     * Manufacturer Path
     */
    const MANUFACTURER_PATH = 'shopflix/xml_setting/manufacturer';

    /**
     * Weight Path
     */
    const WEIGHT_PATH = 'shopflix/xml_setting/weight';

    /**
     * Auto Accept Path
     */
    const AUTO_ACCEPT_PATH = 'shopflix/settings/auto_accept';

    /**
     * @var Magento\Framework\Encryption\EncryptorInterface
     */
    private $_encryptor;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Data constructor.
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param Registry $registry
     */
    public function __construct(
        Context            $context,
        EncryptorInterface $encryptor,
        Registry           $registry
    )
    {
        parent::__construct($context);
        $this->_encryptor = $encryptor;
        $this->_registry = $registry;
    }

    /**
     * Get selected products types
     * @return array
     */
    public function getSelectedProductsTypes(): array
    {
        return explode(",", $this->getConfig(self::SELECTED_PRODUCTS_PATH));
    }

    /**
     * Retrieve config value
     *
     * @param $config
     * @return string
     */
    public function getConfig($config): ?string
    {
        return $this->scopeConfig->getValue(
            $config,
            self::SCOPE_WEBSITE,
            $this->_registry->registry('shopflix_website_id')
        );
    }

    public function isEnabled(): bool
    {
        return (boolean)$this->getConfig(self::ENABLE_PATH);
    }

    public function invoice(): bool
    {
        return (boolean)$this->getConfig(self::INVOICE_ON_ACCEPTANCE);
    }

    public function canGenerateXml($token): bool
    {
        return (boolean)$this->getConfig(self::ENABLE_PATH) &&
            (boolean)$this->getConfig(self::GENERATE_XML_PATH) && $token == $this->getHashedCode();
    }

    public function getHashedCode()
    {
        return $this->_encryptor->hash($this->getUsername());
    }

    /**
     * Get Username
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->getConfig(self::USERNAME_PATH);
    }

    /**
     * Get Api Key
     * @return string
     */
    public function getApikey(): string
    {
        return (string)$this->_encryptor->decrypt($this->getConfig(self::API_KEY_PATH));
    }

    public function getApiUrl(): string
    {
        return $this->getConfig(self::API_URL);
    }

    public function getMpnAttribute(): string
    {
        return $this->getConfig(self::MPN_ATTRIBUTE_PATH) ?? "sku";
    }

    public function getEanAttribute(): string
    {
        return $this->getConfig(self::EAN_ATTRIBUTE_PATH) ?? "sku";
    }

    public function getTitleAttribute(): string
    {
        return $this->getConfig(self::TITLE_ATTRIBUTE_PATH) ?? "name";
    }

    public function getDescriptionAttribute(): string
    {
        return $this->getConfig(self::DESCRIPTION_ATTRIBUTE_PATH) ?? "description";
    }

    public function exportCategory(): bool
    {
        return (boolean)$this->getConfig(self::EXPORT_CATEGORY_PATH);
    }


    public function getManufacturerAttribute(): string
    {
        return $this->getConfig(self::MANUFACTURER_PATH);
    }

    public function getWeightAttribute(): string
    {
        return $this->getConfig(self::WEIGHT_PATH);
    }

    public function toOrder(): string
    {
        return $this->getConfig(self::CONVERT_SHOPFLIX_ORDER_TO_MAGENTO);
    }

    public function autoAccept(): bool
    {
        return (boolean)$this->getConfig(self::AUTO_ACCEPT_PATH);
    }

    public function getTimeModifier(): string
    {
        return $this->getConfig(self::SHOPFLIX_TIME_MODIFIER);
    }

    public function getVoucherPrintFormat(): ?string
    {
        return $this->getConfig(self::SHOPFLIX_VOUCHER_PRINT_FORMAT);
    }
}
