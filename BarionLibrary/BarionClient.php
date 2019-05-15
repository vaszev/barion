<?php

namespace Vaszev\BarionBundle\BarionLibrary;

use Vaszev\BarionBundle\Service\BarionEnvironment;
use Vaszev\BarionBundle\Service\Currency;
use Vaszev\BarionBundle\Service\FundingSourceType;
use Vaszev\BarionBundle\Service\PaymentType;
use Vaszev\BarionBundle\Service\QRCodeSize;

/**
 * Barion library (1.3.1 March 20. 2019.) wrapper for Symfony 4
 */

/**
 * Copyright 2016 Barion Payment Inc. All Rights Reserved.
 * <p/>
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * <p/>
 * http://www.apache.org/licenses/LICENSE-2.0
 * <p/>
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
*  
*  BarionClient.php
*  PHP library for implementing REST API calls towards the Barion payment system.  
*  
*/

DEFINE("BARION_API_URL_PROD", "https://api.barion.com");
DEFINE("BARION_WEB_URL_PROD", "https://secure.barion.com/Pay");
DEFINE("BARION_API_URL_TEST", "https://api.test.barion.com");
DEFINE("BARION_WEB_URL_TEST", "https://secure.test.barion.com/Pay");
DEFINE("API_ENDPOINT_PREPAREPAYMENT", "/Payment/Start");
DEFINE("API_ENDPOINT_PAYMENTSTATE", "/Payment/GetPaymentState");
DEFINE("API_ENDPOINT_QRCODE", "/QR/Generate");
DEFINE("API_ENDPOINT_REFUND", "/Payment/Refund");
DEFINE("API_ENDPOINT_FINISHRESERVATION", "/Payment/FinishReservation");
DEFINE("PAYMENT_URL", "/Pay");


interface iBarionModel {
  public function fromJson($json);
}

/**
 * Gets the value of the specified property from the json
 * @param string $json The json
 * @param string $propertyName
 * @return null The value of the property
 */
function jget($json, $propertyName) {
  return isset($json[$propertyName]) ? $json[$propertyName] : null;
}


class ApiErrorModel {
  public $ErrorCode;
  public $Title;
  public $Description;



  function __construct() {
    $this->ErrorCode = "";
    $this->Title = "";
    $this->Description = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->ErrorCode = $json['ErrorCode'];
      $this->Title = $json['Title'];
      $this->Description = $json['Description'];
    }
  }
}

class BankCardModel implements iBarionModel {
  public $MaskedPan;
  public $BankCardType;
  public $ValidThruYear;
  public $ValidThruMonth;



  function __construct() {
    $this->MaskedPan = "";
    $this->BankCardType = "";
    $this->ValidThruYear = "";
    $this->ValidThruMonth = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->MaskedPan = jget($json, 'MaskedPan');
      $this->BankCardType = jget($json, 'BankCardType');
      $this->ValidThruYear = jget($json, 'ValidThruYear');
      $this->ValidThruMonth = jget($json, 'ValidThruMonth');
    }
  }
}

class BaseRequestModel {
  // for poskey authentication
  public $POSKey;

}

class BaseResponseModel {
  public $Errors;
  public $RequestSuccessful;



  function __construct() {
    $this->Errors = [];
    $this->RequestSuccessful = false;
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->RequestSuccessful = true;
      if (!array_key_exists('Errors', $json) || !empty($json['Errors'])) {
        $this->RequestSuccessful = false;
      }

      if (array_key_exists('Errors', $json)) {
        foreach ($json['Errors'] as $error) {
          $apiError = new ApiErrorModel();
          $apiError->fromJson($error);
          array_push($this->Errors, $apiError);
        }
      } else {
        $internalError = new ApiErrorModel();
        $internalError->ErrorCode = "500";
        if (array_key_exists('ExceptionMessage', $json)) {
          $internalError->Title = $json['ExceptionMessage'];
          $internalError->Description = $json['ExceptionType'];
        } else {
          $internalError->Title = "Internal Server Error";
        }

        array_push($this->Errors, $internalError);
      }
    }
  }
}

class FinishReservationRequestModel extends BaseRequestModel {
  public $PaymentId;
  public $Transactions;



  function __construct($paymentId) {
    $this->PaymentId = $paymentId;
    $this->Transactions = [];
  }



  public function AddTransaction(TransactionToFinishModel $transaction) {
    if ($this->Transactions == null) {
      $this->Transactions = [];
    }
    array_push($this->Transactions, $transaction);
  }



