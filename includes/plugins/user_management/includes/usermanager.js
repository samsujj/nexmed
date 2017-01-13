/**
 * Created by iftekar on 5/1/17.
 */

//alert(8);
$(function () {
    //alert(9);
    $('.page-Representatives').find('.te_new_button').attr('href','addreps');
    $('.page-Representatives').find('.te_export_button').hide();
    $('.page-Representatives').find('.te_new_button').eq(1).hide();
    $('.page-Representatives').find('.te_new_button').find('span').show();
});
