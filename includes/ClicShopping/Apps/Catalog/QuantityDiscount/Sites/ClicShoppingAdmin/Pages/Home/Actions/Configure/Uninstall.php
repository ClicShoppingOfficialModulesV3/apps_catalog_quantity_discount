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

  namespace ClicShopping\Apps\Catalog\QuantityDiscount\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  class Uninstall extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_QuantityDiscount = Registry::get('QuantityDiscount');

      $current_module = $this->page->data['current_module'];
      $m = Registry::get('QuantityDiscountAdminConfig' . $current_module);
      $m->uninstall();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_QuantityDiscount->getDef('alert_module_uninstall_success'), 'success', 'QuantityDiscount');

      $CLICSHOPPING_QuantityDiscount->redirect('Configure&module=' . $current_module);
    }
  }