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

  class Save implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $template;
    protected $db;

    public function __construct()
    {
      if (!Registry::exists('QuantityDiscount')) {
        Registry::set('QuantityDiscount', new QuantityDiscountApp());
      }

      $this->app = Registry::get('QuantityDiscount');

      $this->db = Registry::get('Db');
    }

    private function save()
    {
      if (isset($_GET['pID'])) {
        $products_id = HTML::sanitize($_GET['pID']);
      } else {
//insert last id of product table
        $Qproducts = $this->app->db->prepare('select products_id
                                              from :table_products
                                              order by products_id desc
                                              limit 1
                                            ');
        $Qproducts->execute();

        $products_id = $Qproducts->valueInt('products_id');
      }

      $Qdiscount_quantity = Registry::get('Db')->get('products_discount_quantity', 'id', ['products_id' => (int)$products_id]);


      if ($Qdiscount_quantity->valueInt('id') != 0) {
        $Qdelete = $this->app->db->prepare('delete
                                            from :table_products_discount_quantity
                                            where products_id = :products_id
                                          ');
        $Qdelete->bindInt(':products_id', $products_id);
        $Qdelete->execute();
      }

      if (isset($_POST['products_quantitydiscount'])) {
        foreach ($_POST['products_quantitydiscount'] as $products_quantity_discount) {
          $suppliers_id = HTML::sanitize($products_quantity_discount['suppliers_id']);
          $customers_group_id = HTML::sanitize($products_quantity_discount['customers_group_id']);
          $discount_quantity = HTML::sanitize($products_quantity_discount['discount_quantity']);
          $discount_supplier_price = $products_quantity_discount['discount_supplier_price'];
          $discount_customer = $products_quantity_discount['discount_customer'];

          $sql_data_array = ['products_id' => (int)$products_id,
            'suppliers_id' => (int)$suppliers_id,
            'customers_group_id' => (int)$customers_group_id,
            'discount_quantity' => (int)$discount_quantity,
            'discount_supplier_price' => (float)$discount_supplier_price,
            'discount_customer' => (float)$discount_customer
          ];

          $this->app->db->save('products_discount_quantity', $sql_data_array);

        }
      }
    }

    public function execute()
    {
      if (!defined('CLICSHOPPING_APP_QUANTITY_DISCOUNT_QD_STATUS') || CLICSHOPPING_APP_QUANTITY_DISCOUNT_QD_STATUS == 'False') {
        return false;
      }

      $this->save();
    }
  }