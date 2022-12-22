<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\QuantityDiscount\Module\Hooks\Shop\Cart;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class AdditionalCheckoutInfoProductsDiscount implements \ClicShopping\OM\Modules\HooksInterface
  {

    public function execute()
    {
      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

      $id = HTML::sanitize($_POST['products_id']);

      if (!is_null($id)) {
        $products_cart_array = $CLICSHOPPING_ShoppingCart->get_products();

        $total = 0;

        if (count($products_cart_array) > 0 && is_array($products_cart_array)) {
          $output = '<!-- Hook Products Discount Amount -->';

          foreach ($products_cart_array as $value) {
            $total += $CLICSHOPPING_ProductsCommon->getInfoPriceDiscountByQuantityShoppingCart($value['id'], $value['quantity'], $value['price']);
          }
        }

        if ($total != 0) {
          $total = $CLICSHOPPING_Currencies->displayPrice(round($total, 2), null);

          $output .= '<div class="row alert alert-info col-md-12" role="alert"> ' .
            '        <span>' . CLICSHOPPING::getDef('text_shopping_cart_products_discount_quantity') . '  : </span>' .
            '        <span class="moduleShoppingCartProductsListingDiscount">' . $total . ' ' . CLICSHOPPING::getDef('text_shopping_cart_products_discount_without_tax') . '</span>' .
            '      </div>';


          $output .= '<!-- End Products Discount Amount -->';

          return $output;
        }
      }
    }
  }