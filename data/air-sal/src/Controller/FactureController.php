<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Reservation;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/facture")
 */
class FactureController extends Controller
{
    /**
     * @Route("/", name="facture_index", methods="GET")
     */
    public function index(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/index.html.twig', ['factures' => $factureRepository->findAll()]);
    }

    /**
     * @Route("/{id}/new", name="facture_new", methods="GET|POST")
     */
    public function new(Request $request, Reservation $reservation): Response
    {
        //die(var_dump($reservation));
        $facture = new Facture();
        $em = $this->getDoctrine()->getManager();
        //$reservation = $request->request->get('reservation');

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = $this->get('jms_serializer');
        $jsonReservation=  $serializer->serialize($reservation, 'json');

        $facture->setData($jsonReservation);
        $facture->setReservation($reservation);
        $facture->setPrice($reservation->getReservationPrice());

        $em->persist($facture);
        $em->flush();
       
        return $this->redirectToRoute('facture_show', ['id' => $facture->getId()]);
    }

    /**
     * @Route("/{id}", name="facture_show", methods="GET")
     */
    public function show(Facture $facture): Response
    {
        $data = (array) json_decode($facture->getData());
        $data = $this->object_to_array($data);
        $total_price = 0;
        foreach ($data['_prestations'] as $key=>$value){
           // price_fixed
            $total_price+=$value['price_fixed'];

        }
        $date = date('Y_m_d-H-i-s');
        $cuurentdate = date('Y/m/d H:i:s');
        $filename = 'pdf/'.$facture->getId().'file.pdf';

        if (!file_exists($filename)) {
            $this->get('knp_snappy.pdf')->generateFromHtml(
                $this->renderView(
                    'facture/facture_template.html.twig',
                    array(
                        'facture'  => $data,
                        'cuurentdate' => $cuurentdate,
                        'total_price' => $total_price
                    )
                ),
                'pdf/'. $filename
            );
        }
       /**/
        return $this->render('facture/show.html.twig', ['facture' => $data,'file' => $filename]);
    }

    /**
     * @Route("/{id}/edit", name="facture_edit", methods="GET|POST")
     */
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('facture_edit', ['id' => $facture->getId()]);
        }

        return $this->render('facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="facture_delete", methods="DELETE")
     */
    public function delete(Request $request, Facture $facture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($facture);
            $em->flush();
        }

        return $this->redirectToRoute('facture_index');
    }
    public function  object_to_array($data)
    {
    if (is_array($data) || is_object($data))
    {
        $result = array();
        foreach ($data as $key => $value)
        {
            $result[$key] = $this->object_to_array($value);
        }
    return $result;
    }
    return $data;
}
}
