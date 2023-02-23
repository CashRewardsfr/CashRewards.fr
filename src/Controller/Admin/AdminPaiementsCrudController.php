<?php

namespace App\Controller\Admin;

use App\Entity\Paiement;
use App\Form\PaiementType;
use App\Repository\PaiementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/paiements')]
class AdminPaiementsCrudController extends AbstractController
{
    #[Route('/', name: 'app_admin_paiements_crud_index', methods: ['GET'])]
    public function index(PaiementRepository $paiementRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $paiements = $paginator->paginate(
            $paiementRepository->findByDateDesc(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('admin/admin_paiements_crud/index.html.twig', [
            'paiements' => $paiements,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_paiements_crud_show', methods: ['GET'])]
    public function show(Paiement $paiement): Response
    {
        return $this->render('admin/admin_paiements_crud/show.html.twig', [
            'paiement' => $paiement,
        ]);
    }

    #[Route('/{id}/validate', name: 'app_admin_validate_paiements', methods: ['GET', 'POST'])]
    public function edit(Request $request, Paiement $paiement, PaiementRepository $paiementRepository): Response
    {
        if ($this->isCsrfTokenValid('validate'.$paiement->getId(), $request->request->get('_token'))) {

            $paiement->setStatut(1);
            $paiement->setCreated(new \DateTimeImmutable());
            $paiementRepository->add($paiement);

            $this->addFlash('success', 'Demande validée avec succès!');

            return $this->redirectToRoute('app_admin_paiements_crud_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    #[Route('/{id}', name: 'app_admin_paiements_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Paiement $paiement, PaiementRepository $paiementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paiement->getId(), $request->request->get('_token'))) {
            $paiement->setDeleted(1);
            $paiement->setCreated(new \DateTimeImmutable());
            $paiementRepository->add($paiement);

        }

        return $this->redirectToRoute('app_admin_paiements_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
