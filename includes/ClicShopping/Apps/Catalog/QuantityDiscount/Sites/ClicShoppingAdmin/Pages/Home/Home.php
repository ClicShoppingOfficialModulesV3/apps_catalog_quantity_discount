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

  namespace ClicShopping\Apps\Catalog\QuantityDiscount\Sites\ClicShoppingAdmin\Pages\Home;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\QuantityDiscount\QuantityDiscount;

  class Home extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      $CLICSHOPPING_QuantityDiscount = new QuantityDiscount();
      Registry::set('QuantityDiscount', $CLICSHOPPING_QuantityDiscount);

      $this->app = $CLICSHOPPING_QuantityDiscount;

      $this->app->loadDefinitions('Sites/ClicShoppingAdmin/main');
    }
  }
