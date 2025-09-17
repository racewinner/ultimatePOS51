<?php

namespace App\Http\Controllers;

use App\Contact;
use App\User;
use App\Business;
use App\Notifications\CustomerNotification;
use App\Notifications\SupplierNotification;
use App\NotificationTemplate;
use App\Restaurant\Booking;
use App\Transaction;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Notification;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    protected $notificationUtil;

    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param  NotificationUtil  $notificationUtil, TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(NotificationUtil $notificationUtil, TransactionUtil $transactionUtil)
    {
        $this->notificationUtil = $notificationUtil;
        $this->transactionUtil = $transactionUtil;
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
            $user_id = auth()->user()->id;
            $notifications = DatabaseNotification::select('*')
                ->whereJsonContains('data->receivers', $user_id)
                ->orWhereJsonContains('data->receivers', (string)$user_id)
                ->orderBy('updated_at', 'desc')
                ->get();

            return Datatables::of($notifications)
                ->addColumn('receivers', function ($notification) {
                    $receivers = $notification->data['receivers'] ?? [];
                    $receivers = array_map(function ($receiveId) {
                        $receiver = User::find($receiveId);
                        return $receiver ? $receiver->surname . ' ' .$receiver->first_name . ' ' . $receiver->last_name : '';
                    }, $receivers);
                    $receivers = array_filter($receivers, function ($value) {
                        return $value !== '';
                    });
                    return implode(", ", $receivers);
                })
                ->addColumn('receiverIds', function ($notification) {
                    $receivers = $notification->data['receivers'] ?? [];
                    return implode(", ", $receivers);
                })
                ->addColumn('type', function ($notification) {
                    $type = '';
                    switch ( $notification->type ) {
                        case \App\Notifications\RecurringInvoiceNotification::class:
                            $type = 'Recurring Invoice is created';
                            break;
                        case \App\Notifications\RecurringExpenseNotification::class:
                            $type = 'Recurring Expense is created';
                            break;
                        case \App\Notifications\PurchaseNotification::class:
                            $type = 'New Invoice is created';
                            break;
                        case \App\Notifications\PurchaseOrderNotification::class:
                            $type = 'New Purchase Order is created';
                            break;
                        case \App\Notifications\PaymentNotification::class:
                            $type = 'New Payment Order is created';
                            break;
                        default:
                            // $moduleUtil = new \App\Utils\ModuleUtil;
                            // $module_notification_data = $moduleUtil->getModuleData('parse_notification', $notification);
                            // if (! empty($module_notification_data)) {
                            //     foreach ($module_notification_data as $module_data) {
                            //         if (! empty($module_data)) {
                            //             $notifications_data[] = $module_data;
                            //         }
                            //     }
                            // }
                            break;
                    }
                    return $type;
                })
                ->addColumn('created_at', function ($notification) {
                    $created_at = $notification->created_at;
                    return $created_at;
                })
                ->addColumn(
                    'action',
                    '<button notify_id="{{$id}}" receiverIds="{{$receiverIds}}" receivers="{{$receivers}}" class="btn btn-xs btn-primary edit-button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\NotificationController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    <a href="#" notify_id="{{$id}}" receiverIds="{{$receiverIds}}" class="btn btn-xs btn-primary update-button hidden"><i class="glyphicon glyphicon-floppy-disk"></i> @lang("messages.update")</a>
                    &nbsp;
                    <a data-href="#" class="btn btn-xs btn-danger cancel_button hidden"><i class="glyphicon glyphicon-remove"></i> @lang("messages.cancel")</a>
                    
                    ')
                ->only(['receivers', 'receiverIds', 'action', 'type', 'created_at'])
                ->rawColumns(['action', 'type'])
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
                $notification = DatabaseNotification::find($id);
                $notification->delete();
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

    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('notification.edit')) {
            abort(403, 'Unauthorized action.');
        }

        $notification = DatabaseNotification::find($id);
        $receiversIds = $notification->data->receivers ?? [];
        $receivers = User::whereIn('id', $receiversIds)->get();
        return view('business.bell_notifications.edit')->with(compact('notification', 'receivers'));
    }

    /**
     * Display a notification view.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTemplate($id, $template_for)
    {
        $business_id = request()->session()->get('user.business_id');

        $notification_template = NotificationTemplate::getTemplate($business_id, $template_for);

        $contact = null;
        $transaction = null;
        if ($template_for == 'new_booking') {
            $transaction = Booking::where('business_id', $business_id)
                            ->with(['customer'])
                            ->find($id);

            $contact = $transaction->customer;
        } elseif ($template_for == 'send_ledger') {
            $contact = Contact::find($id);
        } else {
            $transaction = Transaction::where('business_id', $business_id)
                            ->with(['contact'])
                            ->find($id);

            $contact = $transaction->contact;
        }

        $customer_notifications = NotificationTemplate::customerNotifications();
        $supplier_notifications = NotificationTemplate::supplierNotifications();
        $general_notifications = NotificationTemplate::generalNotifications();

        $template_name = '';

        $tags = [];
        if (array_key_exists($template_for, $customer_notifications)) {
            $template_name = $customer_notifications[$template_for]['name'];
            $tags = $customer_notifications[$template_for]['extra_tags'];
        } elseif (array_key_exists($template_for, $supplier_notifications)) {
            $template_name = $supplier_notifications[$template_for]['name'];
            $tags = $supplier_notifications[$template_for]['extra_tags'];
        } elseif (array_key_exists($template_for, $general_notifications)) {
            $template_name = $general_notifications[$template_for]['name'];
            $tags = $general_notifications[$template_for]['extra_tags'];
        }

        //for send_ledger notification template
        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');
        $ledger_format = request()->input('format');
        $location_id = request()->input('location_id');

        return view('notification.show_template')
                ->with(compact('notification_template', 'transaction', 'tags', 'template_name', 'contact', 'start_date', 'end_date', 'ledger_format', 'location_id'));
    }

    /**
     * Sends notifications to customer and supplier
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        // if (!auth()->user()->can('send_notification')) {
        //     abort(403, 'Unauthorized action.');
        // }
        $notAllowed = $this->notificationUtil->notAllowedInDemo();
        if (! empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $customer_notifications = NotificationTemplate::customerNotifications();
            $supplier_notifications = NotificationTemplate::supplierNotifications();

            $data = $request->only(['to_email', 'subject', 'email_body', 'mobile_number', 'sms_body', 'notification_type', 'cc', 'bcc', 'whatsapp_text']);

            $emails_array = array_map('trim', explode(',', $data['to_email']));

            $transaction_id = $request->input('transaction_id');
            $business_id = request()->session()->get('business.id');

            $transaction = ! empty($transaction_id) ? Transaction::find($transaction_id) : null;

            $orig_data = [
                'email_body' => $data['email_body'],
                'sms_body' => $data['sms_body'],
                'subject' => $data['subject'],
                'whatsapp_text' => $data['whatsapp_text'],
            ];

            if ($request->input('template_for') == 'new_booking') {
                $tag_replaced_data = $this->notificationUtil->replaceBookingTags($business_id, $orig_data, $transaction_id);

                $data['email_body'] = $tag_replaced_data['email_body'];
                $data['sms_body'] = $tag_replaced_data['sms_body'];
                $data['subject'] = $tag_replaced_data['subject'];
                $data['whatsapp_text'] = $tag_replaced_data['whatsapp_text'];
            } else {
                $tag_replaced_data = $this->notificationUtil->replaceTags($business_id, $orig_data, $transaction_id);

                $data['email_body'] = $tag_replaced_data['email_body'];
                $data['sms_body'] = $tag_replaced_data['sms_body'];
                $data['subject'] = $tag_replaced_data['subject'];
                $data['whatsapp_text'] = $tag_replaced_data['whatsapp_text'];
            }

            $data['email_settings'] = request()->session()->get('business.email_settings');

            $data['sms_settings'] = request()->session()->get('business.sms_settings');

            $notification_type = $request->input('notification_type');

            $whatsapp_link = '';
            if (array_key_exists($request->input('template_for'), $customer_notifications)) {
                if (in_array('email', $notification_type)) {
                    if (! empty($request->input('attach_pdf'))) {
                        $data['pdf_name'] = 'INVOICE-'.$transaction->invoice_no.'.pdf';
                        $data['pdf'] = $this->transactionUtil->getEmailAttachmentForGivenTransaction($business_id, $transaction_id, true);
                    }

                    Notification::route('mail', $emails_array)
                                    ->notify(new CustomerNotification($data));

                    if (! empty($transaction)) {
                        $this->notificationUtil->activityLog($transaction, 'email_notification_sent', null, [], false);
                    }
                }
                if (in_array('sms', $notification_type)) {
                    $this->notificationUtil->sendSms($data);

                    if (! empty($transaction)) {
                        $this->notificationUtil->activityLog($transaction, 'sms_notification_sent', null, [], false);
                    }
                }
                if (in_array('whatsapp', $notification_type)) {
                    $whatsapp_link = $this->notificationUtil->getWhatsappNotificationLink($data);
                }
            } elseif (array_key_exists($request->input('template_for'), $supplier_notifications)) {
                if (in_array('email', $notification_type)) {
                    if ($request->input('template_for') == 'purchase_order') {
                        $data['pdf_name'] = 'PO-'.$transaction->ref_no.'.pdf';
                        $data['pdf'] = $this->transactionUtil->getPurchaseOrderPdf($business_id, $transaction_id, true);
                    }
                    Notification::route('mail', $emails_array)
                                    ->notify(new SupplierNotification($data));

                    if (! empty($transaction)) {
                        $this->notificationUtil->activityLog($transaction, 'email_notification_sent', null, [], false);
                    }
                }
                if (in_array('sms', $notification_type)) {
                    $this->notificationUtil->sendSms($data);

                    if (! empty($transaction)) {
                        $this->notificationUtil->activityLog($transaction, 'sms_notification_sent', null, [], false);
                    }
                }
                if (in_array('whatsapp', $notification_type)) {
                    $whatsapp_link = $this->notificationUtil->getWhatsappNotificationLink($data);
                }
            }

            $output = ['success' => 1, 'msg' => __('lang_v1.notification_sent_successfully')];
            if (! empty($whatsapp_link)) {
                $output['whatsapp_link'] = $whatsapp_link;
            }
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => $e->getMessage(),
            ];
        }

        return $output;
    }

    
    /**
     * Retrieves users list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEnableUsers()
    {
        if (! auth()->user()->can('business_settings.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $current_business = Business::find($business_id);
            $users = User::whereIn('id', explode(',', $current_business->notification_receivers));

            return Datatables::of($users)
                ->addColumn('username', function ($user) {                    
                    return $user ? $user->surname . ' ' .$user->first_name . ' ' . $user->last_name : '';
                })
                ->addColumn(
                    'action',
                    '<button notify_id="{{$id}}" receiverIds="{{$receiverIds}}" receivers="{{$receivers}}" class="btn btn-xs btn-primary edit-button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\NotificationController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    <a href="#" notify_id="{{$id}}" receiverIds="{{$receiverIds}}" class="btn btn-xs btn-primary update-button hidden"><i class="glyphicon glyphicon-floppy-disk"></i> @lang("messages.update")</a>
                    &nbsp;
                    <a data-href="#" class="btn btn-xs btn-danger cancel_button hidden"><i class="glyphicon glyphicon-remove"></i> @lang("messages.cancel")</a>
                    
                    ')
                ->only(['username', 'action'])
                ->rawColumns(['action'])
                ->make(true);
        }
        return;
        return view('manage_user.index');
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
    public function show()
    {
        if (request()->ajax()) {
            $term = request()->q;
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');
            $query = User::where('business_id', $business_id);
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
            
            return $users;
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
        if (request()->ajax() && isset($request->receivers) && isset($request->id)) {
            $notification = DatabaseNotification::find($request->id);
            $resource = $notification->data;
            $resource['receivers'] = $request->receivers;
            $notification->data = $resource;
            $notification->save();
            return true;
        }
        return false;
    }
}
