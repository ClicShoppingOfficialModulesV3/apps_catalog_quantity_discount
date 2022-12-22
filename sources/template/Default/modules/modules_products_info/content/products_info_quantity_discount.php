<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;
?>
<div class="<?php echo $text_position; ?> col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
    <table class="table">
      <thead>
        <tr>
<?php
  echo '<td class="col-md-1">' . CLICSHOPPING::getDef('text_quantity') . '</td>';
  foreach ($data as $element){
    echo '<td class="col-md-1">' . $element['discount_quantity'] . ' et +</td>';
  }
?>
        </tr>
<?php
  if (defined('MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_DISPLAY_PERCENTAGE') && MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_DISPLAY_PERCENTAGE == 'True') {
?>
        <tr>
<?php
    echo '<td  class="col-md-1">' . CLICSHOPPING::getDef('text_discount') . '</td>';
    foreach ($data as $element1){
       echo '<td class="col-md-1">' . $element1['discount_customer'] . ' %</td>';
    }
?>
        </tr>
<?php
  }
  if (defined('MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_DISPLAY_NEW_PRICE') && MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_DISPLAY_NEW_PRICE == 'True') {
?>
        <tr>
<?php

  echo '<td  class="col-md-1">' . CLICSHOPPING::getDef('text_new_price') . '</td>';
  foreach ($data as $element1){
    $new_price = round($product_price - ($product_price * ($element1['discount_customer'] / 100)), 2);
    echo '<td class="col-md-1">' . $new_price . '</td>';
  }

?>
        </tr>
<?php
  }
?>
      </thead>
    </table>
</div>
