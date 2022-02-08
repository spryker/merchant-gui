<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Spryker\Zed\MerchantGui\MerchantGuiConfig;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\MerchantGui\Communication\MerchantGuiCommunicationFactory getFactory()
 */
class CreateMerchantController extends AbstractController
{
    /**
     * @var string
     */
    protected const PARAM_REDIRECT_URL = 'redirect-url';

    /**
     * @var string
     */
    protected const MESSAGE_MERCHANT_CREATE_SUCCESS = 'Merchant created successfully.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        $dataProvider = $this->getFactory()->createMerchantFormDataProvider();
        $merchantForm = $this->getFactory()
            ->getMerchantCreateForm(
                $dataProvider->getData(),
                $dataProvider->getOptions(),
            )
            ->handleRequest($request);

        if ($merchantForm->isSubmitted() && $merchantForm->isValid()) {
            return $this->createMerchant($request, $merchantForm);
        }

        return $this->viewResponse([
            'form' => $merchantForm->createView(),
            'merchantFormTabs' => $this->getFactory()->createMerchantFormTabs()->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Form\FormInterface $merchantForm
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    protected function createMerchant(Request $request, FormInterface $merchantForm)
    {
        $redirectUrl = $request->get(static::PARAM_REDIRECT_URL, MerchantGuiConfig::URL_MERCHANT_LIST);
        /** @var \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer */
        $merchantTransfer = $merchantForm->getData();
        $merchantResponseTransfer = $this->getFactory()
            ->getMerchantFacade()
            ->createMerchant($merchantTransfer);

        if ($merchantResponseTransfer->getIsSuccess() && $merchantResponseTransfer->getMerchant()->getIdMerchant()) {
            $this->addSuccessMessage(static::MESSAGE_MERCHANT_CREATE_SUCCESS);

            return $this->redirectResponse($redirectUrl);
        }

        foreach ($merchantResponseTransfer->getErrors() as $merchantErrorTransfer) {
            $this->addErrorMessage($merchantErrorTransfer->getMessage());
        }

        return $this->viewResponse([
            'form' => $merchantForm->createView(),
            'merchantFormTabs' => $this->getFactory()->createMerchantFormTabs()->createView(),
        ]);
    }
}
