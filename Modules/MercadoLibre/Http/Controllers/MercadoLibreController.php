<?php

namespace Modules\MercadoLibre\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Media;
use App\Product;
use App\SellingPriceGroup;
use App\System;
use App\TaxRate;
use App\Utils\ModuleUtil;
use App\Variation;
use App\VariationTemplate;
use DB;
use Illuminate\Http\Response;
// use Modules\MercadoLibre\Model\MercadoLibreSyncLog;
use Modules\MercadoLibre\Utils\MercadoLibreUtil;
use Yajra\DataTables\Facades\DataTables;
use WebDEV\Meli\Services\MeliApiService;

class MercadoLibreController extends Controller
{
    protected $mercadolibreUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  MercadoLibreUtil  $mercadolibreUtil
     * @return void
     */
    public function __construct(MercadoLibreUtil $mercadolibreUtil, ModuleUtil $moduleUtil)
    {
        $this->mercadolibreUtil = $mercadolibreUtil;
        $this->moduleUtil = $moduleUtil;
    }
    
    public function test() {
        $service = new MeliApiService(auth()->user()->id);
        $data = $service->get('/users/me');
        dd($data->response);
    }

    public function validateToken() {
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->validateToken();
        } catch(\Exception $e) {
            $redirectUri = route('meli.token');
            $link = $service->getAuthUrl($redirectUri);
            return redirect()->away($link);
        }
    }
    
    public function create_testuser() {
        $service = new MeliApiService(auth()->user()->id);
        $data = ['site_id' => 'MLU'];
        $data = $service->post('/users/test_user', $data);
        dd($data->response);
    }

    public function disconnect() {
        try {
            $this->mercadolibreUtil->disconnect();
        }catch(\Exception $e) {

        }
    }

    public function getListingTypes($site_id) {
        try {
            $listing_types = $this->mercadolibreUtil->getListingTypes($site_id);
            dd($listing_types);
        }catch(\Exception $e) {

        }
    }

    public function getCategoryAttributes($cat_id) {
        try {
            $attributes = $this->mercadolibreUtil->getCategoryAttributes($cat_id);
            dd($attributes);
        } catch(\Exception $e) {

        }
    }

    public function getCategoryDetail($cat_id) {
        $service = new MeliApiService(auth()->user()->id);
        $data = $service->get("/categories/{$cat_id}");
        dd($data);
    }
    
    public function getCategory($cat_id) {
        try {
            if($cat_id == 'top') {
                $top_categories = $this->mercadolibreUtil->getTopCategories();
                return response()->json([
                    'success' => true,
                    'sub_categories'=> $top_categories
                ]);
            } else {
                $details = $this->mercadolibreUtil->getCategoryDetail($cat_id);
                return response()->json([
                    ...$details,
                    'success' => true,
                ]);
            }
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getProducts() {
        $service = new MeliApiService(auth()->user()->id);
        $data = $service->get('/users/2033149444/items/search');
        dd($data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        // To check whether connected to ML.
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->validateToken();
            $mercadolibre_connected = true;
        } catch (\Exception $e) {
            $mercadolibre_connected = false;
            $alerts['connection_failed'] = 'You are disconnected from MercadoLibre for now.';
            $redirectUri = route('meli.token');
            $meli_auth_link = $service->getAuthUrl($redirectUri);
        }

        try {
            $business_id = request()->session()->get('business.id');

            if (! (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'mercadolibre_module'))) {
                abort(403, 'Unauthorized action.');
            }

            $mercadolibre_api_settings = $this->mercadolibreUtil->get_api_settings($business_id);

            // new products count
            $query = Product::join("categories as sub_category", "products.sub_category_id", "=", "sub_category.id")
                ->where('products.business_id', $business_id)
                ->whereIn('products.type', ['single', 'variable'])
                ->whereNull('products.mercadolibre_product_id')
                ->where('products.mercadolibre_enable_sync', 1);

            if (! empty($mercadolibre_api_settings->location_id)) {
                $query->ForLocation($mercadolibre_api_settings->location_id);
            }
            $not_synced_product_count = $query->count();

            // count of products to be updated
            $updated_product_count = Product::where('products.business_id', $business_id)
                                    ->whereNotNull('products.mercadolibre_product_id')
                                    ->where('products.mercadolibre_enable_sync', 1)
                                    ->whereIn('products.type', ['single', 'variable'])
                                    ->where('updated_at', ">", function($query) {
                                        $query->select(DB::raw('MAX(created_at)'))->from('mercadolibre_sync_logs')->whereColumn('mercadolibre_sync_logs.prod_id', 'products.id');
                                    })
                                    ->count();

            return view('mercadolibre::index')->with(compact('alerts', 'mercadolibre_connected', 'meli_auth_link',
            'not_synced_product_count', 'updated_product_count'));

        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            return "Server Error";
        }
    }

    public function viewSyncLog() 
    {

    }

    public function apiSettings()
    {
        $business_id = request()->session()->get('business.id');

        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'mercadolibre_module') && auth()->user()->can('mercadolibre.access_mercadolibre_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $default_settings = [
            'location_id' => null,
            'default_tax_class' => '',
            'product_tax_type' => 'inc',
            'default_selling_price_group' => '',
            'product_fields_for_create' => ['quantity'],
            'product_fields_for_update' => ['name', 'price', 'quantity'],
        ];

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                        ->pluck('name', 'id')->prepend(__('lang_v1.default'), '');

        $business = Business::find($business_id);

        $notAllowed = $this->mercadolibreUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            $business = null;
        }

        if (! empty($business->mercadolibre_api_settings)) {
            $default_settings = json_decode($business->mercadolibre_api_settings, true);
            if (empty($default_settings['product_fields_for_create'])) {
                $default_settings['product_fields_for_create'] = [];
            }

            if (empty($default_settings['product_fields_for_update'])) {
                $default_settings['product_fields_for_update'] = [];
            }
        }

        $locations = BusinessLocation::forDropdown($business_id);
        $module_version = System::getProperty('mercadolibre_version');

        $cron_job_command = $this->moduleUtil->getCronJobCommand();

        $shipping_statuses = $this->moduleUtil->shipping_statuses();

        return view('mercadolibre::api_settings')
                ->with(compact('default_settings', 'locations', 'price_groups', 'module_version', 'cron_job_command', 'business', 'shipping_statuses'));
    }

        /**
     * Updates mercadolibre api settings.
     *
     * @return Response
     */
    public function updateSettings(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'mercadolibre_module') && auth()->user()->can('mercadolibre.access_mercadolibre_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->mercadolibreUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $input = $request->except('_token');

            $input['product_fields_for_create'] = ! empty($input['product_fields_for_create']) ? $input['product_fields_for_create'] : [];
            $input['product_fields_for_update'] = ! empty($input['product_fields_for_update']) ? $input['product_fields_for_update'] : [];
            $input['order_statuses'] = ! empty($input['order_statuses']) ? $input['order_statuses'] : [];
            $input['shipping_statuses'] = ! empty($input['shipping_statuses']) ? $input['shipping_statuses'] : [];

            $business = Business::find($business_id);
            $business->mercadolibre_api_settings = json_encode($input);
            $business->save();

            $output = ['success' => 1,
                'msg' => trans('lang_v1.updated_succesfully'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Synchronizes pos products with MercadoLibre products
     *
     * @return Response
     */
    public function syncProducts()
    {
        $notAllowed = $this->mercadolibreUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (! (auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'mercadolibre_module') && auth()->user()->can('mercadolibre.sync_products')))) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        try {
            $user_id = request()->session()->get('user.id');
            $sync_type = request()->input('type');

            DB::beginTransaction();

            $page = request()->input('page');
            $limit = 10;
            $sync_result = $this->mercadolibreUtil->syncProducts($business_id, $user_id, $sync_type, $limit, $page);

            DB::commit();
            $output = [
                'success' => 1,
                ...$sync_result
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());
            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        return $output;
    }

    public function syncOrders() {

    }

    public function resetCategories() {

    }

    public function resetProducts() {

    }

    public function getSyncLog() {

    }

    public function getLogDetails($id) {

    }
}
