<?php

namespace Modules\MercadoLibre\Utils;

use App\Business;
use App\Category;
use App\Contact;
use App\Exceptions\PurchaseSellMismatch;
use App\Product;
use App\TaxRate;
use App\Transaction;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\VariationLocationDetails;
use App\VariationTemplate;
use DB;
use Modules\MercadoLibre\Model\MercadoLibreSyncLog;
use Modules\MercadoLibre\Exceptions\MercadoLibreError;
use WebDEV\Meli\Services\MeliApiService;

class MercadoLibreUtil extends Util
{
    protected $transactionUtil;

    protected $productUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
    }

    public function getAuthUrl()
    {
        $service = new MeliApiService(auth()->user()->id);
        $redirectUri = route('meli.token');
        $link = $service->getAuthUrl($redirectUri);
        return $link;
    }

    public function disconnect() 
    {
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->disconnect();
        }catch(\Exception $e) {

        }
    }

    public function validateToken()
    {
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->validateToken();
            return true;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getTopCategories($site_id='MLU') 
    {
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->validateToken();

            $data = $service->get("/sites/{$site_id}/categories");
            if($data->httpCode == 200) {
                return $data->response;
            } else {
                throw new \Exception("You are disconnected from MercadoLibre.");
            }
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getCategoryDetail($cat_id) {
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->validateToken();

            // To get detail information of the category from ML
            $data = $service->get("/categories/{$cat_id}");
            if($data->httpCode != 200) {
                throw new \Exception("You are disconnected from MercadoLibre.");
            }
            $category_details = $data->response;

            // item condition
            $item_conditions = [];
            $settings = $category_details->settings;
            foreach($settings->item_conditions as $cond) {
                $item_conditions[$cond] = $cond;
            }
    
            // To get attributes fromi ML
            $data = $service->get("/categories/{$cat_id}/attributes");
            if($data->httpCode != 200) {
                throw new \Exception("You are disconnected from MercadoLibre.");
            }
            $attributes = $data->response;
    
            return [
                'sub_categories'=> $category_details->children_categories,
                'path_from_root' => $category_details->path_from_root,
                'attributes' => $attributes,
                'item_conditions' => $item_conditions
            ]; 
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getListingTypes($site_id='MLU') {
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->validateToken();

            $data = $service->get("/sites/{$site_id}/listing_types");
            if($data->httpCode != 200) {
                throw new \Exception("You are disconnected from MercadoLibre.");
            }

            $listing_types = [];
            foreach($data->response as $t) {
                $listing_types[$t->id] = $t->name;
            }
            return $listing_types;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function getCategoryAttributes($cat_id) {
        try {
            $service = new MeliApiService(auth()->user()->id);
            $service->validateToken();
            
            $data = $service->get("/categories/{$cat_id}/attributes");
            if($data->httpCode != 200) {
                throw new \Exception("You are disconnected from MercadoLibre.");
            }
            return $data->response;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function get_api_settings($business_id)
    {
        $business = Business::find($business_id);
        $mercadolibre_api_settings = json_decode($business->mercadolibre_api_settings);

        return $mercadolibre_api_settings;
    }

    public function getAllResponse($business_id, $endpoint, $params = []) {
        
    }

    /**
     * Retrives last synced date from the database
     *
     * @param  id  $business_id
     * @param  string  $type
     * @param  bool  $for_humans = true
     */
    public function getLastSync($business_id, $prod_id, $for_humans = true)
    {
        $last_sync = MercadoLibreSyncLog::where('business_id', $business_id)
                            ->where('prod_id', $prod_id)
                            ->whereIn('operation_type', ['created', 'updated'])
                            ->whereIn('status', ['OK', 'ok'])
                            ->max('created_at');

        //If last reset present make last sync to null
        $last_reset = MercadoLibreSyncLog::where('business_id', $business_id)
                            ->where('operation_type', 'reset')
                            ->max('created_at');
        if (! empty($last_reset) && ! empty($last_sync) && $last_reset >= $last_sync) {
            $last_sync = null;
        }

        if (! empty($last_sync) && $for_humans) {
            $last_sync = \Carbon::createFromFormat('Y-m-d H:i:s', $last_sync)->diffForHumans();
        }

        return $last_sync;
    }

    public function getLastStockUpdated($location_id, $product_id)
    {
        $last_updated = VariationLocationDetails::where('location_id', $location_id)
                                    ->where('product_id', $product_id)
                                    ->max('updated_at');

        return $last_updated;
    }

    public function syncProd($item, $type, $mercado_product_id=null)
    {
        try {
            $service = new MeliApiService(auth()->user()->id);
            if($type == 'create') {
                $data = $service->post('/items', $item);
            } else {
                $data = $service->put("/items/" . $mercado_product_id, $item);
            }

            if($data->httpCode == 200 || $data->httpCode == 201) {
                return [
                    'status' => 'OK',
                    'mercado_product_id' => $data->response->id
                ];
            } else {
                $message = "";
                foreach($data->response->cause as $c) {
                    if($c->type == "error") {
                        $message .= $c->message;
                    }
                }
                return [
                    'status' => 'FAIL',
                    'error' => $data->response->cause,
                    'message' => $message
                ];
            }
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function syncOneProduct($product, $business, $user_id)
    {
        try {
            $mercadolibre_api_settings = json_decode($business->mercadolibre_api_settings);

            // if there is no image for the product, skip it.
            if (empty($product->image) || !isValidImage($product->image_path)) {
                \Log::info("Failed to synchronize product($product->id), reason: no image: ImagePath=$product->image_path");
                throw new \Exception("Failed to synchronize with Mercado because of no valid image");
            }

            //Set common data
            $sync_data = [];
            $sync_type = '';

            $manage_stock = false;
            if ($product->enable_stock == 1 && $product->type == 'single') {
                $manage_stock = true;
            }

            //Get details from first variation for single product only
            $first_variation = $product->variations->first();
            if (empty($first_variation)) {
                \Log::info("Failed to synchronize product($product->id), reasone: no found variation.");
                throw new \Exception("Failed to synchronize with Mercado because of no found variation");
            };
            
            $price = $mercadolibre_api_settings->product_tax_type == 'exc' ? $first_variation->default_sell_price : $first_variation->sell_price_inc_tax;

            if (! empty($mercadolibre_api_settings->default_selling_price_group)) {
                $group_prices = $this->productUtil->getVariationGroupPrice($first_variation->id, $mercadolibre_api_settings->default_selling_price_group, $product->tax_id);

                $price = $mercadolibre_api_settings->product_tax_type == 'exc' ? $group_prices['price_exc_tax'] : $group_prices['price_inc_tax'];
            }

            //Set product stock
            $qty_available = 0;
            if ($manage_stock) {
                $variation_location_details = $first_variation->variation_location_details;
                foreach ($variation_location_details as $vld) {
                    if ($vld->location_id == $mercadolibre_api_settings->location_id) {
                        $qty_available = $vld->qty_available;
                    }
                }
            }
            if($qty_available <= 0.0) {
                \Log::info("Failed to synchronize product($product->id), reason: no available quantity");
                throw new \Exception("Failed to synchronize with Mercado because of no available quantity");
            }

            if (empty($product->mercadolibre_product_id)) {
                $sync_type = 'create';
                $mercadolibre_details = [];
                if(!empty($product->mercadolibre_details)) {
                    $mercadolibre_details = json_decode($product->mercadolibre_details, true);
                }

                $sync_data['title'] = $product->name;
                $sync_data['category_id'] = $product->sub_category->mercadolibre_category_id;
                $sync_data['currency_id'] = !empty($product->currency_id) ? $product->currency->code : $business->currency->code;
                $sync_data['condition'] = $mercadolibre_details['item_condition'] ?? 'new' ;
                $sync_data['listing_type_id'] = $mercadolibre_details['listing_type'] ?? 'free';
                $sync_data['pictures'] = [['source' => $product->image_url]];

                // To set required attributes
                $sync_data['attributes'] = [];
                foreach($mercadolibre_details['attributes'] as $key => $val) {
                    $sync_data['attributes'][] = [
                        'id' => $key,
                        'value_name' => $val
                    ];
                }

                // sync product weight
                if (in_array('weight', $mercadolibre_api_settings->product_fields_for_create) && !empty($product->weight)) {
                    $sync_data['attributes'][] = [
                        'id' => 'WEIGHT', 
                        'value_name' => formatDecimalPoint($product->weight) . "kg"
                    ];
                }

                //sync product description
                if (in_array('description', $mercadolibre_api_settings->product_fields_for_create) && !empty($product->product_description)) {
                    $sync_data['description'] = $product->product_description;
                }

                //assign quantity and price if single product
                if ($product->type == 'single') {
                    $sync_data['available_quantity'] = formatDecimalPoint($qty_available, 'quantity');
                    // $sync_data['available_quantity'] = 1;
                    $sync_data['price'] = formatDecimalPoint($price);
                }
    
                $sync_response = $this->syncProd($sync_data, $sync_type);
                if($sync_response['status'] == 'OK') {
                    $product->mercadolibre_product_id = $sync_response['mercado_product_id'];
                    $product->save();
                }

                $this->createSyncLog($product->id, $business->id, $user_id, 'one_product', 'created', $sync_response['status'], $sync_data, $sync_response['error']);
            } else {
                $sync_type = 'update';

                if (in_array('name', $mercadolibre_api_settings->product_fields_for_update)) {
                    $sync_data['title'] = $product->name;
                }
                if (in_array('image', $mercadolibre_api_settings->product_fields_for_update)) {
                    $sync_data['pictures'] = [['source' => $product->image_url]];
                }

                // weight
                if (in_array('weight', $mercadolibre_api_settings->product_fields_for_update)) {
                    $sync_data['attributes'][] = [
                        'id' => 'WEIGHT', 
                        'value_name' => formatDecimalPoint($product->weight) . "kg"
                    ];
                }

                if ($product->type == 'single') {
                    if (in_array('quantity', $mercadolibre_api_settings->product_fields_for_update)) {
                        $sync_data['available_quantity'] = formatDecimalPoint($qty_available, 'quantity');
                    }
                    $sync_data['price'] = formatDecimalPoint($price);
                }

                $sync_response = $this->syncProd($sync_data, $sync_type, $product->mercadolibre_product_id);

                $this->createSyncLog($product->id, $business->id, $user_id, 'one_product', 'updated', $sync_response['status'], $sync_data, $sync_response['error']);
            }

            return $sync_response;

        }catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * Synchronizes pos products with MercadoLibre products
     *
     * @param  int  $business_id
     * @return void
     */
    public function syncProducts($business_id, $user_id, $sync_type, $limit = 100, $page = 0)
    {
        try {
            $sync_count = 0;
            $errors = [];

            $business = Business::find($business_id);
            $mercadolibre_api_settings = $this->get_api_settings($business_id);
            if(empty($mercadolibre_api_settings)) {
                throw new \Exception("No found mercadolibre setting.");
            }

            $business_location_id = $mercadolibre_api_settings->location_id;
            $offset = $page * $limit;
            $query = Product::where('products.business_id', $business_id)
                ->whereIn('products.type', ['single', 'variable'])
                ->where('products.mercadolibre_enable_sync', 1)
                ->with(['variations', 'variations.variation_location_details', 'currency',
                    'variations.product_variation','variations.product_variation.variation_template', 'sub_category']);

            if ($limit > 0) {
                $query->limit($limit)->offset($offset);
            }

            if ($sync_type == 'new') {
                $query->whereNull('mercadolibre_product_id');
            }

            //Select products only from selected location
            if (! empty($business_location_id)) {
                $query->ForLocation($business_location_id);
            }

            $all_products = $query->get();
            if (count($all_products) == 0) {
                request()->session()->forget('meli_last_product_synced');
            }

            foreach ($all_products as $product) {
                $last_synced = $this->getLastSync($business_id, $product->id, false);
                if(!empty($last_synced)) {
                    //Skip product if last updated is less than last sync
                    $last_updated = $product->updated_at;
                    //check last stock updated
                    $last_stock_updated = $this->getLastStockUpdated($business_location_id, $product->id);
                    if (! empty($last_stock_updated)) {
                        $last_updated = strtotime($last_stock_updated) > strtotime($last_updated) ?
                                $last_stock_updated : $last_updated;
                    }

                    if (! empty($product->mercadolibre_product_id) && ! empty($last_synced) && strtotime($last_updated) < strtotime($last_synced)) {
                        \Log::info("sync_product: skip $product->id, reason: already synchronized");
                        continue;
                    }
                }

                try {
                    $sync_response = $this->syncOneProduct($product, $business, $user_id);
                    if($sync_response['status'] == 'OK') $sync_count++;
                } catch(\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            return [
                'sync_count' => $sync_count,
                'errors' => $errors
            ];
        }catch(\Exception $e) {
            throw $e;
        }
    }

    public function createSyncLog($prod_id, $business_id, $user_id, $type, $operation, $status, $data = [], $errors = null)
    {
        MercadoLibreSyncLog::create([
            'business_id' => $business_id,
            'prod_id' => $prod_id,
            'created_by' => $user_id,
            'operation_type' => $operation,
            'status' => $status,
            'data' => ! empty($data) ? json_encode($data) : null,
            'details' => ! empty($errors) ? json_encode($errors) : null,
        ]);
    }
}
?>