  public function AddTransactions($transactions) {
    if (!empty($transactions)) {
      foreach ($transactions as $transaction) {
        if ($transaction instanceof TransactionToFinishModel) {
          $this->AddTransaction($transaction);
        }
      }
    }
  }
}

class FinishReservationResponseModel extends BaseResponseModel implements iBarionModel {
  public $IsSuccessful;
  public $PaymentId;
  public $PaymentRequestId;
  public $Status;
  public $Transactions;



  function __construct() {
    parent::__construct();
    $this->IsSuccessful = false;
    $this->PaymentId = "";
    $this->PaymentRequestId = "";
    $this->Status = "";
    $this->Transactions = [];
  }



  public function fromJson($json) {
    if (!empty($json)) {
      parent::fromJson($json);

      $this->IsSuccessful = jget($json, 'IsSuccessful');
      $this->PaymentId = jget($json, 'PaymentId');
      $this->PaymentRequestId = jget($json, 'PaymentRequestId');
      $this->Status = jget($json, 'Status');

      $this->Transactions = [];

      if (!empty($json['Transactions'])) {
        foreach ($json['Transactions'] as $key => $value) {
          $tr = new TransactionResponseModel();
          $tr->fromJson($value);
          array_push($this->Transactions, $tr);
        }
      }
    }
  }
}

class FundingInformationModel implements iBarionModel {
  public $BankCard;
  public $AuthorizationCode;



  function __construct() {
    $this->BankCard = new BankCardModel();
    $this->AuthorizationCode = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->BankCard = new BankCardModel();
      $this->BankCard->fromJson(jget($json, 'BankCard'));
      $this->AuthorizationCode = jget($json, 'AuthorizationCode');
    }
  }
}

class ItemModel implements iBarionModel {
  public $Name;
  public $Description;
  public $Quantity;
  public $Unit;
  public $UnitPrice;
  public $ItemTotal;
  public $SKU;



  function __construct() {
    $this->Name = "";
    $this->Description = "";
    $this->Quantity = 0;
    $this->Unit = "";
    $this->UnitPrice = 0;
    $this->ItemTotal = 0;
    $this->SKU = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->Name = jget($json, 'Name');
      $this->Description = jget($json, 'Description');
      $this->Quantity = jget($json, 'Quantity');
      $this->Unit = jget($json, 'Unit');
      $this->UnitPrice = jget($json, 'UnitPrice');
      $this->ItemTotal = jget($json, 'ItemTotal');
      $this->SKU = jget($json, 'SKU');
    }
  }
}

class PayeeTransactionModel {
  public $POSTransactionId;
  public $Payee;
  public $Total;
  public $Comment;



  function __construct() {
    $this->POSTransactionId = "";
    $this->Payee = "";
    $this->Total = 0;
    $this->Comment = "";
  }
}

class PayeeTransactionToFinishModel {
  public $TransactionId;
  public $Total;
  public $Comment;



  function __construct() {
    $this->TransactionId = "";
    $this->Total = 0;
    $this->Comment = "";
  }
}

class PaymentQRRequestModel extends BaseRequestModel {
  public $UserName;
  public $Password;
  public $PaymentId;
  public $Size;



  function __construct($paymentId) {
    $this->PaymentId = $paymentId;
    $this->Size = QRCodeSize::Normal;
  }
}

class PaymentStateRequestModel extends BaseRequestModel {
  public $PaymentId;



  function __construct($paymentId) {
    $this->PaymentId = $paymentId;
  }
}

class PaymentStateResponse extends BaseResponseModel implements iBarionModel {
  public $PaymentId;
  public $PaymentRequestId;
  public $OrderNumber;
  public $POSId;
  public $POSName;
  public $POSOwnerEmail;
  public $Status;
  public $PaymentType;
  public $FundingSource;
  public $FundingInformation;
  public $AllowedFundingSources;
  public $GuestCheckout;
  public $CreatedAt;
  public $ValidUntil;
  public $CompletedAt;
  public $ReservedUntil;
  public $Total;
  public $Currency;
  public $Transactions;
  public $RecurrenceResult;
  public $SuggestedLocale;
  public $FraudRiskScore;
  public $RedirectUrl;
  public $CallbackUrl;



