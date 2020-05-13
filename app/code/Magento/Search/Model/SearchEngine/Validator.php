<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Search\Model\SearchEngine;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Validate search engine configuration
 */
class Validator implements ValidatorInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $engineBlacklist = ['mysql' => 'MySQL'];

    /**
     * @var ValidatorInterface[]
     */
    private $engineValidators;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param array $engineValidators
     * @param array $engineBlacklist
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $engineValidators = [],
        array $engineBlacklist = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->engineValidators = $engineValidators;
        $this->engineBlacklist = array_merge($this->engineBlacklist, $engineBlacklist);
    }

    /**
     * @inheritDoc
     */
    public function validate(): array
    {
        $errors = [];
        $currentEngine = $this->scopeConfig->getValue('catalog/search/engine');
        if (isset($this->engineBlacklist[$currentEngine])) {
            $blacklistedEngine = $this->engineBlacklist[$currentEngine];
            $errors[] = "Your current search engine, '{$blacklistedEngine}', is not supported."
                . " You must install a supported search engine before upgrading."
                . " See the System Upgrade Guide for more information.";
        }

        if (isset($this->engineValidators[$currentEngine])) {
            $validator = $this->engineValidators[$currentEngine];
            $validationErrors = $validator->validate();
            $errors = array_merge($errors, $validationErrors);
        }
        return $errors;
    }
}
