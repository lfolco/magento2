<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Security\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Add the `expires_at` form field to the User main form.
 *
 * @package Magento\Security\Observer
 */
class AddUserExpirationToAdminUserForm implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * AddUserExpirationToAdminUserForm constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate)
    {
        $this->localeDate = $localeDate;
    }

    /**
     * Add the `expires_at` field to the admin user edit form.
     * TODO: this element doesn't get added to the form until after the form data is set in
     * \Magento\Framework\Data\Form::setValues.
     * Needs to have \Magento\Framework\Data\Form::addElementToCollection is called for all other form values, form
     * values are set in \Magento\Framework\Data\Form::setValues, and
     * \Magento\Framework\Data\Form::addElementToCollection is called again with this form element
     *
     * addElementToCollection is called with all the form values
     * form->setValues is called in \Magento\User\Block\User\Edit\Tab\Main
     * The form is set on \Magento\User\Block\User\Edit\Tab\Main
     * And then the observer is called
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Backend\Block\Template $block */
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof \Magento\User\Block\User\Edit\Tab\Main) {
            /** @var \Magento\Framework\Data\Form $form */
            $form = $block->getForm();
            $fieldset = $form->getElement('base_fieldset');
            $dateFormat = $this->localeDate->getDateFormat(
                \IntlDateFormatter::MEDIUM
            );
            $timeFormat = $this->localeDate->getTimeFormat(
                \IntlDateFormatter::MEDIUM
            );
            $fieldset->addField(
                'expires_at',
                'date',
                [
                    'name' => 'expires_at',
                    'label' => __('Expiration Date'),
                    'title' => __('Expiration Date'),
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat,
                    'class' => 'validate-date',
                ]
            );
        }
    }
}