  function __construct() {
    parent::__construct();
    $this->PaymentId = "";
    $this->PaymentRequestId = "";
    $this->OrderNumber = "";
    $this->POSId = "";
    $this->POSName = "";
    $this->POSOwnerEmail = "";
    $this->Status = "";
    $this->PaymentType = "";
    $this->FundingSource = "";
    $this->FundingInformation = new FundingInformationModel();
    $this->AllowedFundingSources = "";
    $this->GuestCheckout = "";
    $this->CreatedAt = "";
    $this->ValidUntil = "";
    $this->CompletedAt = "";
    $this->ReservedUntil = "";
    $this->Total = 0;
    $this->Currency = "";
    $this->Transactions = [];
    $this->RecurrenceResult = "";
    $this->SuggestedLocale = "";
    $this->FraudRiskScore = 0;
    $this->RedirectUrl = "";
    $this->CallbackUrl = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      parent::fromJson($json);

      $this->PaymentId = jget($json, 'PaymentId');
      $this->PaymentRequestId = jget($json, 'PaymentRequestId');
      $this->OrderNumber = jget($json, 'OrderNumber');
      $this->POSId = jget($json, 'POSId');
      $this->POSName = jget($json, 'POSName');
      $this->POSOwnerEmail = jget($json, 'POSOwnerEmail');
      $this->Status = jget($json, 'Status');
      $this->PaymentType = jget($json, 'PaymentType');
      $this->FundingSource = jget($json, 'FundingSource');
      if (!empty($json['FundingInformation'])) {
        $this->FundingInformation = new FundingInformationModel();
        $this->FundingInformation->fromJson(jget($json, 'FundingInformation'));
      }
      $this->AllowedFundingSources = jget($json, 'AllowedFundingSources');
      $this->GuestCheckout = jget($json, 'GuestCheckout');
      $this->CreatedAt = jget($json, 'CreatedAt');
      $this->ValidUntil = jget($json, 'ValidUntil');
      $this->CompletedAt = jget($json, 'CompletedAt');
      $this->ReservedUntil = jget($json, 'ReservedUntil');
      $this->Total = jget($json, 'Total');
      $this->Currency = jget($json, 'Currency');
      $this->RecurrenceResult = jget($json, 'RecurrenceResult');
      $this->SuggestedLocale = jget($json, 'SuggestedLocale');
      $this->FraudRiskScore = jget($json, 'FraudRiskScore');
      $this->RedirectUrl = jget($json, 'RedirectUrl');
      $this->CallbackUrl = jget($json, 'CallbackUrl');

      $this->Transactions = [];

      if (!empty($json['Transactions'])) {
        foreach ($json['Transactions'] as $key => $value) {
          $tr = new TransactionDetailModel();
          $tr->fromJson($value);
          array_push($this->Transactions, $tr);
        }
      }

    }
  }
}

class PaymentTransactionModel {
  public $POSTransactionId;
  public $Payee;
  public $Total;
  public $Comment;
  public $Items;
  public $PayeeTransactions;



  function __construct() {
    $this->POSTransactionId = "";
    $this->Payee = "";
    $this->Total = 0;
    $this->Comment = "";
    $this->Items = [];
    $this->PayeeTransactions = [];
  }



  public function AddItem(ItemModel $item) {
    if ($this->Items == null) {
      $this->Items = [];
    }
    array_push($this->Items, $item);
  }



  public function AddItems($items) {
    if (!empty($items)) {
      foreach ($items as $item) {
        if ($item instanceof ItemModel) {
          $this->AddItem($item);
        }
      }
    }
  }



  public function AddPayeeTransaction(PayeeTransactionModel $model) {
    if ($this->PayeeTransactions == null) {
      $this->PayeeTransactions = [];
    }
    array_push($this->PayeeTransactions, $model);
  }



  public function AddPayeeTransactions($transactions) {
    if (!empty($transactions)) {
      foreach ($transactions as $transaction) {
        if ($transaction instanceof PayeeTransactionModel) {
          $this->AddPayeeTransaction($transaction);
        }
      }
    }
  }
}

class PreparePaymentRequestModel extends BaseRequestModel {
  public $PaymentType;
  public $ReservationPeriod;
  public $PaymentWindow;
  public $GuestCheckout;
  public $FundingSources;
  public $PaymentRequestId;
  public $PayerHint;
  public $Transactions;
  public $Locale;
  public $OrderNumber;
  public $ShippingAddress;
  public $InitiateRecurrence;
  public $RecurrenceId;
  public $RedirectUrl;
  public $CallbackUrl;
  public $Currency;



