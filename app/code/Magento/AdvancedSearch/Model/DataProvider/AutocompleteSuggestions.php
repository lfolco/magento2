<?php
/************************************************************************
 *
 * Copyright 2023 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 * ***********************************************************************
 */
namespace Magento\AdvancedSearch\Model\DataProvider;

use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Search\Model\QueryFactory;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\AdvancedSearch\Model\SuggestedQueries;
use Magento\CatalogSearch\Model\Autocomplete\DataProvider;
use Magento\AdvancedSearch\Model\SuggestedQueriesInterface;
use Magento\Store\Model\ScopeInterface;

class AutocompleteSuggestions implements DataProviderInterface
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var SuggestedQueries
     */
    private $suggestedQueries;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var ScopeConfig
     */
    private $scopeConfig;

    /**
     * @param QueryFactory $queryFactory
     * @param ItemFactory $itemFactory
     * @param ScopeConfig $scopeConfig
     * @param SuggestedQueries $suggestedQueries
     * @param DataProvider $dataProvider
     */
    public function __construct(
        QueryFactory $queryFactory,
        ItemFactory $itemFactory,
        ScopeConfig $scopeConfig,
        SuggestedQueries $suggestedQueries,
        DataProvider $dataProvider
    ) {
        $this->queryFactory = $queryFactory;
        $this->itemFactory = $itemFactory;
        $this->suggestedQueries = $suggestedQueries;
        $this->dataProvider = $dataProvider;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        $result = [];
        if ($this->scopeConfig->isSetFlag(
            SuggestedQueriesInterface::SEARCH_SUGGESTION_ENABLED,
            ScopeInterface::SCOPE_STORE
        )) {
            // populate with search suggestions
            $query = $this->queryFactory->get();
            $suggestions = $this->suggestedQueries->getItems($query);
            foreach ($suggestions as $suggestion) {
                $resultItem = $this->itemFactory->create([
                    'title' => $suggestion->getQueryText(),
                    'num_results' => $suggestion->getResultsCount(),
                ]);
                $result[] = $resultItem;
            }
        } else {
            // populate with autocomplete
            $result = $this->dataProvider->getItems();
        }
        return $result;
    }
}
