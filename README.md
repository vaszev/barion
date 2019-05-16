# Barion smart gateway wrapper

### Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require vaszev/barion-bundle
```

### Contains

    Barion library (1.3.1 March 20. 2019.) wrapper for Symfony 4

Please note that this bundle is for simple **B2C** Immediate payment type only. Project still *in development stage, use only at your own risk!* 

### Install

Configure your credentials:

`/config/vaszev.yaml`

```yaml
vaszev_barion:
  posKey: 'YOUR_POS_KEY_FROM_BARION'
  apiVersion: 2
  sandbox: true
  payee: 'YOUR.SHOP.EMAIL@EXAMPLE.COM'
  pixelId: 'YOUR_BARION_PIXEL_ID'
  webshopName: 'YOUR_WEBSHOP_PREFIX_YOU_WANT_TO_USE'
  webshopDefaultRoute: 'YOUR_WEBSHOP_ROUTE_FOR_BACK_BUTTON_IF_NOT_AVAILABLE'
  waitingRoomBg: '#fff'
  waitingRoomColor: '#333'
  waitingRoomAmountColor: 'tomato'
  waitingRoomPositiveFeedbackColor: 'green'
  waitingRoomNegativeFeedbackColor: 'red'
  waitingRoomNeturalFeedbackColor: 'orange'
  waitingRoomGoogleFont: 'Lato'
```

Add the following to your routing:

`/config/routes.yaml`

```yaml
_barion:
  resource: "@VaszevBarionBundle/Controller"
  type:     annotation
  prefix:   /barion
```
      
Update doctrine's schema:

`$ php bin/console doctrine:schema:update --force`

Install assets:

`$ php bin/console assets:install --symlink`

Include Barion's pixel into your webshop pages:

```twig
{{ render(controller('Vaszev\\BarionBundle\\Controller\\BarionController::pixel')) }}
```

Adding translations :

```yaml
'barion.cart': ''
'barion.transaction': ''
'barion.done': ''
'barion.Your.payment.of': ''
'barion.transaction.failed': ''
'barion.transaction.success': ''
'barion.transaction.neutral': ''
'barion.back.to.the.webshop': ''
```

### Example

You have to gather your items into **one** transactions that could have **more items**:

```php
$myWebsopTransactionId = 8211;
$redirectURL = $this->generateUrl('webshop', [], UrlGeneratorInterface::ABSOLUTE_URL);
try {
  $barion->initShopping($redirectURL)->createTransaction($myWebsopTransactionId, 'Please post it ASAP!');
  $barion->addItem('Product name','Description',2,2900,'ProdId#5312','Piece');
  $barion->addItem('Product name 2','Description so far',1,1000,'ProdId#4362','Meter');
  $barion->addItem('Product name 3','Description will fit',1,5900,'ProdId#7309','L');
  $payURL = $barion->preparePaymentRequest('buyer@example.com', '1234 Hungary, Budapest...')->closeAndGetPaymentURL();

  ...

} catch (\Exception $e) {
  // yikes! something went wrong...
}
```

Check if your order's payment received:

```php
$barion->checkMyOrderBeingPaid($myWebsopTransactionId);
```

Call getters on your order:

```php
$barion->getMyOrderPaymentId($myWebsopTransactionId);
$barion->getMyOrderResponse($myWebsopTransactionId);
$barion->getMyOrderItems($myWebsopTransactionId);
$barion->getMyOrderRequest($myWebsopTransactionId);
$barion->getMyOrderTransaction($myWebsopTransactionId);
$barion->getMyOrderStateResponses($myWebsopTransactionId);
```