  function __construct($requestId = null, $type = PaymentType::Immediate, $guestCheckoutAllowed = true, $allowedFundingSources = [FundingSourceType::All], $window = "00:30:00", $locale = "hu-HU", $initiateRecurrence = false, $recurrenceId = null, $redirectUrl = null, $callbackUrl = null, $currency = Currency::HUF) {
    $this->PaymentRequestId = $requestId;
    $this->PaymentType = $type;
    $this->PaymentWindow = $window;
    $this->GuestCheckout = true;
    $this->FundingSources = [FundingSourceType::All];
    $this->Locale = $locale;
    $this->InitiateRecurrence = $initiateRecurrence;
    $this->RecurrenceId = $recurrenceId;
    $this->RedirectUrl = $redirectUrl;
    $this->CallbackUrl = $callbackUrl;
    $this->Currency = $currency;
  }



  public function AddTransaction(PaymentTransactionModel $transaction) {
    if ($this->Transactions == null) {
      $this->Transactions = [];
    }
    array_push($this->Transactions, $transaction);
  }



  public function AddTransactions($transactions) {
    if (!empty($transactions)) {
      foreach ($transactions as $transaction) {
        if ($transaction instanceof PaymentTransactionModel) {
          $this->AddTransaction($transaction);
        }
      }
    }
  }
}

class PreparePaymentResponseModel extends BaseResponseModel implements iBarionModel {
  public $PaymentId;
  public $PaymentRequestId;
  public $Status;
  public $Transactions;
  public $QRUrl;
  public $RecurrenceResult;
  public $PaymentRedirectUrl;



  function __construct() {
    parent::__construct();
    $this->PaymentId = "";
    $this->PaymentRequestId = "";
    $this->Status = "";
    $this->QRUrl = "";
    $this->RecurrenceResult = "";
    $this->PaymentRedirectUrl = "";
    $this->Transactions = [];
  }



  public function fromJson($json) {
    if (!empty($json)) {
      parent::fromJson($json);
      $this->PaymentId = jget($json, 'PaymentId');
      $this->PaymentRequestId = jget($json, 'PaymentRequestId');
      $this->Status = jget($json, 'Status');
      $this->QRUrl = jget($json, 'QRUrl');
      $this->RecurrenceResult = jget($json, 'RecurrenceResult');
      $this->Transactions = [];

      if (!empty($json['Transactions'])) {
        foreach ($json['Transactions'] as $key => $value) {
          $tr = new TransactionResponseModel();
          $tr->fromJson($value);
          array_push($this->Transactions, $tr);
        }
      }

    }
  }
}

class RefundedTransactionModel implements iBarionModel {

  public $TransactionId;
  public $Total;
  public $POSTransactionId;
  public $Comment;
  public $Status;



  function __construct() {
    $this->TransactionId = "";
    $this->Total = 0;
    $this->POSTransactionId = "";
    $this->Comment = "";
    $this->Status = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->TransactionId = $json['TransactionId'];
      $this->Total = $json['Total'];
      $this->POSTransactionId = $json['POSTransactionId'];
      $this->Comment = $json['Comment'];
      $this->Status = $json['Status'];
    }
  }
}

class RefundRequestModel extends BaseRequestModel {
  public $PaymentId;
  public $TransactionsToRefund;



  function __construct($paymentId) {
    $this->PaymentId = $paymentId;
  }



  public function AddTransaction(TransactionToRefundModel $transaction) {
    if ($this->TransactionsToRefund == null) {
      $this->TransactionsToRefund = [];
    }
    array_push($this->TransactionsToRefund, $transaction);
  }



  public function AddTransactions($transactions) {
    if (!empty($transactions)) {
      foreach ($transactions as $transaction) {
        if ($transaction instanceof TransactionToRefundModel) {
          $this->AddTransaction($transaction);
        }
      }
    }
  }

}

class RefundResponseModel extends BaseResponseModel implements iBarionModel {
  public $PaymentId;
  public $RefundedTransactions;



  function __construct() {
    parent::__construct();
    $this->PaymentId = "";
    $this->RefundedTransactions = [];
  }



