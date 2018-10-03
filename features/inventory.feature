Feature: Inventory
  Background:
    Given I add "content-type" header equal to "application/json"
    And I reset database
    And there is an inventory item "Item 1" of id "0f887823-fc4f-4eaa-b6a3-596031eb1f66" with 5 items
    And there is an inventory item "Item 2" of id "2a73b03c-2325-4b51-a2fe-3845a3b61a95" with 2 items
    And there is an inventory item "Item 3" of id "70971124-a2e2-47bb-8d0c-4ed4ed96f22b" with 15 items

  Scenario: List inventory items
    When I send a GET request to "/api/InventoryItems/"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "" should have 3 elements
    And the JSON node "[0].id" should be equal to "0f887823-fc4f-4eaa-b6a3-596031eb1f66"
    And the JSON node "[0].name" should be equal to "Item 1"

  Scenario: Get single inventory item
    When I send a GET request to "/api/InventoryItems/0f887823-fc4f-4eaa-b6a3-596031eb1f66"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should be equal to "0f887823-fc4f-4eaa-b6a3-596031eb1f66"
    And the JSON node "name" should be equal to "Item 1"
    And the JSON node "count" should be equal to "5"

  Scenario: Create inventory item
    When I send a POST request to "/api/InventoryItems/" with body:
    """
    {"name": "Item 4"}
    """
    And print response
    Then the response status code should be 202
    And the "Location" response header exists

  Scenario: Deactivate inventory item
    When I send a DELETE request to "/api/InventoryItems/0f887823-fc4f-4eaa-b6a3-596031eb1f66"
    Then the response status code should be 202

  Scenario: Rename inventory item
    Given I set "Content-Type" header equal to "application/json;domain-model=RenameInventoryItem"
    When I send a PUT request to "/api/InventoryItems/0f887823-fc4f-4eaa-b6a3-596031eb1f66" with body:
    """
    {"newName": "Item 1 updated"}
    """
    And print response
    Then the response status code should be 202

 Scenario: Check in inventory items
   Given I set "Content-Type" header equal to "application/json;domain-model=CheckInItemsToInventory"
   When I send a POST request to "/api/InventoryItems/0f887823-fc4f-4eaa-b6a3-596031eb1f66" with body:
   """
   {"count": 5}
   """
   And print response
   Then the response status code should be 202

 Scenario: Remove inventory item
   Given I set "Content-Type" header equal to "application/json;domain-model=RemoveItemsFromInventory"
   When I send a POST request to "/api/InventoryItems/0f887823-fc4f-4eaa-b6a3-596031eb1f66" with body:
   """
   {"count": 5}
   """
   And print response
   Then the response status code should be 202
