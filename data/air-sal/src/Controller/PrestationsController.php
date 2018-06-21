<?php

namespace App\Controller;

use App\Entity\Prestations;
use App\Form\PrestationsType;
use App\Repository\PrestationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/prestations")
 */
class PrestationsController extends Controller
{
    /**
     * @Route("/", name="prestations_index", methods="GET")
     */
    public function index(PrestationsRepository $prestationsRepository): Response
    {
        return $this->render('prestations/index.html.twig', ['prestations' => $prestationsRepository->findAll()]);
    }

    /**
     * @Route("/new", name="prestations_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $prestation = new Prestations();
        $form = $this->createForm(PrestationsType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($prestation);
            $em->flush();

            return $this->redirectToRoute('prestations_index');
        }

        return $this->render('prestations/new.html.twig', [
            'prestation' => $prestation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="prestations_show", methods="GET")
     */
    public function show(Prestations $prestation): Response
    {
        return $this->render('prestations/show.html.twig', ['prestation' => $prestation]);
    }

    /**
     * @Route("/{id}/edit", name="prestations_edit", methods="GET|POST")
     */
    public function edit(Request $request, Prestations $prestation): Response
    {
        $form = $this->createForm(PrestationsType::class, $prestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('prestations_edit', ['id' => $prestation->getId()]);
        }

        return $this->render('prestations/edit.html.twig', [
            'prestation' => $prestation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="prestations_delete", methods="DELETE")
     */
    public function delete(Request $request, Prestations $prestation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$prestation->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($prestation);
            $em->flush();
        }

        return $this->redirectToRoute('prestations_index');
    }
}
