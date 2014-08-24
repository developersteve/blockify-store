<?php
if($_POST){

  if($_POST['type']=='list'){
    $data = Session::getInstance();
    echo json_encode($data->cart);
  }
  else{
    $data = Session::getInstance();
    $data->cart('cart', $_POST);

    echo $data->cart['cartValue'];
  }

}else{
  $block->open();

  $data = Session::getInstance();
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
                <button type="button" class="btn btn-danger">Check out</button>
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