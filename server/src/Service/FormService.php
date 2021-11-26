<?php

namespace App\Service;

use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FormService
{
    private FormFactoryInterface $formFactory;

    public function __construct(
        FormFactoryInterface $formFactory,
    ) {
        $this->formFactory = $formFactory;
    }

    public function handleForm(string $formType, Request $request, mixed $data = null): mixed
    {
        $form = $this->formFactory->create($formType, $data);
        $form->submit(json_decode($request->getContent(), true), true);

        $this->validateForm($form);

        return $form->getData();
    }

    public function validateForm(FormInterface $form)
    {
        $this->validateErrors($this->getFormErrors($form));
    }

    public function getFormErrors(FormInterface $form): array
    {
        $errors = $form->getErrors(true);
        $count = $errors->count();

        $errors_array = [];
        for ($i = 0; $i < $count; ++$i) {
            $error = $errors->offsetGet($i);
            $errors_array[$error->getOrigin()->getName()] = $error->getMessage();
        }

        return $errors_array;
    }

    private function validateErrors(array $errors): void
    {
        if (empty($errors)) return;

        throw new BadRequestHttpException(json_encode($errors), null, 400);
    }
}
