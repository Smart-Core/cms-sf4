<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Monolith\CMSBundle\Entity\Language;
use Monolith\CMSBundle\Form\Type\LanguageFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Smart\CoreBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN_LANGUAGE') or is_granted('ROLE_SUPER_ADMIN')")
 */
class AdminLanguageController extends Controller
{
    //use ControllerTrait;

    /**
     * @return Response
     *
     * @Route("/language/", name="cms_admin.language")
     */
    public function indexAction(EntityManagerInterface $em): Response
    {
        $languages = $em->getRepository(Language::class)->findAll();

        return $this->render('@CMS/Admin/Language/index.html.twig', [
            'languages' => $languages,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/language/create/", name="cms_admin.language_create")
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createForm(LanguageFormType::class, new Language());
        $form->add('create', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        $form->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('cms_admin.language');
            }

            if ($form->get('create')->isClicked() and $form->isValid()) {
                /** @var Language $language */
                $language = $form->getData();
                $language->setUser($this->getUser());

                $this->persist($language, true);

                $this->addFlash('success', 'Язык добавлен.');

                return $this->redirectToRoute('cms_admin.language');
            }
        }

        return $this->render('@CMS/Admin/Language/create.html.twig', [
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @param Request  $request
     * @param Language $language
     *
     * @return Response
     *
     * @Route("/language/{id<\d+>}/", name="cms_admin.language_edit")
     */
    public function editAction(Request $request, Language $language): Response
    {
        $form = $this->createForm(LanguageFormType::class, $language);
        $form->add('update', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        $form->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('cms_admin.language');
            }

            if ($form->get('update')->isClicked() and $form->isValid()) {
                $this->persist($form->getData(), true);
                $this->addFlash('success', 'Язык обновлён.');

                return $this->redirectToRoute('cms_admin.language');
            }
        }

        return $this->render('@CMS/Admin/Language/edit.html.twig', [
            'form'    => $form->createView(),
        ]);
    }
}
