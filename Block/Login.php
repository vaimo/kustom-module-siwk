<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Siwk\Block;

use Klarna\AdminSettings\Model\Configurations\Siwk;
use Klarna\AdminSettings\Model\System\Config\Siwk\Source\DefaultScope;
use Klarna\Base\Model\Api\MagentoToKlarnaLocaleMapper;
use Klarna\AdminSettings\Model\Configurations\Api;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;

/**
 * @internal
 */
class Login extends Base
{

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;
    /**
     * @var MagentoToKlarnaLocaleMapper
     */
    private MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper;
    /**
     * @var Api
     */
    private Api $apiConfiguration;
    /**
     * @var Siwk
     */
    private Siwk $siwkConfiguration;
    /**
     * @var DefaultScope
     */
    private DefaultScope $defaultScopes;

    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param CustomerSession $customerSession
     * @param MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper
     * @param Api $apiConfiguration
     * @param Siwk $siwkConfiguration
     * @param DefaultScope $defaultScopes
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        CustomerSession $customerSession,
        MagentoToKlarnaLocaleMapper $magentoToKlarnaLocaleMapper,
        Api $apiConfiguration,
        Siwk $siwkConfiguration,
        DefaultScope $defaultScopes,
        array $data = []
    ) {
        parent::__construct($siwkConfiguration, $context, $data);
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
        $this->magentoToKlarnaLocaleMapper = $magentoToKlarnaLocaleMapper;
        $this->siwkConfiguration = $siwkConfiguration;
        $this->apiConfiguration = $apiConfiguration;
        $this->defaultScopes = $defaultScopes;
    }

    /**
     * Getting back the button theme
     *
     * @return string
     */
    public function getButtonTheme(): string
    {
        return $this->siwkConfiguration->getButtonTheme($this->_storeManager->getStore());
    }

    /**
     * Getting back the button alignment
     *
     * @return string
     */
    public function getButtonAlignment(): string
    {
        return $this->siwkConfiguration->getButtonAlignment($this->_storeManager->getStore());
    }

    /**
     * Getting back the button shape
     *
     * @return string
     */
    public function getButtonShape(): string
    {
        return $this->siwkConfiguration->getButtonShape($this->_storeManager->getStore());
    }

    /**
     * Getting back the ajax update url
     *
     * @return string
     */
    public function getAjaxUpdateUrl(): string
    {
        return  $this->urlBuilder->getCurrentUrl() . 'siwk/klarna/login';
    }

    /**
     * Returns true if its a showable position
     *
     * @param string $position
     * @return bool
     */
    public function isSiwkOnPositionEnabled(string $position): bool
    {
        if ($this->customerSession->isLoggedIn()) {
            return false;
        }
        $generalEnabled = $this->isSiwkEnabled();
        if (!$generalEnabled) {
            return false;
        }

        return $this->siwkConfiguration->isEnabledOnPosition($this->_storeManager->getStore(), $position);
    }

    /**
     * Getting back the scopes
     *
     * @return string
     */
    public function getScopes(): string
    {
        $defaultScopes = [];
        foreach ($this->defaultScopes->toOptionArray() as $scope) {
            $defaultScopes[] = $scope['value'];
        }

        return implode(' ', $defaultScopes) . ' openid offline_access customer:login ' .
            str_replace(',', ' ', $this->siwkConfiguration->getScopes($this->_storeManager->getStore()));
    }

    /**
     * Getting back the current url
     *
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->urlBuilder->getCurrentUrl() . 'siwk/klarna/callback';
    }

    /**
     * Getting back the interaction mode
     *
     * @return string
     */
    public function getInteractionMode(): string
    {
        return 'DEVICE_BEST';
    }
}