  public function fromJson($json) {
    if (!empty($json)) {
      parent::fromJson($json);

      $this->PaymentId = jget($json, 'PaymentId');
      $this->RefundedTransactions = [];

      if (!empty($json['RefundedTransactions'])) {
        foreach ($json['RefundedTransactions'] as $key => $value) {
          $tr = new RefundedTransactionModel();
          $tr->fromJson($value);
          array_push($this->RefundedTransactions, $tr);
        }
      }
    }
  }
}

class TransactionDetailModel implements iBarionModel {
  public $TransactionId;
  public $POSTransactionId;
  public $TransactionTime;
  public $Total;
  public $Currency;
  public $Payer;
  public $Payee;
  public $Comment;
  public $Status;
  public $TransactionType;
  public $Items;
  public $RelatedId;
  public $POSId;
  public $PaymentId;



  function __construct() {
    $this->TransactionId = "";
    $this->POSTransactionId = "";
    $this->TransactionTime = "";
    $this->Total = 0;
    $this->Currency = "";
    $this->Payer = new UserModel();
    $this->Payee = new UserModel();
    $this->Comment = "";
    $this->Status = "";
    $this->TransactionType = "";
    $this->Items = [];
    $this->RelatedId = "";
    $this->POSId = "";
    $this->PaymentId = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->TransactionId = $json['TransactionId'];
      $this->POSTransactionId = $json['POSTransactionId'];
      $this->TransactionTime = $json['TransactionTime'];
      $this->Total = $json['Total'];
      $this->Currency = $json['Currency'];

      $this->Payer = new UserModel();
      $this->Payer->fromJson($json['Payer']);

      $this->Payee = new UserModel();
      $this->Payee->fromJson($json['Payee']);

      $this->Comment = $json['Comment'];
      $this->Status = $json['Status'];
      $this->TransactionType = $json['TransactionType'];

      $this->Items = [];

      if (!empty($json['Items'])) {
        foreach ($json['Items'] as $i) {
          $item = new ItemModel();
          $item->fromJson($i);
          array_push($this->Items, $item);
        }
      }

      $this->RelatedId = $json['RelatedId'];
      $this->POSId = $json['POSId'];
      $this->PaymentId = $json['PaymentId'];
    }
  }
}

class TransactionResponseModel implements iBarionModel {
  public $POSTransactionId;
  public $TransactionId;
  public $Status;
  public $TransactionTime;
  public $RelatedId;



  function __construct() {
    $this->POSTransactionId = "";
    $this->TransactionId = "";
    $this->Status = "";
    $this->TransactionTime = "";
    $this->RelatedId = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->POSTransactionId = $json['POSTransactionId'];
      $this->Status = $json['Status'];
      $this->TransactionId = $json['TransactionId'];
      $this->TransactionTime = $json['TransactionTime'];
      $this->RelatedId = $json['RelatedId'];
    }
  }
}

class TransactionToFinishModel {
  public $TransactionId;
  public $Total;
  public $PayeeTransactions;
  public $Items;
  public $Comment;



  function __construct() {
    $this->TransactionId = "";
    $this->Total = 0;
    $this->PayeeTransactions = [];
    $this->Comment = "";
    $this->Items = [];
  }



  public function AddItem(ItemModel $item) {
    if ($this->Items == null) {
      $this->Items = [];
    }
    array_push($this->Items, $item);
  }



  public function AddItems($items) {
    if (!empty($items)) {
      foreach ($items as $item) {
        if ($item instanceof ItemModel) {
          $this->AddItem($item);
        }
      }
    }
  }



  public function AddPayeeTransaction(PayeeTransactionToFinishModel $model) {
    if ($this->PayeeTransactions == null) {
      $this->PayeeTransactions = [];
    }
    array_push($this->PayeeTransactions, $model);
  }



  public function AddPayeeTransactions($transactions) {
    if (!empty($transactions)) {
      foreach ($transactions as $transaction) {
        if ($transaction instanceof PayeeTransactionToFinishModel) {
          $this->AddPayeeTransaction($transaction);
        }
      }
    }
  }
}

class TransactionToRefundModel {
  public $TransactionId;
  public $POSTransactionId;
  public $AmountToRefund;
  public $Comment;



  function __construct($transactionId = null, $posTransactionId = null, $amountToRefund = null, $comment = null) {
    $this->TransactionId = $transactionId;
    $this->POSTransactionId = $posTransactionId;
    $this->AmountToRefund = $amountToRefund;
    $this->Comment = $comment;
  }
}

class UserModel implements iBarionModel {
  public $Name;
  public $Email;



