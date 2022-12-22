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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class pi_products_info_quantity_discount {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_quantity_discount_title');
      $this->description = CLICSHOPPING::getDef('module_products_info_quantity_discount_description');

      if (defined('MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

      if ($CLICSHOPPING_ProductsCommon->getID() && isset($_GET['Products'])) {

        $content_width = (int)MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_CONTENT_WIDTH;
        $text_position = MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_POSITION;

        $CLICSHOPPING_Customer = Registry::get('Customer');
         $CLICSHOPPING_Db = Registry::get('Db');
        $CLICSHOPPING_Template = Registry::get('Template');

        $QprodutsQuantityDiscount= $CLICSHOPPING_Db->prepare('select *
                                                              from :table_products_discount_quantity
                                                              where products_id = :products_id
                                                              and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                              and discount_quantity <> 0
                                                            ');
        $QprodutsQuantityDiscount->bindInt(':products_id', $CLICSHOPPING_ProductsCommon->getId());
        $QprodutsQuantityDiscount->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());

        $QprodutsQuantityDiscount->execute();

        $data = $QprodutsQuantityDiscount->fetchAll();

        $product_price = $CLICSHOPPING_ProductsCommon->getDisplayPriceGroupWithoutCurrencies();

        if ($QprodutsQuantityDiscount->rowCount() > 0) {
          if ( $CLICSHOPPING_ProductsCommon->getProductsGroupView() == 1 ||  $CLICSHOPPING_ProductsCommon->getProductsView() == 1) {

            $content = '<!-- Start products_quantity_discount -->' . "\n";

            ob_start();
            require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_info_quantity_discount'));

            $content .= ob_get_clean();

            $content .= '<!-- end products_quantity_discount -->' . "\n";

            $CLICSHOPPING_Template->addBlock($content, $this->group);
          }
        }
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to enable this module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to enable this module in your shop ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to display the percentage discount ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_DISPLAY_PERCENTAGE',
          'configuration_value' => 'True',
          'configuration_description' => 'Display the percentage discount',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to display the new price with the discount ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_DISPLAY_NEW_PRICE',
          'configuration_value' => 'False',
          'configuration_description' => 'Display the new price with the discount',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );


      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Please select the width of the display?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Please enter a number between 1 and 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Where do you want to display the module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_POSITION',
          'configuration_value' => 'none',
          'configuration_description' => 'Display Left, right ?',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'configuration_value' => 'none',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort order',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_SORT_ORDER',
          'configuration_value' => '118',
          'configuration_description' => 'Sort order of display. Lowest is displayed first. The sort order must be different on every module',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_STATUS',
        'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_DISPLAY_PERCENTAGE',
        'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_DISPLAY_NEW_PRICE',
        'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_POSITION',
        'MODULE_PRODUCTS_INFO_QUANTITY_DISCOUNT_SORT_ORDER'
      );
    }
  }

