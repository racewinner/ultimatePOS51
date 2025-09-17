<?php

namespace App\Http\Controllers;

use App\Contact;
use App\User;
use App\Utils\ModuleUtil;
use App\Business;
use App\Notifications\CustomerNotification;
use App\Notifications\SupplierNotification;
use App\NotificationTemplate;
use App\Restaurant\Booking;
use App\Transaction;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Notification;
use Yajra\DataTables\Facades\DataTables;

class NotificationUserController extends Controller
{
    protected $notificationUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  NotificationUtil  $notificationUtil, TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(NotificationUtil $notificationUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->notificationUtil = $notificationUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $current_business = Business::find($business_id);
            $data = json_decode($current_business->notification_receivers, true);
            if ($data) {
                $receivers = isset($data[request()->document_type]) ? $data[request()->document_type] : [];
            } else {
                $data = [];
                $receivers = [];
            }
            $users = User::user()
                    ->where('is_cmmsn_agnt', 0)
                    ->whereIn('id', $receivers)
                    ->select(['id', 'username',
                            DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), 'email', 'allow_login', ]);

            return Datatables::of($users)
                ->editColumn('username', '{{$username}}')
                ->addColumn('role',
                    function ($row) {
                        $role_name = $this->moduleUtil->getUserRoleName($row->id);

                        return $role_name;
                    }
                )
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->addColumn( 'action',
                    '<button data-href="{{action(\'App\Http\Controllers\NotificationUserController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>')
                ->only(['username', 'full_name', 'role', 'action', 'email'])
                ->rawColumns(['action'])
                ->make(true);
        }
        return;
        return view('manage_user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('notifications.delete')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $current_business = Business::find($business_id);
                $data = json_decode($current_business->notification_receivers, true);
                if ($data) {
                    $receivers = isset($data[request()->document_type]) ? $data[request()->document_type] : [];
                } else {
                    $data = [];
                    $receivers = [];
                }
                $receivers = array_diff($receivers, [$id]);
                $data[request()->document_type] = $receivers;
                $current_business->notification_receivers = $data;
                $current_business->save();
                $output = ['success' => true,
                    'msg' => __('user.user_delete_success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
    
    public function getUsers()
    {
        if (request()->ajax()) {
            $term = request()->q;
            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            $query = User::where('business_id', $business_id)->active();

            $users = $query->where(function ($query) use ($term) {
                    $query->where('surname', 'like', '%'.$term.'%')
                        ->orWhere('first_name', 'like', '%'.$term.'%')
                        ->orWhere('last_name', 'like', '%'.$term.'%');
                })
                ->select(
                    'id',
                    'surname',
                    'first_name',
                    'last_name'
                )
                ->get();

            return json_encode($users);
        }
    }

        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Disable in demo
        if (request()->ajax() && isset($request->id) && isset($request->type) ) {
            $business_id = request()->session()->get('user.business_id');
            $current_business = Business::find($business_id);
            $data = json_decode($current_business->notification_receivers, true);
            if ($data) {
                $receivers = isset($data[$request->type]) ? $data[$request->type] : [];
            } else {
                $data = [];
                $receivers = [];
            }
            if (!in_array($request->id, $receivers)) {
                array_push($receivers, $request->id);
            }
            $data[$request->type] = $receivers;
            $current_business->notification_receivers = $data;
            $current_business->save();
            return true;
        }
        return false;
    }
}
