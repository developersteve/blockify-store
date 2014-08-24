(function() {

    function btnSwitch(element, timer){

        $(element).removeClass("btn-danger");
        $(element).addClass("btn-success");
        $(element).html("Added");

        setTimeout(
            function()
            {
                $(element).addClass("btn-danger");
                $(element).removeClass("btn-success");
                $(element).html("Add to Cart");
            }, timer);
    }

    $(".cartbutton").click(function() {

        var loc = window.location.href;

        var message = "type=list";

        $.ajax({
            url : loc,
            type: "POST",
            headers: {
                blockName: "blockify-store",
            },
            dataType: "json",
            data : message,
            success: function(data)
            {
                var curr = $('.cartbutton').attr("data-currency");
                var output = "";

                $.each(data.cartData, function (index, value) {
                        output = output+"<div class='row r"+index+"'><div class='col-md-offset-1 col-md-8 col-sm-8 col-xs-6'>"+this.name+"</div><div class='col-md-2 col-sm-4 col-xs-6 text-center'>"+curr+"<span class='itemPrice'>"+this.value+"</span></div><div class='col-md-1 col-sm-4 col-xs-6 text-center remove'><a href='#' class='removeItem' data-index='"+index+"'>X</a></div></div>";
                });

                output = output+"<div class='row totalrow'><div class='col-md-offset-7 col-md-2 col-sm-8 col-xs-6'><strong>TOTAL</strong></div><div class='col-md-2 col-sm-4 col-xs-6 text-center'><strong>"+curr+"<span class='totPrice'>"+parseFloat(data.cartValue).toFixed(2)+"</span></strong></div><div class='col-md-1'></div></div>";

                $('.cartList').html(output);

                $(".removeItem").click(function () {
                    var ind = $(this).attr("data-index");
                    var itemPrice = $('.r'+ind+' div .itemPrice').text();
                    var totPrice = $('.totalrow div .totPrice').text();

                    var newPrice = (totPrice-itemPrice);

                    $.ajax({
                        url : loc,
                        type: "POST",
                        headers: {
                            blockName: "blockify-store",
                        },
                        data : "type=remove&itemNum="+ind,
                        success: function(data)
                        {
                            $('.r'+ind).remove();
                            $('.totPrice').text(parseFloat(newPrice).toFixed(2));
                            $('.cartAmt').html(curr+parseFloat(newPrice).toFixed(2));
                        }

                    });

                    return false;
                });

            }

        });

    });

    $(".blockify-store").find(".cart-button").click(function () {

        var element = this;

        var sku = $(element).attr("data-sku");
        var name = $(element).attr("data-name");
        var type = $(element).attr("data-type");
        var value = $(element).attr("data-value");

        var loc = window.location.href;

        var message = "sku="+sku+"&name="+name+"&type="+type+"&value="+value;

        $.ajax({
            url : loc,
            type: "POST",
            headers: {
                blockName: "blockify-store",
            },
            data : message,
            success: function(data)
            {
                var curr = $('.cartbutton').attr("data-currency");

                btnSwitch(element, 500);
                $('.cartAmt').html(curr+parseFloat(data).toFixed(2));
            }

        });
    });
})();