  function __construct() {
    $this->Name = "";
    $this->Email = "";
  }



  function fromJson($json) {
    if (!empty($json)) {
      $this->Email = $json['Email'];

      $name = new UserNameModel();
      $name->fromJson($json['Name']);
    }
  }
}

class UserNameModel implements iBarionModel {
  public $LoginName;
  public $FirstName;
  public $LastName;
  public $OrganizationName;



  function __construct() {
    $this->LoginName = "";
    $this->FirstName = "";
    $this->LastName = "";
    $this->OrganizationName = "";
  }



  public function fromJson($json) {
    if (!empty($json)) {
      $this->LoginName = $json['LoginName'];
      $this->FirstName = $json['FirstName'];
      $this->LastName = $json['LastName'];
      $this->OrganizationName = $json['OrganizationName'];
    }
  }
}

class BarionClient {
  private $Environment;

  private $Password;
  private $APIVersion;
  private $POSKey;

  private $BARION_API_URL = "";
  private $BARION_WEB_URL = "";

  private $UseBundledRootCertificates;



  /**
   *  Constructor
   * @param string $poskey The secret POSKey of your shop
   * @param int $version The version of the Barion API
   * @param string $env The environment to connect to
   * @param bool $useBundledRootCerts Set this to true if you're having problem with SSL connection
   */
  function __construct($poskey, $version = 2, $env = BarionEnvironment::Prod, $useBundledRootCerts = false) {

    $this->POSKey = $poskey;
    $this->APIVersion = $version;
    $this->Environment = $env;

    switch ($env) {

      case BarionEnvironment::Test:
        $this->BARION_API_URL = BARION_API_URL_TEST;
        $this->BARION_WEB_URL = BARION_WEB_URL_TEST;
        break;

      case BarionEnvironment::Prod:
      default:
        $this->BARION_API_URL = BARION_API_URL_PROD;
        $this->BARION_WEB_URL = BARION_WEB_URL_PROD;
        break;
    }

    $this->UseBundledRootCertificates = $useBundledRootCerts;
  }

  /* -------- BARION API CALL IMPLEMENTATIONS -------- */


  /**
   * Prepare a new payment
   * @param PreparePaymentRequestModel $model The request model for payment preparation
   * @return PreparePaymentResponseModel Returns the response from the Barion API
   */
  public function PreparePayment(PreparePaymentRequestModel $model) {
    $model->POSKey = $this->POSKey;
    $url = $this->BARION_API_URL . "/v" . $this->APIVersion . API_ENDPOINT_PREPAREPAYMENT;
    $response = $this->PostToBarion($url, $model);
    $rm = new PreparePaymentResponseModel();
    if (!empty($response)) {
      $json = json_decode($response, true);
      $rm->fromJson($json);
      if (!empty($rm->PaymentId)) {
        $rm->PaymentRedirectUrl = $this->BARION_WEB_URL . "?" . http_build_query(["id" => $rm->PaymentId]);
      }
    }

    return $rm;
  }



  /**
   * Finish an existing reservation
   * @param FinishReservationRequestModel $model The request model for the finish process
   * @return FinishReservationResponseModel Returns the response from the Barion API
   */
  public function FinishReservation(FinishReservationRequestModel $model) {
    $model->POSKey = $this->POSKey;
    $url = $this->BARION_API_URL . "/v" . $this->APIVersion . API_ENDPOINT_FINISHRESERVATION;
    $response = $this->PostToBarion($url, $model);
    $rm = new FinishReservationResponseModel();
    if (!empty($response)) {
      $json = json_decode($response, true);
      $rm->fromJson($json);
    }

    return $rm;
  }



  /**
   * Refund a payment partially or totally
   * @param RefundRequestModel $model The request model for the refund process
   * @return RefundResponseModel Returns the response from the Barion API
   */
  public function RefundPayment(RefundRequestModel $model) {
    $model->POSKey = $this->POSKey;
    $url = $this->BARION_API_URL . "/v" . $this->APIVersion . API_ENDPOINT_REFUND;
    $response = $this->PostToBarion($url, $model);
    $rm = new RefundResponseModel();
    if (!empty($response)) {
      $json = json_decode($response, true);
      $rm->fromJson($json);
    }

    return $rm;
  }



