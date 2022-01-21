<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Controller;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\MerchantGui\Communication\MerchantGuiCommunicationFactory getFactory()
 */
class MerchantStatusController extends AbstractController
{
    /**
     * @var string
     */
    protected const PARAM_ID_MERCHANT = 'id-merchant';

    /**
     * @var string
     */
    protected const PARAM_MERCHANT_STATUS = 'status';

    /**
     * @var string
     */
    protected const MESSAGE_ERROR_MERCHANT_WRONG_PARAMETERS = 'merchant_gui.error_wrong_params';

    /**
     * @var string
     */
    protected const MESSAGE_SUCCESS_MERCHANT_STATUS_UPDATE = 'merchant_gui.success_merchant_status_update';

    /**
     * @uses \Spryker\Zed\MerchantGui\Communication\Controller\ListMerchantController::indexAction()
     *
     * @var string
     */
    protected const URL_REDIRECT_MERCHANT_LIST = '/merchant-gui/list-merchant';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request): RedirectResponse
    {
        $form = $this->getFactory()->createMerchantStatusForm()->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addErrorMessage('CSRF token is not valid');

            return $this->redirectResponse($request->headers->get('referer', static::URL_REDIRECT_MERCHANT_LIST));
        }

        $idMerchant = $request->query->getInt(static::PARAM_ID_MERCHANT);
        $newMerchantStatus = (string)$request->query->get(static::PARAM_MERCHANT_STATUS) ?: null;

        if (!$idMerchant || !$newMerchantStatus) {
            return $this->returnErrorRedirect($request);
        }

        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setIdMerchant($idMerchant);
        $merchantTransfer = $this->getFactory()->getMerchantFacade()->findOne($merchantCriteriaTransfer);
        if (!$merchantTransfer) {
            return $this->returnErrorRedirect($request);
        }

        $merchantTransfer->setStatus($newMerchantStatus);

        $merchantResponseTransfer = $this->getFactory()->getMerchantFacade()->updateMerchant($merchantTransfer);

        if (!$merchantResponseTransfer->getIsSuccess()) {
            return $this->returnErrorRedirect($request);
        }

        $this->addSuccessMessage(static::MESSAGE_SUCCESS_MERCHANT_STATUS_UPDATE);

        return $this->redirectResponse($request->headers->get('referer', static::URL_REDIRECT_MERCHANT_LIST));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function returnErrorRedirect(Request $request): RedirectResponse
    {
        $this->addErrorMessage(static::MESSAGE_ERROR_MERCHANT_WRONG_PARAMETERS);

        return $this->redirectResponse($request->headers->get('referer', static::URL_REDIRECT_MERCHANT_LIST));
    }
}
