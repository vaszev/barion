# Barion smart gateway wrapper

### Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require vaszev/barion-bundle
```

### Contains

    Barion library (1.3.1 March 20. 2019.) wrapper for Symfony 4

Please note that this bundle is implemented for simple **B2C** Immediate payment type only. 

### Install

Add the following to your routing:

    /config/routes.yaml:
    
    ...
    
    _barion:
      resource: "@VaszevBarionBundle/Controller"
      type:     annotation
      prefix:   /barion

### Support

You have to gather your items into **one** transactions that could have **more items**:

    - PaymentRequestModel (1:PTM)
      - PaymentTransactionModel (PRQ:1)
        - ItemModel (PTM:N)
        - ItemModel
        - ItemModel
