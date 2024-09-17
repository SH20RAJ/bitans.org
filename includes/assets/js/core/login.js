/**
 * login js
 * 
 * @package Sngine
 * @author Zamblek
 */

$(function () {

  $('body').on('click', '.js_login, .js_chat-start, .js_friend-add, .js_follow, .js_like-page, .js_join-group, .js_go-event, .js_interest-event, .js_job-apply, .js_shopping-add-to-cart, .js_sneak-peak', function () {
    modal('#modal-login');
  });

});