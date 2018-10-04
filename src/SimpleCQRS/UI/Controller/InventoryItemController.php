<?php

declare(strict_types=1);

namespace SimpleCQRS\UI\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use Prooph\ServiceBus\CommandBus;
use SimpleCQRS\Application\Command\CheckInItemsToInventory;
use SimpleCQRS\Application\Command\CreateInventoryItem;
use SimpleCQRS\Application\Command\DeactivateInventoryItem;
use SimpleCQRS\Application\Command\RemoveItemsFromInventory;
use SimpleCQRS\Application\Command\RenameInventoryItem;
use SimpleCQRS\Application\Query\InventoryItemDetailsDTO;
use SimpleCQRS\Application\Query\InventoryItemListDTO;
use SimpleCQRS\Application\Query\InventoryItemsQueryService;
use SimpleCQRS\UI\ParamConverter\Command;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/InventoryItems")
 *
 * Class InventoryItemController
 */
final class InventoryItemController
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var InventoryItemsQueryService
     */
    private $queryService;

    public function __construct(CommandBus $commandBus, InventoryItemsQueryService $queryService)
    {
        $this->commandBus = $commandBus;
        $this->queryService = $queryService;
    }

    /**
     * @Route(path="/", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of inventory items",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=InventoryItemListDTO::class, groups={"full"}))
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return new JsonResponse($this->queryService->getAll());
    }

    /**
     * @Route(path="/{id}", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns the details of inventory item",
     *     @Model(type=InventoryItemDetailsDTO::class)
     * )
     *
     * @return JsonResponse
     */
    public function get(string $id): JsonResponse
    {
        $result = $this->queryService->getById($id);

        if (!$result) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($result);
    }

    /**
     * @Route(path="/", methods={"POST"})
     *
     * @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     required=true,
     *     description="JSON payload",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="name", type="string", maxLength=255, example="Item 1 name")
     *      )
     * )
     *
     * @SWG\Response(
     *     response=202,
     *     description="Returns the HTTP Accepted code on success",
     *     @SWG\Header(header="Location", description="URL of created item ie. /api/InventoryItems/1234-1234-123 ", type="string")
     * )
     * @Command("command", uuid="id")
     *
     * @return Response
     */
    public function create(CreateInventoryItem $command)
    {
        $this->commandBus->dispatch($command);

        return new JsonResponse(null, 202, [
            'Location' => '/api/InventoryItems/'.$command->id()->toString(),
        ]);
    }

    /**
     * @Route(path="/{id}", methods={"PUT"}, condition="request.attributes.get('_domain_model') == 'RenameInventoryItem'")
     *
     * @SWG\Parameter(
     *     in="header",
     *     name="Content-Type",
     *     type="string",
     *     required=true,
     *     enum={"application/json;domain-model=RenameInventoryItem"},
     *     description="Content-type header must contain a media type with specified domain-model, accepted models are:
    <ul><li>RenameInventoryItem</li></ul>
    Example value: <code>application/json;domain-model=RenameInventoryItem</code>"
     * )
     * @SWG\Parameter(
     *     in="body",
     *     name="body - RenameInventoryItem",
     *     required=true,
     *     description="JSON payload",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="name", type="string", example="New name", maxLength=255)
     *      ),
     * )
     * @SWG\Response(
     *     response=202,
     *     description="Returns the HTTP Accepted code on success"
     * )
     * @Command("command", routeParams=true)
     */
    public function rename(RenameInventoryItem $command)
    {
        $this->commandBus->dispatch($command);

        return new Response('', 202);
    }

    /**
     * Method for API documentation purposes.
     *
     * @SWG\Post(summary="")
     * @Route(path="/{id}", methods={"POST"}, condition="false")
     *
     * @SWG\Parameter(
     *     in="header",
     *     name="Content-Type",
     *     type="string",
     *     required=true,
     *     enum={"application/json;domain-model=CheckInItemsToInventory"},
     *     description="Content-type header must contain a media type with specified domain-model, accepted models are:
    <ul><li>CheckInItemsToInventory</li><li>RemoveItemsFromInventory</li></ul>
    Example value: <code>application/json;domain-model=CheckInItemsToInventory</code>"
     * )
     * @SWG\Parameter(
     *     in="body",
     *     name="body - CheckInItemsToInventory",
     *     required=true,
     *     description="JSON payload",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="count", type="int", example=5)
     *      ),
     * )
     * @SWG\Parameter(
     *     in="body",
     *     name="body - RemoveItemsFromInventory",
     *     required=true,
     *     description="JSON payload",
     *     @SWG\Schema(
     *          type="object",
     *          @SWG\Property(property="count", type="int", example=15)
     *      )
     * ),
     * @SWG\Response(
     *     response=202,
     *     description="Returns the HTTP Accepted code on success"
     * )
     */
    public function post()
    {
    }

    /**
     * @Route(path="/{id}", methods={"POST"}, condition="request.attributes.get('_domain_model') == 'CheckInItemsToInventory'")
     *
     * @Command("command", routeParams={"id"})
     */
    public function checkInItems(CheckInItemsToInventory $command)
    {
        $this->commandBus->dispatch($command);

        return new Response('', 202);
    }

    /**
     * @Route(path="/{id}", methods={"POST"}, condition="request.attributes.get('_domain_model') == 'RemoveItemsFromInventory'")
     *
     * @Command("command", routeParams={"id": "newId"})
     */
    public function removeItems(RemoveItemsFromInventory $command)
    {
        $this->commandBus->dispatch($command);

        return new Response('', 202);
    }

    /**
     * @Route(path="/{id}", methods={"DELETE"})
     *
     * @Command("command", routeParams=true)
     */
    public function deactivate(DeactivateInventoryItem $command)
    {
        $this->commandBus->dispatch($command);

        return new Response('', 202);
    }
}
