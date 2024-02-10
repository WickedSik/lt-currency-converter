<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\CurrencyRate;
use App\Entity\User;
use App\Form\CurrencyConversionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->buildForm($entityManager);
        $form->handleRequest($request);
        $conversions = [];

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // LOAD ALL THE CURRENCIES
            $currencies = $entityManager->getRepository(CurrencyRate::class)->findBy([
                'fromCode' => $data['from_code']
            ]);
            $value = $data['amount'];

            foreach($currencies as $currency) {
                $conversions[] = [
                    'currency' => $currency,
                    'value' => $value * $currency->getRate()
                ];
            }
        }

        return $this->render('page/index.html.twig', [
            'conversions' => $conversions,
            'form' => $form
        ]);
    }

    private function buildForm(EntityManagerInterface $entityManager): FormInterface {
        $codes = $entityManager->getRepository(CurrencyRate::class)->findOriginCodes();
        $fromCodeOptions = [];
        foreach($codes as $code) {
            $fromCodeOptions[$code] = $code;
        }

        return $this->createFormBuilder()
            ->add('from_code', ChoiceType::class, [
                'label' => 'From',
                'choices' => $fromCodeOptions
            ])
            ->add('amount', MoneyType::class)
            ->add('convert', SubmitType::class)
            ->getForm();
    }
}
