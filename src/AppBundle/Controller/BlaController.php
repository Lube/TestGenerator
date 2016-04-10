<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\Serializer\SerializationContext;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use AppBundle\Entity\Bla;

/**
 * @Route("/api/bla")
 */
class BlaController extends Controller
{
  /** @Inject("em") */
  private $em;

  /** @Inject("serializer") */
  private $serializer;

  
  /** 
   * @Route("")
   * @Method({"GET"})
   * @ApiDoc(description="Devueve un/a lista de Blas, la ruta indica los items a devolver")
   */
  public function cgetAction(Request $request) 
  {
      $offset = $request->query->get('offset',    null);
      $limit  = $request->query->get('limit',     null);
      $filter = json_decode($request->query->get('filter_by', array()), true);
      $order  = $request->query->get('order_by',  null);

      $blas  = $this->em->getRepository('AppBundle:Bla')->findBy($filter, $order, $limit, $offset);

      $Response['data']   = json_decode($this->serializer->serialize($blas, 'json'));
      $Response['total']  = count($this->em->getRepository('AppBundle:Bla')->findBy($filter));

      return new JsonResponse($Response);
  }
  
  /** 
   * @Route("/{id}", requirements={"id" = "\d+"})
   * @Method({"GET"})
   * @ParamConverter("bla", class="AppBundle:Bla")
   * @ApiDoc(
   *  description="Retorna un/a Bla segun la id provista",
   *  requirements={
   *      {
   *          "name"="id",
   *          "dataType"="integer",
   *          "requirement"="\d+",
   *          "description"="Id de la Bla a retornar"
   *      }
   *  }
   * )
   */
  public function getAction($bla)
  {
    return new JsonResponse(json_decode($this->serializer->serialize($bla, 'json')));
  }
  
  /** 
   * @Route(" ")
   * @Method({"POST"})
   * @ApiDoc(
   *  description="Crea un/a Bla"
   * )
   */
  public function saveAction(Request $request)
  {
    $bla = $this->serializer->deserialize($request->getContent(), 'AppBundle\Entity\Bla', 'json');
    
    $errors = $this->get('validator')->validate($bla);
    if (count($errors) > 0) 
    {
        foreach ($errors->getIterator() as $error) 
        {
            $errorMessages[] = $error->getMessage();
        }

        return new JsonResponse($errorResponse, 400);
    }

    $this->em->persist($bla);
    $this->em->flush();

    return new JsonResponse(json_decode($this->serializer->serialize($bla, 'json')));
  }
  
  /** 
   * @Route("/{id}", requirements={"id" = "\d+"})
   * @Method({"DELETE"})
   * @ParamConverter("bla", class="AppBundle:Bla")
   * @ApiDoc(
   *  description="Destruye un/a Bla"
   * )
   */
  public function removeAction(Request $request, $bla)
  {
    $this->em->remove($bla);
    $this->em->flush();

    return new JsonResponse();
  }
  
  /** 
   * @Route("/{id}", requirements={"id" = "\d+"})
   * @Method({"POST"})
   * @ParamConverter("bla", class="AppBundle:Bla")
   * @ApiDoc(
   *  description="Edita un/a Bla"
   * )
   */
  public function updateAction(Request $request, $bla)
  {
    $context = new \JMS\Serializer\DeserializationContext();
    $context->attributes->set('target', $bla);

    $bla = $this->serializer->deserialize($request->getContent(), 'AppBundle\Entity\Bla', 'json', $context);
    
    $errors = $this->get('validator')->validate($bla);
    if (count($errors) > 0) 
    {
        foreach ($errors->getIterator() as $error) 
        {
            $errorMessages[] = $error->getMessage();
        }

        return new JsonResponse($errorResponse, 400);
    }

    $this->em->persist($bla);
    $this->em->flush();

    return new JsonResponse(json_decode($this->serializer->serialize($bla, 'json')));
  }
  
}
