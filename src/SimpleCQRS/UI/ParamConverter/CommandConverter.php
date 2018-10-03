<?php

declare(strict_types=1);

namespace SimpleCQRS\UI\ParamConverter;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\MessageFactory;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

final class CommandConverter implements ParamConverterInterface
{
    private $messageFactory;

    public function __construct(MessageFactory $messageFactory)
    {
        $this->messageFactory = $messageFactory;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $payload = $request->request->all();
        $className = $configuration->getClass();

        $this->processOnly($request, $configuration, $payload);
        $this->processUuid($request, $configuration, $payload);
        $this->processRouteParameters($request, $configuration, $payload);
        $this->processExclude($request, $configuration, $payload);

        $command = $this->messageFactory->createMessageFromArray($className, [
            'payload' => $payload,
        ]);

        $request->attributes->set($configuration->getName(), $command);
    }

    public function supports(ParamConverter $configuration)
    {
        return is_a($configuration->getClass(), Command::class, true);
    }

    private function processUuid(Request $request, ParamConverter $configuration, array &$payload)
    {
        $options = $configuration->getOptions();

        if (!array_key_exists('uuid', $options)) {
            return;
        }

        if (!is_array($options['uuid'])) {
            $options['uuid'] = [$options['uuid']];
        }

        foreach ($options['uuid'] as $uuidAttribute) {
            $payload[$uuidAttribute] = Uuid::uuid4()->toString();
        }
    }

    private function processRouteParameters(Request $request, ParamConverter $configuration, array &$payload)
    {
        $options = $configuration->getOptions();

        if (!array_key_exists('route_params', $options)) {
            return;
        }

        $routeParams = $request->attributes->get('_route_params', []);

        if (true === $options['route_params']) {
            foreach ($routeParams as $param => $value) {
                $payload[$param] = $value;
            }

            return;
        }

        if (!is_array($routeParams)) {
            throw new \LogicException('`route_params` options should be of boolean or array type');
        }

        if ($this->isAssoc($options['route_params'])) {
            foreach ($options['route_params'] as $routeParam => $payloadParam) {
                $payload[$payloadParam] = $routeParams[$routeParam];
            }

            return;
        }

        foreach ($options['route_params'] as $param) {
            $payload[$param] = $routeParams[$param];
        }
    }

    private function processOnly(Request $request, ParamConverter $configuration, array &$payload)
    {
        $options = $configuration->getOptions();

        if (!array_key_exists('only', $options)) {
            return;
        }

        $newPayload = [];

        foreach ($options['only'] as $param) {
            $newPayload[$param] = $payload[$param];
        }

        $payload = $newPayload;
    }

    private function processExclude(Request $request, ParamConverter $configuration, array &$payload)
    {
        $options = $configuration->getOptions();

        if (!array_key_exists('exclude', $options)) {
            return;
        }

        foreach ($options['exclude'] as $param) {
            if (array_key_exists($payload, $param)) {
                unset($payload[$param]);
            }
        }
    }

    private function isAssoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
