<?php

declare (strict_types=1);


use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\ReadModelProjector;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Ramsey\Uuid\Uuid;
use SimpleCQRS\Domain\InventoryItem;
use SimpleCQRS\Domain\InventoryItems;
use SimpleCQRS\Infrastructure\Projection\InventoryItem\InventoryItemProjection;
use SimpleCQRS\Infrastructure\Projection\InventoryItem\InventoryItemReadModel;
use Ubirak\RestApiBehatExtension\Json\JsonInspector;
use Ubirak\RestApiBehatExtension\Rest\RestApiBrowser;

class ApiContext implements Context
{
    private $restApiBrowser;

    private $jsonInspector;
    /**
     * @var InventoryItems
     */
    private $inventoryItems;
    /**
     * @var EventStore
     */
    private $eventStore;

    private $projectionManager;
    /**
     * @var InventoryItemReadModel
     */
    private $inventoryItemReadModel;
    /**
     * @var InventoryItemProjection
     */
    private $inventoryItemProjection;

    public function __construct(
        RestApiBrowser $restApiBrowser,
        JsonInspector $jsonInspector,
        InventoryItems $inventoryItems,
        EventStore $eventStore,
    ProjectionManager $projectionManager,
    InventoryItemReadModel  $inventoryItemReadModel,
    InventoryItemProjection $inventoryItemProjection
    )
    {
        $this->restApiBrowser = $restApiBrowser;
        $this->jsonInspector = $jsonInspector;
        $this->inventoryItems = $inventoryItems;
        $this->eventStore = $eventStore;
        $this->projectionManager = $projectionManager;
        $this->inventoryItemReadModel = $inventoryItemReadModel;
        $this->inventoryItemProjection = $inventoryItemProjection;
    }


    /**
     * @Given /^the "([^"]*)" response header exists$/
     */
    public function theResponseHeaderExists($header)
    {
        $response = $this->restApiBrowser->getResponse();

        if (!$response->hasHeader($header)) {
            throw new \Exception(sprintf('Response doesn\'t have header "%s"', $header));
        }
    }

    /**
     * @Given /^there is an inventory item "([^"]*)" of id "([^"]*)" with (\d+) items$/
     */
    public function thereIsAnInventoryItemWithItems(string $name, string $id, int $count)
    {
        try {
            $this->inventoryItems->getById(Uuid::fromString($id));
            return;
        } catch (\Exception $exception) {
            $item = InventoryItem::create(Uuid::fromString($id), $name);
            $item->checkIn($count);

            $this->inventoryItems->save($item);
        }
    }

    /**
     * @Given /^I reset database$/
     */
    public function iResetDatabase()
    {
        $this->eventStore->delete(new StreamName('event_stream'));

        $newStream = new Stream(new StreamName('event_stream'), new \ArrayIterator([]));
        $this->eventStore->create($newStream);

        $this->projectionManager->resetProjection('inventory_items_projection');
        $newProjection = $this->projectionManager->createReadModelProjection('inventory_items_projection', $this->inventoryItemReadModel);

        $this->inventoryItemProjection->project($newProjection);

        $this->inventoryItemReadModel->reset();
    }
}
