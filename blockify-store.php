<?php

if($_POST){

  if($_POST['type']=='list'){
    $data = Store::getInstance();
    echo json_encode($data->cart);
  }
  elseif($_POST['type']=='PayPal'){

    $data = Store::getInstance();

    require('pay-methods/paypal.php');

    $config = array(
      "environment" => $block->document['PayPal']['environment'], # or live
      "user"  => $block->document['PayPal']['user'],
      "pwd"  => $block->document['PayPal']['pwd'],
      "signature"  => $block->document['PayPal']['signature'],
      "version"  => $block->document['PayPal']['version']
    );

    $paypal = new PayPal($config);

    $cartCall = array(
      "method"  => "SetExpressCheckout",
      "paymentrequest_0_paymentaction" => "sale",
      "paymentrequest_0_amt"  => number_format($data->cart['cartValue'], 2, '.', ''),
      "paymentrequest_0_currencycode"  => $block->document['PayPal']['currency'],
      "paymentrequest_0_itemamt" => number_format($data->cart['cartValue'], 2, '.', ''),
      "returnurl"  => $paypal->getUrl(1),
      "cancelurl"  => $paypal->getUrl(1),
    );

   foreach($data->cart['cartData'] as $key => $cart ){
      $cartCall['l_paymentrequest_0_name'.$key] = $cart['name'];
      $cartCall['l_paymentrequest_0_qty'.$key] = 1;
      $cartCall['l_paymentrequest_0_amt'.$key] = $cart['value'];
    }

    $result = $paypal->call($cartCall);

    if($result['ACK'] == 'Success'){
      $result['redirect'] = $paypal->redirect($result);
    }

    echo json_encode($result);

  }
  else{
    $data = Store::getInstance();
    $data->cart('cart', $_POST);

    echo $data->cart['cartValue'];
  }

}
elseif($_GET['token'] and $_GET['PayerID']){
  $block->open();

  $data = Store::getInstance();

  require('pay-methods/paypal.php');

  $config = array(
    "environment" => $block->document['PayPal']['environment'], # or live
    "user"  => $block->document['PayPal']['user'],
    "pwd"  => $block->document['PayPal']['pwd'],
    "signature"  => $block->document['PayPal']['signature'],
    "version"  => $block->document['PayPal']['version']
  );

  $paypal = new PayPal($config);

  $result = $paypal->call(array(
    "paymentrequest_0_amt"  => number_format($data->cart['cartValue'], 2, '.', ''),
    "method"  => 'DoExpressCheckoutPayment',
    "token"  => $_GET['token'],
    "payerid"  => $_GET['PayerID'],
  ));

  if($result['ACK']=="Success"){
    echo "<h3>Thank you</h3>";
    echo "<h4>Your order has been placed</h4>";

    echo "<p>Order number: ".$result['PAYMENTINFO_0_TRANSACTIONID']."</p>";

    $data->destroy();

  }
  else{
    echo "<h3>".$result['L_LONGMESSAGE0']."</h3>";
  }

  echo "<h5><a href='".$paypal->getUrl(1)."'>Click here to return to the store</a></h5>";

  $block->close();
}else{

  $block->open();

  $data = Store::getInstance();

  ?>
  <div class="row">

    <div class="col-md-9 col-sm-8 col-xs-6 text-center cartbox">
      <h4>
        <?php echo $block->document['name']; ?>
      </h4>
    </div>

    <div class="col-md-3 col-sm-4 col-xs-6 text-center cartbox">

      <button class="btn btn-primary btn-block cartbutton" data-currency="<?php echo $block->document['currency']; ?>" data-toggle="modal" data-target="#<?php echo $block->document['name']; ?>">

        <span class="glyphicon glyphicon-shopping-cart"></span>
          <span class="cartAmt">
            <?php

              echo $block->document['currency'];

              if($data->cart['cartValue']){
                echo number_format($data->cart['cartValue'], 2, '.', '');
              }
              else echo number_format(0, 2, '.', '');
            ?>
          </span>
      </button>

      <div class="modal fade" id="<?php echo $block->document['name']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <h4 class="modal-title" id="myModalLabel">Your Order</h4>
            </div>
              <div class="modal-body cartList">

              </div>
              <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-success">Keep Shopping</button>
                <?php
                if($block->document['PayPal'])
                {
                  // echo "<a href='#'><img src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' class='img-responsive center-block'></a>";
                  echo "<button type='button' class='btn PayPal'><img src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' class='img-responsive center-block'></button>";
                }
                else
                { ?>
                  <button type="button" class="btn btn-danger">Check out</button>
                <?php }; ?>

              </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div class="row">
      <?php $block->document->each('@list', function ($prop, $value) use ($block) { ?>
         <div class="col-md-3 col-sm-6">
            <div class="col-md-12 item text-center">
                <div class="col-md-12 item">
                  <?php
                    $value->tag('img', 'image', ['class' => ['img-responsive center-block']]);
                    $value->tag('h2', 'name');
                    $value->tag('p', 'description');

                    echo "<h4>";
                      echo $block->document['currency'].number_format($value['price'], 2, '.', '');
                    echo "</h4>";

                    $attribs = [
                      'class' => ['btn-danger btn-sm cart-button'],
                      'data-sku' => $value['sku'],
                      'data-name' => $value['name'],
                      'data-type' => 'add',
                      'data-value' => number_format($value['price'], 2, '.', ''),
                    ];

                    $value->tag('button', 'button', $attribs);
                  ?>
                </div>
            </div>
        </div>
      <?php
      }); ?>

  </div>

  <?php
    $block->close();
  }
?>