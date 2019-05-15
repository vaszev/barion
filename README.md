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

Add the following to your routing:

    /config/routes.yaml:
    
    ...
    
    _barion:
      resource: "@VaszevBarionBundle/Controller"
      type:     annotation
      prefix:   /barion
      
Update doctrine's schema:

`$ php bin/console doctrine:schema:update --force`

Install assets:

`$ php bin/console assets:install --symlink`

### Support

You have to gather your items into **one** transactions that could have **more items**:

    - PaymentRequestModel (1:PTM)
      - PaymentTransactionModel (PRQ:1)
        - ItemModel (PTM:N)
        - ItemModel
        - ItemModel