  /**
   * Get detailed information about a given payment
   * @param string $paymentId The Id of the payment
   * @return PaymentStateResponse Returns the response from the Barion API
   */
  public function GetPaymentState($paymentId) {
    $model = new PaymentStateRequestModel($paymentId);
    $model->POSKey = $this->POSKey;
    $url = $this->BARION_API_URL . "/v" . $this->APIVersion . API_ENDPOINT_PAYMENTSTATE;
    $response = $this->GetFromBarion($url, $model);
    $ps = new PaymentStateResponse();
    if (!empty($response)) {
      $json = json_decode($response, true);
      $ps->fromJson($json);
    }

    return $ps;
  }



  /**
   * Get the QR code image for a given payment
   * NOTE: This call is deprecated and is only working with username & password authentication.
   * If no username and/or password was set, this method returns NULL.
   * @param string $username The username of the shop's owner
   * @param string $password The password of the shop's owner
   * @param string $paymentId The Id of the payment
   * @param string $qrCodeSize The desired size of the QR image
   * @return mixed|string Returns the response of the QR request
   * @deprecated
   */
  public function GetPaymentQRImage($username, $password, $paymentId, $qrCodeSize = QRCodeSize::Large) {
    $model = new PaymentQRRequestModel($paymentId);
    $model->POSKey = $this->POSKey;
    $model->UserName = $username;
    $model->Password = $password;
    $model->Size = $qrCodeSize;
    $url = $this->BARION_API_URL . API_ENDPOINT_QRCODE;
    $response = $this->GetFromBarion($url, $model);

    return $response;
  }

  /* -------- CURL HTTP REQUEST IMPLEMENTATIONS -------- */

  /*
  *
  */
  /**
   * Managing HTTP POST requests
   * @param string $url The URL of the API endpoint
   * @param object $data The data object to be sent to the endpoint
   * @return mixed|string Returns the response of the API
   */
  private function PostToBarion($url, $data) {
    $ch = curl_init();

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    if ($userAgent == "") {
      $cver = curl_version();
      $userAgent = "curl/" . $cver["version"] . " " . $cver["ssl_version"];
    }

    $postData = json_encode($data);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "User-Agent: $userAgent"]);

    if (substr(phpversion(), 0, 3) < 5.6) {
      curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    }

    if ($this->UseBundledRootCertificates) {
      curl_setopt($ch, CURLOPT_CAINFO, join(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'ssl', 'cacert.pem']));

      if ($this->Environment == BarionEnvironment::Test) {
        curl_setopt($ch, CURLOPT_CAPATH, join(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'ssl', 'gd_bundle-g2.crt']));
      }
    }

    $output = curl_exec($ch);
    if ($err_nr = curl_errno($ch)) {
      $error = new ApiErrorModel();
      $error->ErrorCode = "CURL_ERROR";
      $error->Title = "CURL Error #" . $err_nr;
      $error->Description = curl_error($ch);

      $response = new BaseResponseModel();
      $response->Errors = [$error];
      $output = json_encode($response);
    }
    curl_close($ch);

    return $output;
  }



  /**
   * Managing HTTP GET requests
   * @param string $url The URL of the API endpoint
   * @param object $data The data object to be sent to the endpoint
   * @return mixed|string Returns the response of the API
   */
  private function GetFromBarion($url, $data) {
    $ch = curl_init();

    $getData = http_build_query($data);
    $fullUrl = $url . '?' . $getData;

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    if ($userAgent == "") {
      $cver = curl_version();
      $userAgent = "curl/" . $cver["version"] . " " . $cver["ssl_version"];
    }

    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["User-Agent: $userAgent"]);

    if (substr(phpversion(), 0, 3) < 5.6) {
      curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    }

    if ($this->UseBundledRootCertificates) {
      curl_setopt($ch, CURLOPT_CAINFO, join(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'ssl', 'cacert.pem']));

      if ($this->Environment == BarionEnvironment::Test) {
        curl_setopt($ch, CURLOPT_CAPATH, join(DIRECTORY_SEPARATOR, [dirname(__FILE__), 'ssl', 'gd_bundle-g2.crt']));
      }
    }

    $output = curl_exec($ch);
    if ($err_nr = curl_errno($ch)) {
      $error = new ApiErrorModel();
      $error->ErrorCode = "CURL_ERROR";
      $error->Title = "CURL Error #" . $err_nr;
      $error->Description = curl_error($ch);

      $response = new BaseResponseModel();
      $response->Errors = [$error];
      $output = json_encode($response);
    }
    curl_close($ch);

    return $output;
  }
}