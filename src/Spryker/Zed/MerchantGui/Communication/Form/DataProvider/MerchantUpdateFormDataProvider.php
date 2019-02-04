<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Form\DataProvider;

use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\MerchantGui\Communication\Form\MerchantForm;
use Spryker\Zed\MerchantGui\Communication\Form\MerchantUpdateForm;
use Spryker\Zed\MerchantGui\Dependency\Facade\MerchantGuiToMerchantFacadeInterface;
use Spryker\Zed\MerchantGui\MerchantGuiConfig;

class MerchantUpdateFormDataProvider
{
    /**
     * @var \Spryker\Zed\MerchantGui\Dependency\Facade\MerchantGuiToMerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var \Spryker\Zed\MerchantGui\MerchantGuiConfig
     */
    protected $config;

    /**
     * @param \Spryker\Zed\MerchantGui\Dependency\Facade\MerchantGuiToMerchantFacadeInterface $merchantFacade
     * @param \Spryker\Zed\MerchantGui\MerchantGuiConfig $config
     */
    public function __construct(
        MerchantGuiToMerchantFacadeInterface $merchantFacade,
        MerchantGuiConfig $config
    ) {
        $this->merchantFacade = $merchantFacade;
        $this->config = $config;
    }

    /**
     * @param int $idMerchant
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function getData(int $idMerchant): ?MerchantTransfer
    {
        $merchantTransfer = new MerchantTransfer();
        $merchantTransfer->setIdMerchant($idMerchant);

        return $this->merchantFacade->findMerchantById($merchantTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return array
     */
    public function getOptions(MerchantTransfer $merchantTransfer): array
    {
        $nextStatuses = $this->merchantFacade->getNextStatuses($merchantTransfer->getStatus());
        $options = [
            'data_class' => MerchantTransfer::class,
            MerchantForm::SALUTATION_CHOICES_OPTION => $this->config->getSalutationChoices(),
            MerchantUpdateForm::OPTION_CURRENT_ID => $merchantTransfer->getIdMerchant(),
            MerchantUpdateForm::OPTION_STATUS_CHOICES => array_merge([$merchantTransfer->getStatus()], $nextStatuses),
        ];

        return $options;
    }
}
