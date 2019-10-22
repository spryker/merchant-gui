<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Form\Constraint;

use Generated\Shared\Transfer\MerchantCriteriaFilterTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Symfony\Component\Validator\Constraint;

class UniqueMerchantReference extends Constraint
{
    public const OPTION_MERCHANT_FACADE = 'merchantFacade';
    public const OPTION_CURRENT_MERCHANT_ID = 'currentMerchantId';

    /**
     * @var \Spryker\Zed\MerchantGui\Dependency\Facade\MerchantGuiToMerchantFacadeInterface
     */
    protected $merchantFacade;

    /**
     * @var int|null
     */
    protected $currentMerchantId;

    protected const VALIDATION_MESSAGE = 'Merchant reference is already used.';

    /**
     * @return string
     */
    public function getTargets(): string
    {
        return static::CLASS_CONSTRAINT;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return static::VALIDATION_MESSAGE;
    }

    /**
     * @return int|null
     */
    public function getCurrentMerchantId(): ?int
    {
        return $this->currentMerchantId;
    }

    /**
     * @param string $merchantReference
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function findMerchantTransfer(string $merchantReference): ?MerchantTransfer
    {
        $merchantCriteriaFilterTransfer = new MerchantCriteriaFilterTransfer();
        $merchantCriteriaFilterTransfer->setMerchantReference($merchantReference);

        return $this->merchantFacade->findOne($merchantCriteriaFilterTransfer);
    }
}
