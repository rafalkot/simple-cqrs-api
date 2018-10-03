<?php

declare(strict_types=1);

namespace SimpleCQRS\UI\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RequestTransformerListener.
 *
 * @see https://github.com/symfony-bundles/json-request-bundle/blob/master/EventListener/RequestTransformerListener.php
 */
class RequestTransformerListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (false === $this->isAvailable($request)) {
            return;
        }

        if (false === $this->transformContent($request)) {
            $response = Response::create('Unable to parse request.', 400);
            $event->setResponse($response);
        }

        $this->transformDomainModel($request);
    }

    private function isAvailable(Request $request)
    {
        return 'json' === $request->getContentType() && $request->getContent();
    }

    private function transformContent(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }

        if (is_array($data)) {
            $request->request->replace($data);
        }

        return true;
    }

    private function transformDomainModel(Request $request)
    {
        $contentType = $request->headers->get('CONTENT_TYPE');

        if (!$contentType) {
            return;
        }

        $headerParts = $this->parseHeaderValue($contentType);

        if (!isset($headerParts['domain-model'])) {
            return;
        }

        $request->attributes->set('_domain_model', $headerParts['domain-model']);
    }

    /**
     * @author Christian Weiske <christian.weiske@netresearch.de>
     */
    private function parseHeaderValue(string $headerValue)
    {
        if (false === strpos($headerValue, ';')) {
            return ['_value' => $headerValue];
        }

        $parts = explode(';', $headerValue);
        $arData = ['_value' => array_shift($parts)];

        foreach ($parts as $part) {
            list($name, $value) = explode('=', $part);
            $arData[$name] = trim($value, ' "\'');
        }

        return $arData;
    }
}
