

$(document).ready(function() {

  //Add product on cart script
  $(document).on('submit','#cart_add',function(event){
    event.preventDefault();
    let form = $(this);
    let url = form.attr( "action" );
    let selected = $(this);

    let posting = $.post(url,form.serialize());
    addSpinLoaderToDiv('#add-to-cart-btn');

    // new display on success
    posting.done(function( data ) {
      let itemTotalPrice = parseFloat(data['priceUnit']*data['quantity']);
      let divId = '#'+ data['id']+data['size'];  // The id is composed with item_id and size_subname cause you can add many same items of different sizes
      deleteItemFromCartIfItemExist(divId);
      addItemOnNavCartDropDown(data);  // Add item on navcart dropdown
      updateNewTotalCart(itemTotalPrice);
      showAddingToCartValidation();

    });
  });

  //Reset cart form on change qty or size
  $(document).on('change','#form_size',function(event){
    if(!$('#add-to-cart-btn').hasClass('btn-color')){
      let addToCart = "<i class='ui-bag'></i><span>Ajouter au panier</span>";
      $('#add-to-cart-btn').html(addToCart).prop('disabled',false).addClass('btn-color').removeAttr('style');

    }
  });

  $(document).on('change','#form_quantity',function(event){
    if(!$('#add-to-cart-btn').hasClass('btn-color')){
      let addToCart = "<i class='ui-bag'></i><span>Ajouter au panier</span>";
      $('#add-to-cart-btn').html(addToCart).prop('disabled',false).addClass('btn-color').removeAttr('style');

    }
  });

  // Delete item from cart view
  $(document).on('click','.nav-cart__remove',function(event){
    event.preventDefault();
    //$(this).prop('disabled',true);
    let href = $(this).find('a').first().attr('href');
    let parent = $(this).parent();
    let getting = $.get(href);
    $(this).find('a').first().removeAttr('href');
    getting.done(function(data){
      deleteFromCartView('#'+parent.attr('id'));
      if ( $('.nav-cart__items').children().length == 0 ) {  // if cart is empty don't display button 'voir panier' and 'passer au paiement'
          $('#empty-cart').show();
      	$('.nav-cart__actions').hide();
      }
    });
  });


  // Delete item from full cart
  $(document).on('click','.product-remove',function(event){
    event.preventDefault();
    //$(this).prop('disabled',true);
    let href = $(this).find('a').first().attr('href');
    let parent = $(this).parent();
    let getting = $.get(href);
    getting.done(function(data){
      deleteFromFullCart('#'+parent.attr('id'));
      if ( $('#cart-table tbody').children().length == 0 ) {  // if cart is empty don't display button 'voir panier' and 'passer au paiement'
          $('.table-wrap').remove();
          $('#amount-widget').remove();
      	  $('#empty-cart').show();
      }
    });
  });

  // Change product quantity on full cart
  $(document).on('click','.product-quantity',function(event){
    event.preventDefault();
    let parent = $(this).parent();
    changeProductQuantity('#'+parent.attr('id'));
  });


  //Validate cart details and go to payment page
  $(document).on('click','#payment',function(event){
    event.preventDefault();
    addSpinLoaderToDiv('#payment');

    let cartContent = [];
    $('.cart_item').each(function(index){
      let item = {};
      item.id = $(this).attr('data-item-id');
      item.size = $(this).attr('data-item-size');
      item.quantity = $(this).find('input[type="number"].qty').first().val();

      cartContent.push(item)
    });

    let url = $(this).attr('href');
    let posting = $.post(url,{'cart':cartContent});

    posting.done(function(data){
      let paymentPage = Routing.generate('payment_page');
      window.location.href = paymentPage;  // Redirect to payment page
    });
  });


  function deleteFromCartView(divId){
    let qty = parseFloat($(divId).find('.qty').first().text()); // get item quantity
    let price = parseFloat($(divId).find('.price').first().text().replace(',', '.')); //get price and replace comma by dot for calculation
    let itemTotalPrice = parseFloat(qty*price);
    let totalAmount = parseFloat($('#cart-total').text().replace(',', '.')) - itemTotalPrice; // update the total amount
    $('#cart-total').text(totalAmount.toFixed(2).toString().replace(/[.]/, ","));
    let totalItems = parseInt($('#cart-total-items').text())-1;  // update cart item number
    $('#cart-total-items').text(totalItems);
    $('#cart-total-items-mobile').text(totalItems);
    let addToCart = "<i class='ui-bag'></i><span>Ajouter au panier</span>";
    $('#add-to-cart-btn').first().html(addToCart).prop('disabled',false).addClass('btn-color').removeAttr('style');
    $(divId).remove();
  }

  function deleteFromFullCart(divId){
    let qty = parseFloat($(divId).find('input[type="number"].qty').first().val());
    let price = parseFloat($(divId).find('.price').first().text().replace(',', '.')); //get price and replace comma by dot for calculation
    let itemTotalPrice = parseFloat(qty*price);
    let totalAmount = parseFloat($('#cart-total').text().replace(',', '.')) - itemTotalPrice; // update the total amount
    $('#cart-total').text(totalAmount.toFixed(2).toString().replace(/[.]/, ","));
    $('#order-total-amount').text(totalAmount.toFixed(2).toString().replace(/[.]/, ","));
    let totalItems = parseInt($('#cart-total-items').text())-1;  // update cart item number
    $('#cart-total-items').text(totalItems);
    $('#cart-total-items-mobile').text(totalItems);
    $(divId).remove();
  }

  function changeProductQuantity(divId){
    let qty = parseFloat($(divId).find('input[type="number"].qty').first().val()); // get item quantity
    let price = parseFloat($(divId).find('.price').first().text().replace(',', '.')); //get price and replace comma by dot for calculation
    let previousTotal = $(divId).find('.prod-subtotal').first().text().replace(',', '.');
    let itemTotalPrice = parseFloat(qty*price);
    $(divId).find('.prod-subtotal').first().text(itemTotalPrice.toFixed(2).toString().replace(/[.]/, ",")); // change sub total on the line
    let totalAmount = parseFloat($('#cart-total').text().replace(',', '.')) - previousTotal + itemTotalPrice ; // update the total amount
    $('#cart-total').text(totalAmount.toFixed(2).toString().replace(/[.]/, ","));
    $('#order-total-amount').text(totalAmount.toFixed(2).toString().replace(/[.]/, ","));
  }



  function addItemOnNavCartDropDown(item){
    let textSize='';
    let delete_from_cart_route = Routing.generate('remove_from_cart', { id: item['id'], size:item['size'] });
    if(item['size']!='none'){
      textSize = "<br/> <span>Taille:"+item['size']+"</span>";
    }
    let itemContent = "<div class='nav-cart__item clearfix' id="+item['id']+item['size'] +" >"+
                      "<div class='nav-cart__img'>"+
                      "<a href='#'> <img src="+ item['imgUrl']+" alt=''> </a></div>"+
                      "<div class='nav-cart__title'>"+
                      "<a href='#'>"+ item['name']+ "</a>"+ textSize +
                      "<div class='nav-cart__price'>"+
                      "<span><span class='qty'>"+ item['quantity']+"</span> x</span>"+
                      "<span><span class='price'>"+item['priceUnit'].toString().replace(/[.]/, ",") +"</span>&euro;</span>"+
                      "</div></div>"+
                      "<div class='nav-cart__remove'>"+
                      "<a href="+delete_from_cart_route +"><i class='ui-close'></i></a>"+
                      "</div></div>";

    $('.nav-cart__items').append(itemContent);

  }


  function deleteItemFromCartIfItemExist(divId){
    if($(divId).length){  //Verify if item is in the cart
        deleteFromCartView(divId);
    }
  }

  function updateNewTotalCart(itemTotalPrice){
    let totalAmount = parseFloat($('#cart-total').text().replace(',', '.')) + parseFloat(itemTotalPrice);
    $('#cart-total').text(totalAmount.toFixed(2).toString().replace(/[.]/, ",")); //change with the new total

    let totalItems = parseInt($('#cart-total-items').text())+1;
    $('#cart-total-items').text(totalItems);
    $('#cart-total-items-mobile').text(totalItems);
    $('#empty-cart').hide();

  }


  function showAddingToCartValidation(){
    $('.nav-cart__actions').show();  // show actions 'voir panier' et 'paiement'

    $('.nav-cart__dropdown' ).css( {"visibility": "visible",'opacity':1});
    $('#add-to-cart-btn').html("<i class='ui-check'></i><span>Ajouté</span>").prop('disabled',true);
    $('#add-to-cart-btn').css('background-color','green');
    $('#add-to-cart-btn').removeClass('btn-color');
    setTimeout(function(){$('.nav-cart__dropdown' ).prop('style',null)},5000);
  }

  function addSpinLoaderToDiv(divId){
    let loader = "<div class='ld ld-ring ld-spin'></div>";
    $(divId).append(loader);
  }


});
