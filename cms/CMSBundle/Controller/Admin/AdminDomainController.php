<?php

declare(strict_types=1);

namespace Monolith\CMSBundle\Controller\Admin;

use Monolith\CMSBundle\Entity\Domain;
use Monolith\CMSBundle\Form\Type\DomainFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Smart\CoreBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('ROLE_ADMIN_SYSTEM') or is_granted('ROLE_SUPER_ADMIN')")
 */
class AdminDomainController extends Controller
{
    /**
     * @return Response
     *
     * @Route("/site/domains/", name="cms_admin.domains")
     */
    public function indexAction(): Response
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->get('doctrine.orm.entity_manager');

        $domains = $em->getRepository(Domain::class)->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('@CMS/Admin/Domain/index.html.twig', [
            'domains' => $domains,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/domain_create/", name="cms_admin.domain_create")
     */
    public function createAction(Request $request): Response
    {
        $form = $this->createForm(DomainFormType::class, new Domain());
        $form->add('create', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        $form->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('cms_admin.domains');
            }

            if ($form->get('create')->isClicked() and $form->isValid()) {
                /** @var Domain $language */
                $domain = $form->getData();
                $domain->setUser($this->getUser());

                $this->persist($domain, true);

                $this->addFlash('success', 'Domain добавлен.');

                return $this->redirectToRoute('cms_admin.domains');
            }
        }

        return $this->render('@CMS/Admin/Domain/create.html.twig', [
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Domain  $domain
     *
     * @return Response
     *
     * @Route("/domain/{id<\d+>}/", name="cms_admin.domain_edit")
     */
    public function editAction(Request $request, Domain $domain): Response
    {
        $form = $this->createForm(DomainFormType::class, $domain);
        $form->add('update', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        $form->add('delete', SubmitType::class, ['attr' => ['class' => 'btn-danger', 'onclick' => "return confirm('Вы уверены, что хотите удалить домен?')", 'formnovalidate' => 'formnovalidate']]);
        $form->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('cms_admin.domains');
            }

            if ($form->get('delete')->isClicked() and $form->isValid()) {
                $this->remove($form->getData(), true);
                $this->addFlash('success', 'Domain удалён.');

                return $this->redirectToRoute('cms_admin.domains');
            }

            if ($form->get('update')->isClicked() and $form->isValid()) {
                $this->persist($form->getData(), true);
                $this->addFlash('success', 'Domain обновлён.');

                return $this->redirectToRoute('cms_admin.domains');
            }
        }

        return $this->render('@CMS/Admin/Domain/edit.html.twig', [
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Domain  $domain
     *
     * @return Response
     *
     * @Route("/domain_create_alias/{id<\d+>}/", name="cms_admin.domain_create_alias")
     */
    public function createAliasAction(Request $request, Domain $domain): Response
    {
        $alias = new Domain();
        $alias->setParent($domain);

        $form = $this->createForm(DomainFormType::class, $alias);
        $form->add('create', SubmitType::class, ['attr' => ['class' => 'btn-primary']]);
        $form->add('cancel', SubmitType::class, ['attr' => ['class' => 'btn-default', 'formnovalidate' => 'formnovalidate']]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->get('cancel')->isClicked()) {
                return $this->redirectToRoute('cms_admin.domains');
            }

            if ($form->get('create')->isClicked() and $form->isValid()) {
                /** @var Domain $language */
                $domain = $form->getData();
                $domain->setUser($this->getUser());

                $this->persist($domain, true);

                $this->addFlash('success', 'Domain alias добавлен.');

                return $this->redirectToRoute('cms_admin.domains');
            }
        }

        return $this->render('@CMS/Admin/Domain/create_alias.html.twig', [
            'domain' => $domain,
            'form'   => $form->createView(),
        ]);
    }
}
