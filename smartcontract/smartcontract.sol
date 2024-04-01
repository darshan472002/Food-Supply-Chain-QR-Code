// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract SupplyChain1 {
  event Added(uint256 index);

  struct State {
    string description;
    string locationName;
    address person;
  }

  struct Product {
    address creator;
    string productName;
    uint256 productId;
    string manufactureDate;
    string expiryDate;
    uint256 totalStates;
    mapping(uint256 => State) positions;
  }

  mapping(uint => Product) allProducts;
  uint256 items = 0;

  function concat(
    string memory _a,
    string memory _b
  ) public pure returns (string memory) {
    return string(abi.encodePacked(_a, _b));
  }

  function newItem(
    string memory _text,
    string memory _mdate,
    string memory _edate
  ) public returns (bool) {
    Product storage newProduct = allProducts[items];
    newProduct.creator = msg.sender;
    newProduct.totalStates = 0;
    newProduct.productName = _text;
    newProduct.productId = items;
    newProduct.manufactureDate = _mdate;
    newProduct.expiryDate = _edate;
    items++;
    emit Added(items - 1);
    return true;
  }

  function addState(
    uint _productId,
    string memory info,
    string memory lname
  ) public returns (string memory) {
    require(_productId <= items);
    Product storage product = allProducts[_productId];
    product.positions[product.totalStates] = State({
      person: msg.sender,
      description: info,
      locationName: lname
    });
    product.totalStates++;
    return info;
  }

  function searchProduct(uint _productId) public view returns (string memory) {
    require(_productId <= items);
    Product storage product = allProducts[_productId];
    string memory output = "";

    output = concat(output, product.productName);
    output = concat(output, product.manufactureDate);
    output = concat(output, product.expiryDate);

    for (uint256 j = 0; j < product.totalStates; j++) {
      output = concat(output, product.positions[j].description);
    }

    return output;
  }
}
