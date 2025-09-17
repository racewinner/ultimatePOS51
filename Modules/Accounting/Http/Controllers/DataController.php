<?php

namespace Modules\Accounting\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Routing\Controller;
use Menu;

class DataController extends Controller
{

    /**
     * Defines user permissions for the module.
     * @return array
     */
    public function user_permissions()
    {
        return array(
            array('value' => 'accounting.chart_of_accounts.index', 'label' => 'View Chart of accounts'),
            array('value' => 'accounting.chart_of_accounts.create', 'label' => 'Create Chart of accounts'),
            array('value' => 'accounting.chart_of_accounts.edit', 'label' => 'Edit Chart of accounts'),
            array('value' => 'accounting.chart_of_accounts.destroy', 'label' => 'Delete Chart of accounts'),
            array('value' => 'accounting.journal_entries.index', 'label' => 'View Journal Entries'),
            array('value' => 'accounting.journal_entries.create', 'label' => 'Create Journal Entries'),
            array('value' => 'accounting.journal_entries.edit', 'label' => 'Edit Journal Entries'),
            array('value' => 'accounting.journal_entries.reverse', 'label' => 'Reverse Journal Entries'),
            array('value' => 'accounting.reports.balance_sheet', 'label' => 'View Balance Sheet'),
            array('value' => 'accounting.reports.trial_balance', 'label' => 'View Trial Balance'),
            array('value' => 'accounting.reports.income_statement', 'label' => 'View Income Statement'),
            array('value' => 'accounting.reports.ledger', 'label' => 'View Ledger')
        );
    }

    public function superadmin_package()
    {
        return [
            [
                'name' => 'accounting_module',
                'label' => __('accounting::lang.accounting'),
                'default' => false
            ]
        ];
    }

    /**
     * Adds Accounting menus
     * @return null
     */
    public function modifyAdminMenu()
    {
        $business_id = session()->get('user.business_id');
        $module_util = new ModuleUtil();
        $module_names = get_module_names();
        $is_accounting_enabled = (bool)$module_util->hasThePermissionInSubscription($business_id, $module_names->accounting);

        if ($is_accounting_enabled) {
            Menu::modify('admin-sidebar-menu', function ($menu) {
                $menu->url(
                    action('\Modules\Accounting\Http\Controllers\DashboardController@index'),
                    __('accounting::lang.accounting'),
                    [
                        'icon' => 'fa fas fa-book', 'id' => 'tour_step14',
                        'active' => request()->segment(1) == 'accounting' || request()->segment(2) == 'accounting'
                    ]
                )
                    ->order(24);
            });
        }
    }
}
