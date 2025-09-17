<?php

namespace Modules\MercadoLibre\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Menu;

class DataController extends Controller
{
    public function dummy_data()
    {
        Artisan::call('db:seed', ['--class' => 'Modules\MercadoLibre\Database\Seeders\AddDummySyncLogTableSeeder']);
    }

    public function superadmin_package()
    {
        return [
            [
                'name' => 'mercadolibre_module',
                'label' => __('mercadolibre::lang.mercadolibre_module'),
                'default' => false,
            ],
        ];
    }

    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        return [
            [
                'value' => 'mercadolibre.syc_categories',
                'label' => __('mercadolibre::lang.sync_product_categories'),
                'default' => false,
            ],
            [
                'value' => 'mercadolibre.sync_products',
                'label' => __('mercadolibre::lang.sync_products'),
                'default' => false,
            ],
            [
                'value' => 'mercadolibre.sync_orders',
                'label' => __('mercadolibre::lang.sync_orders'),
                'default' => false,
            ],
            [
                'value' => 'mercadolibre.map_tax_rates',
                'label' => __('mercadolibre::lang.map_tax_rates'),
                'default' => false,
            ],
            [
                'value' => 'mercadolibre.access_mercadolibre_api_settings',
                'label' => __('mercadolibre::lang.access_mercadolibre_api_settings'),
                'default' => false,
            ],

        ];
    }

    /**
     * Adds mercadolibre menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $module_util = new ModuleUtil();

        $business_id = session()->get('user.business_id');
        $is_meli_enabled = (bool) $module_util->hasThePermissionInSubscription($business_id, 'mercadolibre_module', 'superadmin_package');

        if ($is_meli_enabled && (auth()->user()->can('mercadolibre.syc_categories') || auth()->user()->can('mercadolibre.sync_products') || auth()->user()->can('mercadolibre.sync_orders') || auth()->user()->can('mercadolibre.map_tax_rates') || auth()->user()->can('mercadolibre.access_mercadolibre_api_settings'))) {
            Menu::modify('admin-sidebar-menu', function ($menu) {
                $menu->url(
                    action([\Modules\MercadoLibre\Http\Controllers\MercadoLibreController::class, 'index']),
                    __('mercadolibre::lang.mercadolibre'),
                    ['icon' => 'fab fa-wordpress', 'style' => config('app.env') == 'demo' ? 'background-color: #9E458B !important;' : '', 'active' => request()->segment(1) == 'mercadolibre']
                )->order(88);
            });
        }
    }
}
