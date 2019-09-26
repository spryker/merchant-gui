<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Controller;

use Generated\Shared\Transfer\MerchantCriteriaFilterTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\MerchantGui\Communication\MerchantGuiCommunicationFactory getFactory()
 */
class StatusMerchantController extends AbstractController
{
    public const PARAM_ID_MERCHANT = 'id-merchant';
    public const PARAM_MERCHANT_STATUS = 'status';

    public const URL_REDIRECT_MERCHANT_LIST = '/merchant-gui/list-merchant';

    public const MESSAGE_ERROR_MERCHANT_WRONG_PARAMETERS = 'Status can\'t be updated.';
    public const MESSAGE_SUCCESS_MERCHANT = 'Status has been updated.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request): RedirectResponse
    {
        $idMerchant = $request->query->get(static::PARAM_ID_MERCHANT);
        $merchantStatus = $request->query->get(static::PARAM_MERCHANT_STATUS);

        if (!$idMerchant || !$merchantStatus) {
            return $this->returnErrorRedirect();
        }

        $merchantCriteriaFilterTransfer = new MerchantCriteriaFilterTransfer();
        $merchantCriteriaFilterTransfer->setIdMerchant($idMerchant);
        $merchantTransfer = $this->getFactory()->getMerchantFacade()->findOne($merchantCriteriaFilterTransfer);
        if (!$merchantTransfer) {
            return $this->returnErrorRedirect();
        }
        $merchantTransfer->setStatus($merchantStatus);

        $merchantResponseTransfer = $this->getFactory()->getMerchantFacade()->updateMerchant($merchantTransfer);

        if (!$merchantResponseTransfer->getIsSuccess()) {
            return $this->returnErrorRedirect();
        }

        $this->addSuccessMessage(static::MESSAGE_SUCCESS_MERCHANT);

        return $this->redirectResponse(static::URL_REDIRECT_MERCHANT_LIST);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function returnErrorRedirect(): RedirectResponse
    {
        $this->addErrorMessage(static::MESSAGE_ERROR_MERCHANT_WRONG_PARAMETERS);

        return $this->redirectResponse(static::URL_REDIRECT_MERCHANT_LIST);
    }
}