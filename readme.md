Mothership Ecommerce
===

Product Page Mapper
---

The ecommerce package ships with two implementations of the product page mapper: `SimpleMapper` and
`OptionCriteriaMapper`. By default the `SimpleMapper` is aliased to `product.page_mapper`.

**Configuration**

To enable the mapper to correctly relate products to pages you must set the valid values for
`product_content.field_name` and `product_content.group_name` for product pages. You can change these using:

```php
$services['product.page_mapper']->setValidFieldName('product');

// Passing an array to either method will match against all values
$services['product.page_mapper']->setValidGroupName(['product', 'showcase']);

// Passing null to the group name will remove it from the relationship
$services['product.page_mapper']->setValidGroupName(null);
```

By default these values are set to `'product'` and `null` respectively.


### SimpleMapper

The simple mapper just matches a basic product to a page.

**Usage**

```php
// Find a page from a product
$page = $services['product.page_mapper']->getPageForProduct($product);

// Find a product from a page
$product = $services['product.page_mapper']->getProductForPage($page);
```


### OptionCriteriaMapper

The option criteria mapper can additionally apply a filter for a specific product option, for example `['colour' => 'red']`. You can pass any number of options: `['colour' => 'red', 'size' => 'medium']`.

To enable the option criteria mapper you must alias it to the page mapper in your services:

```php
$services['product.page_mapper'] = $services['product.page_mapper.option_criteria'];
```

**Usage**

In addition to the previous methods, you can also call:

```php
// Find all pages from a product
$pages = $services['product.page_mapper']->getPagesForProduct($product);

// Find a page from a unit
$page = $services['product.page_mapper']->getPageForUnit($unit);

// Find units from a page
$units = $services['product.page_mapper']->getUnitsForPage($page);
```