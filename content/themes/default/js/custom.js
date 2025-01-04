// handle posts order
$('body').on('change', '.js_posts-order', function() {
  var _this = $(this);
  var order = _this.val();
  $.post(api['posts/order'], {'do': 'set', 'order': order}, function(response) {
    /* check the response */
    if (response.callback) {
      eval(response.callback);
    }
  }, 'json')
  .fail(function() {
    modal('#modal-message', {title: __['Error'], message: __['There is something that went wrong!']});
  });
});
