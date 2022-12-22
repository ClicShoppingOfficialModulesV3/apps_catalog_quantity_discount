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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Customers\Groups\Classes\ClicShoppingAdmin\GroupsB2BAdmin;

  use ClicShopping\Apps\Catalog\QuantityDiscount\QuantityDiscount as QuantityDiscountApp;


  class PageTab implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $number_of_quantity_discount;

    public function __construct()
    {
      if (!Registry::exists('QuantityDiscount')) {
        Registry::set('QuantityDiscount', new QuantityDiscountApp());
      }

      $this->app = Registry::get('QuantityDiscount');
      $this->number_of_quantity_discount = 11;
    }

    public function display()
    {
      $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');
      $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

      if (!defined('CLICSHOPPING_APP_QUANTITY_DISCOUNT_QD_STATUS') || CLICSHOPPING_APP_QUANTITY_DISCOUNT_QD_STATUS == 'False') {
        return false;
      }

      $this->app->loadDefinitions('Module/Hooks/ClicShoppingAdmin/Products/page_tab');

      $button_delete = $this->app->getDef('button_delete');

      $configure_button = 'Configure';

      $customers_group = GroupsB2BAdmin::getAllGroups();
      $customers_group_name = '';

      foreach ($customers_group as $value) {
        $customers_group_name .= '<option value="' . $value['id'] . '">' . $value['text'] . '</option>';
      }

      $js_supplier_dropdown = '';
      $supplier_dropdown = $CLICSHOPPING_ProductsAdmin->supplierDropDown();

      foreach ($supplier_dropdown as $value) {
        $js_supplier_dropdown .= '<option value="' . $value['id'] . '">' . $value['text'] . '</option>';
      }

      $content = '<style>.table-open, .table-open a { color: #aaa; text-decoration: none; }</style>';

      $content .= '
              <div class="adminformTitle">
                <div class="separator"></div>
                <table width="100%" cellpadding="5" cellspacing="0" border="0">
                  <tr>
                    <td><table class="table table-sm table-hover table-striped table-hover" id="quantitydiscount">
                      <thead>
                        <tr>
                          <th>' . $this->app->getDef('table_heading_id') . '</th>
                          <th>' . $this->app->getDef('table_heading_supplier') . '</th>
                    ';
      if (MODE_B2B_B2C == 'true') {
        $content .= '
                          <th>' . $this->app->getDef('table_heading_customers_group') . '</th>
                      ';
      }

      $content .= '
                          <th>' . $this->app->getDef('table_heading_quantity_range') . '</th>
                          <th>' . $this->app->getDef('table_heading_supplier_price') . '</th>
                          <th>' . $this->app->getDef('table_heading_percentage_customer_discount') . '</th>
                          <td width="20">' . $this->app->getDef('table_heading_specification_action') . '</td>
                        </tr>
                      </thead>
                      <tbody>
                    ';

      if (isset($_GET['pID'])) {
        $pID = HTML::sanitize($_GET['pID']);

        $QproductsDiscountQuantity = $this->app->db->prepare('select *
                                                               from :table_products_discount_quantity
                                                               where products_id = :products_id
                                                              ');
        $QproductsDiscountQuantity->bindInt(':products_id', $pID);
        $QproductsDiscountQuantity->execute();
        $productsDiscountQuantity = $QproductsDiscountQuantity->fetchAll();
      } else {
        $productsDiscountQuantity = false;
      }

      $i = 0;

      if (is_array($productsDiscountQuantity)) {
        foreach ($productsDiscountQuantity as $value) {
          $id = $value['id'];

          $customers_group_id = $value['customers_group_id'];

          $content .= '
                        <tr id="quantitydiscount-row' . $i . '">
                          <td>' . $id . ' ' . HTML::hiddenField('id', $id) . '</td>
                          <td>' . HTML::selectMenu('products_quantitydiscount[' . $i . '][suppliers_id]', $supplier_dropdown, $value['suppliers_id']) . '</td>
                        ';

  //customers group
          if (MODE_B2B_B2C == 'true') {
            $content .= '
                            <td>' . HTML::selectMenu('products_quantitydiscount[' . $i . '][customers_group_id]', GroupsB2BAdmin::getAllGroups(), $customers_group_id) . '</td>
                          ';
          }

          $content .= '
                          <td>' . HTML::inputField('products_quantitydiscount[' . $i . '][discount_quantity]', $value['discount_quantity'], 'placeholder="' . $this->app->getDef('text_qty_range') . '"') . '</td>
                          <td>' . HTML::inputField('products_quantitydiscount[' . $i . '][discount_supplier_price]', $value['discount_supplier_price'], 'placeholder="' . $this->app->getDef('text_supplier_price') . '"') . '</td>
                          <td>' . HTML::inputField('products_quantitydiscount[' . $i . '][discount_customer]', $value['discount_customer'], 'placeholder="' . $this->app->getDef('text_discount_customer') . '"') . '</td>
                        ';
          $content .= '  <td class="text-end"><button type="button" onclick="$(\'#quantitydiscount-row' . $i . '\').remove();" data-toggle="tooltip" rel="tooltip" title="" class="btn btn-danger" data-original-title="Remove"><i class="fas fa-minus-circle"></i></button></td>';
          $content .= '</tr>';

          $i++;
        }
      }

      $content .= '           </tbody>
                              <tfoot>
                                <tr>
                                  <td colspan="5"></td>
                                  <td class="text-end"><button type="button" onclick="addQuantityDiscountValue();" data-toggle="tooltip" title="' . $this->app->getDef('button_add') . '" class="btn btn-primary"><i class="fas fa-plus-circle"></i></button></td>
                                </tr>
                              </tfoot>
                            </table></td>
                          </tr>
                        </table>
                        </div>
                          <div class="separator"></div>
                          <div class="alert alert-info" role="alert">
                          <div> ' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $this->app->getDef('title_help_general')) . '&nbsp;' . $this->app->getDef('title_help_general') . '</div>
                          <div class="separator"></div>
                          <div>' . $this->app->getDef('text_help_general_tab9') . '</div>
                        </div>
                  ';


      $tab_title = $this->app->getDef('tab_quantity_discount');
      $title = $this->app->getDef('text_products_quantity_discount');

      $mode_B2B_B2C = MODE_B2B_B2C;

      $output = <<<EOD
<!-- ######################## -->
<!-- Start QuantityDiscountApp  -->
<!-- ######################## -->

<div class="tab-pane" id="section_QuantityDiscountApp_content">
  <div class="mainTitle">
    <span class="col-md-10">{$title}</span>
    <span class="col-md-2 text-end">{$configure_button}</span>
  </div>
  {$content}
</div>

<script>
$('#section_QuantityDiscountApp_content').appendTo('#productsTabs .tab-content');
$('#productsTabs .nav-tabs').append('    <li class="nav-item"><a data-target="#section_QuantityDiscountApp_content" role="tab" data-toggle="tab" class="nav-link">{$tab_title}</a></li>');
</script>

<script type="text/javascript"><!--
  var quantitydiscount_row = {$i};
  var modeB2B_B2C = {$mode_B2B_B2C};

  function addQuantityDiscountValue() {
    html  = '<tr id="quantitydiscount-row' + quantitydiscount_row + '">';

    html  += '<td></td>';
   //suppliers_id
    html += '<td>';
    html += '  <select name="products_quantitydiscount[' + quantitydiscount_row + '][suppliers_id]" class="form-control">{$js_supplier_dropdown}</select>';
    html += '</td>'

//customers_group_id
   if (modeB2B_B2C === true) {
      html += '<td>';
      html += '  <select name="products_quantitydiscount[' + quantitydiscount_row + '][customers_group_id]" class="form-control">{$customers_group_name}</select>';
      html += '</td>';
    }

   //discount_quantity
    html += '<td>';
    html += '  <input type="text" name="products_quantitydiscount[' + quantitydiscount_row + '][discount_quantity]" value="" class="form-control" />';
    html += '</td>';

   //discount_supplier_price
    html += '<td>';
    html += '  <input type="text" name="products_quantitydiscount[' + quantitydiscount_row + '][discount_supplier_price]" value="" class="form-control" />';
    html += '</td>';

       //discount_quantity
    html += '<td>';
    html += '  <input type="text" name="products_quantitydiscount[' + quantitydiscount_row + '][discount_customer]" value="" class="form-control" />';
    html += '</td>';

    //remove
   	html += '  <td class="text-end"><button type="button" onclick="$(\'#quantitydiscount-row' + quantitydiscount_row + '\').remove();" data-toggle="tooltip" title="{$button_delete}" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
    html += '</tr>';

	   $('#quantitydiscount tbody').append(html);

    quantitydiscount_row++;
  }
</script>
<!-- ######################## -->
<!--  End QuantityDiscountApp  -->
<!-- ######################## -->
EOD;

      return $output;
    }
  }
