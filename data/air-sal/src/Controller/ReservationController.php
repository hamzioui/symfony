<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Entity\Prestations;
use App\Form\PrestationsType;
use App\Repository\PrestationsRepository;
use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/reservation")
 */
class ReservationController extends Controller
{
    /**
     * @Route("/", name="reservation_index", methods="GET")
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', ['reservations' => $reservationRepository->findAll()]);
    }

    /**
     * @Route("/new", name="reservation_new", methods="GET|POST")
     */
    public function new(Request $request, Security $security): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $reservation->setUser($security->getUser());
            $em->persist($reservation);
            $em->flush();
            //die(var_dump($reservation));
            return $this->redirectToRoute('facture_new', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'salles'      => $this->getDoctrine()->getRepository(Salle::class)->findAll(),
            'prestations' => $this->getDoctrine()->getRepository(Prestations::class)->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_show", methods="GET")
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', ['reservation' => $reservation]);
    }

    /**
     * @Route("/{id}/edit", name="reservation_edit", methods="GET|POST")
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_edit', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_delete", methods="DELETE")
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reservation);
            $em->flush();
        }

        return $this->redirectToRoute('reservation_index');
    }

    /**
     * @Route("/new/submitForm", name="reservation_submit", methods="POST")
     */
    public function submitForm(Request $request, Security $security): Response
    {
        var_dump($_POST);

        $doc = $this->getDoctrine();
        $docS = $doc->getRepository(Salle::class);
        $docP = $doc->getRepository(Prestations::class);
        $reservation = new Reservation();
        $reservation -> setSalle( $docS->findOneById( intval($_POST['salle']) ) );
        if(isset($_POST['prestations']) ){
            foreach( $_POST['prestations'] as $p ){
                $reservation -> addPrestation( $docP->findOneById( intval($p) ) );
            }
        }

        $dateStart = new \DateTime( $_POST['date_start'] );
        $reservation -> setDateStart(  $dateStart );
        $dateEnd = new \DateTime($_POST['date_end']);
        $reservation -> setDateEnd( $dateEnd );

        $em = $this->getDoctrine()->getManager();
        $reservation->setUser($security->getUser());
        $em->persist($reservation);
        $em->flush();

        return $this->redirectToRoute('reservation_index');

    }


}
