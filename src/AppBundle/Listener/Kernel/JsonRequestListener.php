<?php
namespace AppBundle\Listener\Kernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Observe;

/**
 * @Service("tracy.request_listener")
 */
class JsonRequestListener
{
    /**
     * @Observe("kernel.request", priority = 255)
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (! $this->isJsonRequest($request)) {
            return;
        }

        if (! $this->transformJsonBody($request)) {
            $response = Response::create('Unable to parse request.', 400);
            $event->setResponse($response);
        }
    }

    private function isJsonRequest(Request $request)
    {
        return 'json' === $request->getContentType();
    }

    private function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) 
        {
            return false;
        }

        if ($data === null) 
        {
            return true;
        }
        $request->request->replace($data);
        return true;
    }
}