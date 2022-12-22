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

  namespace ClicShopping\Apps\Catalog\QuantityDiscount\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Catalog\QuantityDiscount\QuantityDiscount as QuantityDiscountApp;

  class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('QuantityDiscount')) {
        Registry::set('QuantityDiscount', new QuantityDiscountApp());
      }

      $this->app = Registry::get('QuantityDiscount');
    }

    private function removeQuantityDiscount($id)
    {
      $this->app->db->delete('products_discount_quantity', ['products_id' => (int)$id]);
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_QUANTITY_DISCOUNT_QD_STATUS') || CLICSHOPPING_APP_QUANTITY_DISCOUNT_QD_STATUS == 'False') {
        return false;
      }

      if (isset($_POST['remove_id'])) $pID = $_POST['remove_id'];
      if (isset($_POST['pID'])) $pID = $_POST['pID'];

      if (isset($pID)) {
        $id = HTML::sanitize($_POST['remove_id']);
        $this->removeQuantityDiscount($id);
      }
    }
  }