<?php

namespace App\Http\Controllers\EForms\PurchaseOrder;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Main\ActivityLogsController;
use App\Mail\SendMail;
use App\Models\EForms\HotelAccommodation\HotelAccommodationAccountModel;
use App\Models\Eforms\HotelAccommodation\HotelAccommodationModel;
use App\Models\Eforms\PurchaseOrder\PurchaseOrderModel;
use App\Models\EForms\HotelAccommodation\HotelSuppliersModel;
use App\Models\Main\AccountsChartModel;
use App\Models\Main\AttachedFileModel;
use App\Models\Main\EformApprovalsModel;
use App\Models\Main\EFormModel;
use App\Models\Main\ProfileAssigmentModel;
use App\Models\Main\ProfileDelegatedModel;
use App\Models\Main\ProfileModel;
use App\Models\Main\ProjectsModel;
use App\Models\Main\StatusModel;
use App\Models\Main\TotalsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Mockery\CountValidator\Exception;


class PurchaseOrderController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Store a piece of data in the session...
        session(['eform_id' => config('constants.eforms_id.purchase_order')]);
        session(['eform_code' => config('constants.eforms_name.purchase_order')]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request, $value)
    {

//       dd(1111);
        //get list of all petty cash forms for today
        if ($value == "all") {
            $list = PurchaseOrderModel::orderBy('code')->paginate(50);
            $category = "All";
        } else if ($value == "pending") {
            $list = PurchaseOrderModel::where('config_status_id', '>', config('constants.purchase_order_status.new_application'))
                ->where('config_status_id', '<', config('constants.purchase_order_status.closed'))
                ->orderBy('code')->paginate(50);
            $category = "Opened";
        } else if ($value == config('constants.purchase_order_status.new_application')) {
            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.new_application'))
                ->orderBy('code')->paginate(50);
            $category = "New Application";
        } else if ($value == config('constants.purchase_order_status.closed')) {
            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.closed'))
                ->orderBy('code')->paginate(50);
            $category = "Closed";
            //  dd(11);
        } else if ($value == config('constants.purchase_order_status.rejected')) {
            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.rejected'))
                ->orderBy('code')->paginate(50);
            $category = "Rejected";
        }
//        else if ($value == config('constants.purchase_order_status.cancelled')) {
//            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status. cancelled'))
//                ->orderBy('code')->paginate(50);
//            $category = "Cancelled";
//        }
        else if ($value == config('constants.purchase_order_status.void')) {
            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.void'))
                ->orderBy('code')->paginate(50);
            $category = "Void";
        }
//        else if ($value == config('constants.purchase_order_status.audited')) {
//            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.audited'))
//                ->orderBy('code')->paginate(50);
//            $category = "Audited";
//        } else if ($value == config('constants.purchase_order_status.queried')) {
//            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.queried'))
//                ->orderBy('code')->paginate(50);
//            $category = "Queried";
//        }

        else if ($value == "needs_me") {
            $list = $totals_needs_me = HomeController::needsMeList();
            $category = "Needs My Attention";
        } else if ($value == "admin") {
            $list = PurchaseOrderModel::where('config_status_id', 0)
                ->orderBy('code')->paginate(50);
        }

        //count all
        $totals = TotalsModel::where('eform_id', config('constants.eforms_id.purchase_order'))->get();

        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();
        //pending forms for me before i apply again
        $pending = HomeController::pendingForMe();

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me,
            'list' => $list,
            'totals' => $totals,
            'pending' => $pending,
            'category' => $category,
        ];

        //return view
        return view('eforms.purchase-order.list')->with($params);

    }


    /**
     * Display a listing of the resource for the admin.
     *
     * @return \Illuminate\HtFtp\Response
     */
    public function records(Request $request, $value)
    {
        //get list of all petty cash forms for today
        if ($value == "all") {

            $list = DB::table('eform_hotel_accomodation')
                ->select('eform_hotel_accomodation.*', 'config_status.name as status_name ', 'config_status.html as html ' )
                ->join('config_status' , 'eform_hotel_accomodation.config_status_id', '=', 'config_status.id')
                ->paginate(50);

            $category = "All Records";
        } else if ($value == "pending") {
            $list = PurchaseOrderModel::where('config_status_id', '>', config('constants.purchase_order_status.new_application'))
                ->where('config_status_id', '<', config('constants.purchase_order_status.closed'))
                ->orderBy('code')->paginate(50);
            $category = "Opened";
        } else if ($value == config('constants.purchase_order_status.new_application')) {

            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.new_application'))
                ->orderBy('code')->paginate(50);
            $category = "New Application";

        } else if ($value == config('constants.purchase_order_status.closed')) {

            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.closed'))
                ->orderBy('code')->paginate(50);
            $category = "Closed";

        } else if ($value == config('constants.purchase_order_status.rejected')) {

            $list = PurchaseOrderModel::where('config_status_id', config('constants.purchase_order_status.rejected'))
                ->orderBy('code')->paginate(50);

            $category = "Rejected";

        } else if ($value == "needs_me") {

            $list = $totals_needs_me = HomeController::needsMeList();

            $category = "Needs My Attention";

        } else if ($value == "admin") {

        }


        //count all
        $totals = TotalsModel::where('eform_id', config('constants.eforms_id.purchase_order'))->get();

        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();

        //pending forms for me before i apply again
        $pending = HomeController::pendingForMe();

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me,
            'list' => $list,
            'totals' => $totals,
            'pending' => $pending,
            'category' => $category,
        ];

        //return view
        return view('eforms.hotel.accommodation.records')->with($params);

    }

    /**
     * Mark the form as void.
     *
     * @return Response
     */
    public function void(Request $request, $id)
    {
        //GET THE HOTEL ACCOMMODATION MODEL
        $list = DB::select("SELECT * FROM eform_purchase_orders where id = {$id} ");
        $form = PurchaseOrderModel::hydrate($list)->first();
        //get the status
        $current_status = $form->status->id;
        $new_status = 0;
        $user = Auth::user();
        //get the form type
        $eform_purchase_order = EFormModel::find(config('constants.eforms_id.purchase_order'));

        //HANDLE VOID REQUEST
        $new_status = config('constants.purchase_order_status.void');

        //update the totals rejected
        $totals = TotalsModel::where('eform_id', config('constants.eforms_id.purchase_order'))
            ->where('id', config('constants.totals.purchase_order_reject'))
            ->first();
        $totals->value = $totals->value + 1;
        $totals->save();
        $eform_purchase_order->total_rejected = $totals->value;
        $eform_purchase_order->save();

        //update the totals open
        $totals = TotalsModel::where('eform_id', config('constants.eforms_id.purchase_order'))
            ->where('id', config('constants.totals.purchase_order_open'))
            ->first();
        $totals->value = $totals->value - 1;
        $totals->save();
        $eform_purchase_order->total_pending = $totals->value;
        $eform_purchase_order->save();

        //get status id
        $status_model = StatusModel::where('id', $new_status)
            ->where('eform_id', config('constants.eforms_id.purchase_order'))->first();
        $new_status = $status_model->id;

        //update the form status
        $form->config_status_id = $new_status;
        $form->save();

        //save reason
        $reason = EformApprovalsModel::Create(
            [
                'profile' => $user->profile_id,
                'title' => $user->profile_id,
                'name' => $user->name,
                'staff_no' => $user->staff_no,
                'reason' => $request->reason,
                'action' => $request->approval,
                'current_status_id' => $current_status,
                'action_status_id' => $new_status,
                'config_eform_id' => config('constants.eforms_id.purchase_order'),
                'eform_id' => $form->id,
                'created_by' => $user->id,
            ]);

        //redirect home
        return Redirect::back()->with('message', 'Purchase Order ' . $form->code . ' for has been marked as Void successfully');

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
//        dd(1111);
        $user = auth()->user();
      //  $projects = ProjectsModel::all();
        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();
    //    $hotel = HotelSuppliersModel::all();
        $list = DB::select("SELECT document_no from purchase_order_header
                                  where status = '02'
            ");
          //  $list = HotelSuppliersModel::hydrate($list)->all();

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me,
          //  'projects' => $projects,
            'user' => $user,
            'po' => $list,
        ];
        //show the create form
        return view('eforms.purchase-order.create')->with($params);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //[1]get the logged in user
        $user = Auth::user();   //superior_code
        $error = false;

      //  dd($user->user_unit);

        //[1B] check pending forms for me before i apply again
//        $pending = HomeController::pendingForMe();
//        if ($pending >= 1) {
//            //return with error msg
//            return Redirect::route('hotel.accommodation-home')->with('error', 'Sorry, You can not raise a new petty cash because you already have an open petty cash. Please allow the opened one to be closed or cancelled');
//        }

        //[2A] find my code superior
        $my_hods = self::findMyNextPerson(config('constants.purchase_order_status.new_application'), $user->user_unit, $user);

        if (empty($my_hods)) {
            //prepare details
            $details = [
                'name' => "Team",
                'url' => 'purchase.order.home',
                'subject' => "purchase-order-Voucher Path Configuration Needs Your Attention",
                'title' => "Path Configuration Not Defined For {$user->name}",
                'body' => "Please note that {$user->name} with Staff Number {$user->staff_no} and Phone/Extension {$user->phone}, managed to submit or raise new purchase-order voucher.
                     <br>But the voucher path is not completely configured. Please confirm that this is so and take action to correct this as soon as possible.
                     <br><br>
                     <b> Path for {$user->user_unit->user_unit_code} user-unit </b><br>
                   1: Checker (User Department) -> {$user->user_unit->ch_code} : {$user->user_unit->ch_unit}  <br>
                   2: Approver (User Department) ->  {$user->user_unit->hod_code} : {$user->user_unit->hod_unit} <br>
                   3: Reinstater (Procurement) -> {$user->user_unit->re_code} : {$user->user_unit->re_unit}  <br>

                   Please assign the correct position code and position user-unit for {$user->user_unit->user_unit_code}. <br>
                <br>You can update the details by clicking on 'Purchase Order Work Flow' menu, then search for {$user->user_unit->user_unit_code}
                 and 'Edit' to update the correct details . <br> <br>
                 Else the HOD has not registered or assigned the correct profile yet.
                 "
            ];

            //send emails
            $to = config('constants.team_email_list');
         //   $mail_to_is = Mail::to($to)->send(new SendMail($details));

            $error = true;
            //return with error msg

             }

        //generate the purchase order unique code
        $code = self::randGenerator("PO", 1);

        //raise the voucher
        $formModel = PurchaseOrderModel::firstOrCreate(
            [
                'code'=> $request->purchase_order_no,
                'purchase_order_no' => $request->purchase_order_no,
                'reason_for_reinstatement'=> $request->reason_for_reinstatement,
                'purchase_order_value'=> $request->purchase_order_value,

                'staff_name'=> $request->employee_staff_name,
                'job_title'=> $request->employee_job_title,
                'staff_no'=> $request->employee_staff_no,



            ],
            [
                'purchase_order_no' => $request->purchase_order_no,
                'reason_for_reinstatement'=> $request->reason_for_reinstatement,
                'purchase_order_value'=> $request->purchase_order_value,

                'staff_name'=> $request->employee_staff_name,
                'job_title'=> $request->employee_job_title,
                'staff_no'=> $request->employee_staff_no,
                'employee_claim_date'=> $request->employee_claim_date,

                'config_status_id' => config('constants.purchase_order_status.new_application'),
                'profile' => Auth::user()->profile_id,
                'created_by' => $user->id,

                'ch_code' => $user->user_unit->ch_code,
                'ch_unit' => $user->user_unit->ch_unit,
                'hod_code' => $user->user_unit->hod_code,
                'hod_unit' => $user->user_unit->hod_unit,
                're_code' => $user->user_unit->re_code,
                're_unit' => $user->user_unit->re_unit,

//                'dr_code' => $user->user_unit->dr_code ?? "0",
//                'dr_unit' => $user->user_unit->dr_unit  ?? "0",

                'cost_center' => $user->user_unit->user_unit_cc_code,
                'business_code' => $user->user_unit->user_unit_bc_code,
                'user_unit_code' => $user->user_unit->user_unit_code,
            ]);


        /** upload quotation files */
        // upload the receipt files
        $files = $request->file('quotation');
        if ($request->hasFile('quotation')) {
            foreach ($files as $file) {
                $filenameWithExt =  preg_replace("/[^a-zA-Z]+/", "_",  $file->getClientOriginalName());
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                //get size
                $size = number_format($file->getSize() * 0.000001, 2);
                // Get just ext
                $extension = $file->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = trim(preg_replace('/\s+/', ' ', $filename . '_' . time() . '.' . $extension));
                // Upload File
                $path = $file->storeAs('public/purchase_order_quotation', $fileNameToStore);

                //upload the receipt
                $file = AttachedFileModel::updateOrCreate(
                    [
                        'name' => $fileNameToStore,
                        'location' => $path,
                        'extension' => $extension,
                        'file_size' => $size,
                        'form_id' => $formModel->code,
                        'form_type' => config('constants.eforms_id.purchase_order'),
                        'file_type' => config('constants.file_type.quotation')
                    ],
                    [
                        'name' => $fileNameToStore,
                        'location' => $path,
                        'extension' => $extension,
                        'file_size' => $size,
                        'form_id' => $formModel->code,
                        'form_type' => config('constants.eforms_id.purchase_order'),
                        'file_type' => config('constants.file_type.quotation')
                    ]
                );
            }
        }

        /** update the totals */
//        $totals = TotalsModel::where('eform_id', config('constants.eforms_id.purchase_order'))
//            ->where('id', config('constants.totals.purchase_order_new'))
//            ->first();
//        $totals->value = $totals->value + 1;
//        $totals->save();
//        $eform_purchase_order = EFormModel::find(config('constants.eforms_id.purchase_order'));
//        $eform_purchase_order->total_new = $totals->value;
//        $eform_purchase_order->save();

        /** send email to supervisor */
        //get team email addresses

        $names = "";
        $to = [];
        //add hods email addresses
        foreach ($my_hods as $item) {
            $to[] = ['email' => $item->email, 'name' => $item->name];
            $names = $names . '<br>' . $item->name;
        }

        //prepare details
        $details = [
            'name' => $names,
            'url' => 'purchase.order.home',
            'subject' => "New purchase-order Voucher Needs Your Attention",
            'title' => "New purchase-order Voucher Needs Your Attention {$user->name}",
            'body' => "Please note that {$user->name} with Staff Number {$user->staff_no} has successfully raised a purchase-order voucher with
                   <br> Serial: {$formModel->code}  <br> Reference: {$formModel->ref_no} <br> Status: {$formModel->status->name}  and <br> <b>Amount: ZMW {$request->total_payment}</b></br>. <br>
            This voucher now needs your approval, kindly click on the button below to login to E-ZESCO and take action on the voucher.<br> regards. "
        ];
        // send mail
//        $mail_to_is = Mail::to($to)->send(new SendMail($details));

        // log the activity
        ActivityLogsController::store($request, "Creating of Purchase Order", "update", " pay point created", $formModel->id);

        if ($error) {
            dd ($error);
            // return with error msg
            return Redirect::route('purchase.order.home')->with('error', 'Sorry!, The superior who is supposed to approve your purchase order,
                       <br> has not registered or not fully configured yet, Please, <b>try first contacting your superior</b> so as to make sure he/she has registered in the system,
                       then you can contact eZESCO Admins (1142,1126,2350,2345,3309,3306 or 3319) isd@zesco.co.zm to configure your petty cash voucher path. Your purchase-order voucher has been saved.');
        } else {
            // return the view
            return Redirect::route('purchase.order.home')->with('message', 'Purchase Order Details for ' . $formModel->code . ' have been Created successfully');
        }
    }


    /**
     * Fetch a list of my HODs
     * @param $user
     * @return array
     */
    public function findMyNextPerson($current_status, $user_unit, $claimant)
    {
        $users_array = [];
        $not_claimant = true;

        //FOR MY HOD USERS
        if ($current_status == config('constants.purchase_order_status.new_application')) {
            $superior_user_unit = $user_unit->ch_code;
            $superior_user_code = $user_unit->ch_unit;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_016'));

        } elseif ($current_status == config('constants.purchase_order_status.hod_approved')) {
            $superior_user_code = $user_unit->hod_code;
            $superior_user_unit = $user_unit->hod_unit;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_004'));

        } elseif ($current_status == config('constants.purchase_order_status.re_approved')) {
            $superior_user_code = $user_unit->re_code;
            $superior_user_unit = $user_unit->re_unit;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_017'));

//        } elseif ($current_status == config('constants.purchase_order_status.chief_accountant_approved')) {
//            $superior_user_unit = $user_unit->expenditure_unit;
//            $superior_user_code = $user_unit->expenditure_unit;
//            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_014'));

        } elseif ($current_status == config('constants.purchase_order_status.funds_disbursement')) {
            $not_claimant = false;

        } elseif ($current_status == config('constants.purchase_order_status.funds_acknowledgement')) {
            $superior_user_unit = $user_unit->security_unit;
            $superior_user_code = $user_unit->security_code;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_013'));

        } elseif ($current_status == config('constants.purchase_order_status.security_approved')) {
            $superior_user_unit = $user_unit->expenditure_unit;
            $superior_user_code = $user_unit->expenditure_unit;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_014'));
            // dd(1);
        } elseif ($current_status == config('constants.purchase_order_status.closed')) {
            $superior_user_unit = $user_unit->audit_unit;
            $superior_user_code = $user_unit->audit_unit;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_011'));
            // dd(1);
        } else {
            //no one
            $superior_user_unit = "0";
            $superior_user_code = "0";
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_002'));
        }

        if ($not_claimant) {
            //SELECT USERS
            $users_list[] = '';
            //[A]check for any users who have this assigned profile
            $assigned_users = ProfileAssigmentModel::
            where('eform_id', config('constants.eforms_id.purchase_order'))
                ->where('profile', $profile->code)
                ->get();
            //loop through assigned users
            foreach ($assigned_users as $item) {
                if ($profile->id == config('constants.user_profiles.EZESCO_014') ||
                    $profile->id == config('constants.user_profiles.EZESCO_011') ||
                    $profile->id == config('constants.user_profiles.EZESCO_013')) {
                    //expenditure, audit and security
                    $my_superiors = User::where('user_unit_code', $superior_user_unit)
                        ->where('id', $item->user_id)
                        ->get();
                    foreach ($my_superiors as $item) {
                        $users_array[] = $item;
                    }
                } else {
                    //hod, hr, ca
                    $my_superiors = User::where('user_unit_code', $superior_user_unit)
                        ->where('job_code', $superior_user_code)
                        ->where('id', $item->user_id)
                        ->get();
                    foreach ($my_superiors as $item) {
                        $users_array[] = $item;
                    }
                }

            }
            //[B]check if one the users with the profile have this delegated profile
            $delegated_users = ProfileDelegatedModel::
            where('eform_id', config('constants.eforms_id.purchase_order'))
                ->where('delegated_profile', $profile->id)
                ->where('delegated_job_code', $superior_user_code)
                ->where('delegated_user_unit', $superior_user_unit)
                ->where('config_status_id', config('constants.active_state'))
                ->get();
            //loop through delegated users
            foreach ($delegated_users as $item) {
                $user = User::find($item->delegated_to);
                $users_array[] = $user;
            }

        } else {
            $users_array[] = $claimant;
        }

        //[3] return the list of users
        return $users_array;
    }


    /**
     * Generate Voucher Code
     * @param $head
     * @return string
     */
    public function randGenerator($head, $value)
    {
        // use the total number of petty cash in the system
        $count = DB::select("SELECT count(id) as total FROM EFORM_purchase_orders");

        //random number
        // $random = rand(1, 9999999);
        $random = $count[0]->total;  // count total and begin again
        // $random = $size->total ;  // oracle sequence
        $random = sprintf("%07d", ($random + $value));
        $random = $head . $random;

        $count_existing_forms = DB::select("SELECT count(id) as total FROM EFORM_purchase_orders WHERE code = '{$random}'");
        try {
            $total = $count_existing_forms[0]->total;
        } catch (\Exception $exception) {
            $total = 0;
        }

        if ($total < 1) {
            return $random;
        } else {
            return self::randGenerator("PO", $value);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {

        //GET THE PETTY CASH MODEL if you are an admin
        //  if (Auth::user()->type_id == config('constants.user_types.developer')) {
        $list = DB::select("SELECT * FROM eform_purchase_orders where id = {$id} ");
        $form = PurchaseOrderModel::hydrate($list)->first();

        $receipts = AttachedFileModel::where('form_id', $form->code)
            ->where('form_type', config('constants.eforms_id.purchase_order'))
            ->where('file_type', config('constants.file_type.receipt'))
            ->get();
        $quotations = AttachedFileModel::where('form_id', $form->code)

            ->where('form_type', config('constants.eforms_id.purchase_order'))
            ->where('file_type', config('constants.file_type.quotation'))
            ->get();
//        dd($quotations);
        $form_accounts = PurchaseOrderModel::where('id', $id)->get();
      //  $projects = ProjectsModel::all();
        $accounts = AccountsChartModel::all();
        $approvals = EformApprovalsModel::where('eform_id', $form->id)->where('config_eform_id', config('constants.eforms_id.purchase_order'))
            ->orderBy('created_at', 'asc')->get();

        $user = User::find($form->created_by);
        $hotel= HotelSuppliersModel::all();
        //IMPORTANT AS WELL
        $user_array = self::findMyNextPerson($form->config_status_id, $user->user_unit, $user);

        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();

        //data to send to the view
        $params = [
            'receipts' => $receipts,
            'quotations' => $quotations,
            'form_accounts' => $form_accounts,
            'totals_needs_me' => $totals_needs_me,
            'form' => $form,
            'user_array' => $user_array,
            'approvals' => $approvals,
            'accounts' => $accounts,
            'user' => Auth::user(),
            'hotel' => $hotel


        ];
        //return view

        return view('eforms.hotel-accommodation.show')->with($params);

    }


    public function showForm($id)
    {
        //GET THE PETTY CASH MODEL if you are an admin
        $list = DB::select("SELECT * FROM eform_purchase_orders where id = {$id} ");
        $form = PurchaseOrderModel::hydrate($list)->first();

        $receipts = AttachedFileModel::where('form_id', $form->code)
            ->where('form_type', config('constants.eforms_id.purchase_order'))
            ->where('file_type', config('constants.file_type.receipt'))
            ->get();
        $quotations = AttachedFileModel::where('form_id', $form->code)
            ->where('form_type', config('constants.eforms_id.purchase_order'))
            ->where('file_type', config('constants.file_type.quotation'))
            ->get();
        $form_accounts = PurchaseOrderModel::where('eform_purchase_order_id', $id)->get();
        $projects = ProjectsModel::all();
        $accounts = AccountsChartModel::all();
        $approvals = EformApprovalsModel::where('eform_id', $form->id)->where('config_eform_id', config('constants.eforms_id.purchase_order'))
            ->orderBy('created_at', 'asc')->get();

        $user = User::find($form->created_by);
        $user_array = self::findMyNextPerson($form->config_status_id, $user->user_unit, $user);

        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();

        //data to send to the view
        $params = [
            'receipts' => $receipts,
            'quotations' => $quotations,
            'form_accounts' => $form_accounts,
            'totals_needs_me' => $totals_needs_me,
            'form' => $form,
            'projects' => $projects,
            'user_array' => $user_array,
            'approvals' => $approvals,
            'accounts' => $accounts
        ];
        //return view
        return view('eforms.hotel-accommodation.show')->with($params);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function approve(Request $request)
    {
        //GET THE PETTY CASH MODEL
        $form = PurchaseOrderModel::find($request->id);
        $current_status = $form->status->id;
        $user = Auth::user();

        //FOR FOR CLAIMANT CANCELLATION
        if (
            Auth::user()->profile_id == config('constants.user_profiles.EZESCO_002')
            && $current_status == config('constants.purchase_order_status.new_application')
        ) {
            //cancel status
            $insert_reasons = true;
            if ($request->approval == config('constants.approval.cancelled')) {
                $new_status = config('constants.purchase_order_status.cancelled');
            } //reject status
            elseif ($request->approval == config('constants.approval.reject')) {
                $new_status = config('constants.purchase_order_status.cancelled');
            }//approve status
            elseif ($request->approval == config('constants.approval.approve')) {
                $new_status = config('constants.purchase_order_status.cancelled');
            } else {
                $new_status = config('constants.purchase_order_status.new_application');
                $insert_reasons = false;
            }
            $form->config_status_id = $new_status;
            $form->profile = Auth::user()->profile_id;
            $form->save();

        } //FOR HOD
        elseif (
            Auth::user()->profile_id == config('constants.user_profiles.EZESCO_004')
            && $current_status == config('constants.purchase_order_status.new_application')
        ) {
            //cancel status
            $insert_reasons = true;
            if ($request->approval == config('constants.approval.cancelled')) {
                $new_status = config('constants.purchase_order_status.cancelled');
            } //reject status
            elseif ($request->approval == config('constants.approval.reject')) {
                $new_status = config('constants.purchase_order_status.rejected');
            }//approve status
            elseif ($request->approval == config('constants.approval.approve')) {
                $new_status = config('constants.purchase_order_status.hod_approved');
            } else {
                $new_status = config('constants.purchase_order_status.new_application');
                $insert_reasons = false;
            }
            //update
            $form->config_status_id = $new_status;
            $form->hod_name = $user->name;
            $form->hod_staff_no = $user->staff_no;
            $form->hod_authorised_date = $request->sig_date;
            $form->profile = Auth::user()->profile_id;
            $form->save();

        }
        //FOR FOR  DIRECTOR
        elseif (Auth::user()->profile_id == config('constants.user_profiles.EZESCO_003')
            && $current_status == config('constants.purchase_order_status.hod_approved')
        ) {
            $insert_reasons = true;
            //cancel status
            if ($request->approval == config('constants.approval.cancelled')) {
                $new_status = config('constants.purchase_order_status.cancelled');
            } //reject status
            elseif ($request->approval == config('constants.approval.reject')) {
                $new_status = config('constants.purchase_order_status.rejected');
            }//approve status
            elseif ($request->approval == config('constants.approval.approve')) {
                $new_status = config('constants.purchase_order_status.director_approved');
            } else {
                $new_status = config('constants.purchase_order_status.hod_approved');
                $insert_reasons = false;
            }
            //update
            $form->config_status_id = $new_status;
            $form->director = $user->name;
            $form->director_staff_no = $user->staff_no;
            $form->director_authorised_date = $request->sig_date;
            $form->profile = Auth::user()->profile_id;
            $form->save();
        }
        //FOR CHIEF ACCOUNTANT
        elseif (
            Auth::user()->profile_id == config('constants.user_profiles.EZESCO_007')
            && $current_status == config('constants.purchase_order_status.director_approved')
        ) {
            //cancel status
            $insert_reasons = true;
            if ($request->approval == config('constants.approval.cancelled')) {
                $new_status = config('constants.purchase_order_status.cancelled');
            } //reject status
            elseif ($request->approval == config('constants.approval.reject')) {
                $new_status = config('constants.purchase_order_status.rejected');
            }//approve status
            elseif ($request->approval == config('constants.approval.approve')) {
                $new_status = config('constants.purchase_order_status.chief_accountant_approved');
            } else {
                $new_status = config('constants.purchase_order_status.director_approved');
                $insert_reasons = false;
            }

            //update
            $form->config_status_id = $new_status;
            $form->chief_accountant_name = $user->name;
            $form->chief_accountant_staff_no = $user->staff_no;
            $form->chief_accountant_date = $request->sig_date;
            $form->profile = Auth::user()->profile_id;
            $form->save();

        }

        //FOR FOR EXPENDITURE OFFICE FUNDS
        elseif (Auth::user()->profile_id == config('constants.user_profiles.EZESCO_014')
            && $current_status == config('constants.purchase_order_status.chief_accountant_approved')
        ) {
            //cancel status
            $insert_reasons = true;
            if ($request->approval == config('constants.approval.cancelled')) {
                $new_status = config('constants.purchase_order_status.cancelled');
            } //reject status
            elseif ($request->approval == config('constants.approval.reject')) {
                $new_status = config('constants.purchase_order_status.rejected');
            }//approve status
            elseif ($request->approval == config('constants.approval.approve')) {
                $new_status = config('constants.purchase_order_status.closed');
            } else {
                $new_status = config('constants.purchase_order_status.chief_accountant_approved');
                $insert_reasons = false;
            }
//            dd($request->all());
            //update
            $form->config_status_id = $new_status;
//            $form->expenditure_office = $user->name;
//            $form->expenditure_office_staff_no = $user->staff_no;
//            $form->expenditure_date = $request->sig_date;
            $form->profile = Auth::user()->profile_id;
            $form->save();



            /** upload quotation files */
            // upload the receipt files
            $files = $request->file('quotation');
            if ($request->hasFile('quotation')) {
                foreach ($files as $file) {
                    $filenameWithExt = preg_replace("/[^a-zA-Z]+/", "_", $file->getClientOriginalName());
                    // Get just filename
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    //get size
                    $size = number_format($file->getSize() * 0.000001, 2);
                    // Get just ext
                    $extension = $file->getClientOriginalExtension();
                    // Filename to store
                    $fileNameToStore = trim(preg_replace('/\s+/', ' ', $filename . '_' . time() . '.' . $extension));
                    // Upload File
                    $path = $file->storeAs('public/purchase_order', $fileNameToStore);

                    //upload the receipt
                    $file = AttachedFileModel::updateOrCreate(
                        [
                            'name' => $fileNameToStore,
                            'location' => $path,
                            'extension' => $extension,
                            'file_size' => $size,
                            'form_id' => $form->code,
                            'form_type' => config('constants.eforms_id.purchase_order'),
                            'file_type' => config('constants.file_type.quotation')
                        ],
                        [
                            'name' => $fileNameToStore,
                            'location' => $path,
                            'extension' => $extension,
                            'file_size' => $size,
                            'form_id' => $form->code,
                            'form_type' => config('constants.eforms_id.purchase_order'),
                            'file_type' => config('constants.file_type.quotation')
                        ]
                    );
                }
            }


            //create records for the accounts associated with this petty cash transaction
//            for ($i = 0; $i < sizeof($request->credited_amount); $i++) {
//                $des = "";
//                $des = $des . " " . $request->account_items[$i] . ",";
//                $des = "hotel-accommodation Serial: " . $form->code . ", Claimant: " . $form->claimant_name . ', Items : ' . $des . ' Amount: ' . $request->credited_amount[$i] . '.';
//
//                //[1] CREDITED ACCOUNT
//                //[1A] - money
//                $formAccountModel = HotelAccommodationAccountModel::updateOrCreate(
//                    [
//                        'creditted_account_id' => $request->credited_account[$i],
//                        'creditted_amount' => $request->credited_amount[$i],
//                        'account' => $request->credited_account[$i],
//                        'debitted_account_id' => $request->debited_account[$i],
//                        //'debitted_amount' => $request->debited_amount[$i],
//                        'eform_purchase_order_id' => $form->id,
//                        'created_by' => $user->id,
//                        'company' => '01',
//                        'intra_company' => '01',
//                        'project' => $form->project->code ?? "",
//                        'pems_project' => 'N',
//                        'spare' => '0000',
//                        'status_id' => config('constants.purchase_order_status.export_not_ready')
//                    ],
//                    [
//                        'creditted_account_id' => $request->credited_account[$i],
//                        'creditted_amount' => $request->credited_amount[$i],
//                        'account' => $request->credited_account[$i],
//                        'debitted_account_id' => $request->debited_account[$i],
//                        //'debitted_amount' => $request->debited_amount[$i],
//
//                        'eform_purchase_order_id' => $form->id,
//                        'purchase_order_code' => $form->code,
//                        'cost_center' => $form->cost_center,
//                        'business_unit_code' => $form->business_unit_code,
//                        'user_unit_code' => $form->user_unit_code,
//                        'claimant_name' => $form->claimant_name,
//                        'claimant_staff_no' => $form->claimant_staff_no,
//                        'claim_date' => $form->claim_date,
//
//                        'hod_code' => $form->hod_code,
//                        'hod_unit' => $form->hod_unit,
//                        'ca_code' => $form->ca_code,
//                        'ca_unit' => $form->ca_unit,
//                        'hrm_code' => $form->hrm_code,
//                        'hrm_unit' => $form->hrm_unit,
//                        'expenditure_code' => $form->expenditure_code,
//                        'expenditure_unit' => $form->expenditure_unit,
//                        'security_code' => $form->security_code,
//                        'security_unit' => $form->security_unit,
//                        'audit_code' => $form->audit_code,
//                        'audit_unit' => $form->audit_unit,
//
//                        'created_by' => $user->id,
//                        'company' => '01',
//                        'intra_company' => '01',
//                        'project' => $form->project->code ?? "",
//                        'pems_project' => 'N',
//                        'spare' => '0000',
//                        'description' => $des,
//                        'status_id' => config('constants.purchase_order_status.export_not_ready')
//                    ]
//                );
//
//                //[2] DEBITED ACCOUNT
//                //[2A] - money
//                $formAccountModel = HotelAccommodationAccountModel::updateOrCreate(
//                    [
//                        'creditted_account_id' => $request->credited_account[$i],
//                        //'creditted_amount' => $request->credited_amount[$i],
//                        'debitted_account_id' => $request->debited_account[$i],
//                        'debitted_amount' => $request->debited_amount[$i],
//                        'account' => $request->debited_account[$i],
//                        'eform_purchase_order_id' => $form->id,
//                        'created_by' => $user->id,
//                        'company' => '01',
//                        'intra_company' => '01',
//                        'project' => $form->project->code ?? "",
//                        'pems_project' => 'N',
//                        'spare' => '0000',
//                        'status_id' => config('constants.purchase_order_status.export_not_ready')
//                    ],
//                    [
//                        'creditted_account_id' => $request->credited_account[$i],
//                        //'creditted_amount' => $request->credited_amount[$i],
//                        'debitted_account_id' => $request->debited_account[$i],
//                        'debitted_amount' => $request->debited_amount[$i],
//                        'account' => $request->debited_account[$i],
//
//                        'eform_purchase_order_id' => $form->id,
//                        'purchase_order_code' => $form->code,
//                        'cost_center' => $form->cost_center,
//                        'business_unit_code' => $form->business_unit_code,
//                        'user_unit_code' => $form->user_unit_code,
//                        'claimant_name' => $form->claimant_name,
//                        'claimant_staff_no' => $form->claimant_staff_no,
//                        'claim_date' => $form->claim_date,
//                        'hod_code' => $form->hod_code,
//                        'hod_unit' => $form->hod_unit,
//                        'ca_code' => $form->ca_code,
//                        'ca_unit' => $form->ca_unit,
//                        'hrm_code' => $form->hrm_code,
//                        'hrm_unit' => $form->hrm_unit,
//                        'expenditure_code' => $form->expenditure_code,
//                        'expenditure_unit' => $form->expenditure_unit,
//                        'security_code' => $form->security_code,
//                        'security_unit' => $form->security_unit,
//                        'audit_code' => $form->audit_code,
//                        'audit_unit' => $form->audit_unit,
//
//                        'created_by' => $user->id,
//                        'company' => '01',
//                        'intra_company' => '01',
//                        'project' => $form->project->code ?? "",
//                        'pems_project' => 'N',
//                        'spare' => '0000',
//                        'description' => $des,
//                        'status_id' => config('constants.purchase_order_status.export_not_ready')
//                    ]
//                );
//            }

        }

        //FOR AUDITING OFFICE
        elseif (Auth::user()->profile_id == config('constants.user_profiles.EZESCO_011')
            && $current_status == config('constants.purchase_order_status.closed')
        ) {
            //cancel status
            $insert_reasons = true;
            if ($request->approval == config('constants.approval.cancelled')) {
                $new_status = config('constants.purchase_order_status.cancelled');
            } //reject status
            elseif ($request->approval == config('constants.approval.reject')) {
                $new_status = config('constants.purchase_order_status.rejected');
            }//approve status
            elseif ($request->approval == config('constants.approval.approve')) {
                $new_status = config('constants.purchase_order_status.audited');
            }//audit status
            elseif ($request->approval == config('constants.approval.queried')) {
                $new_status = config('constants.purchase_order_status.queried');
            } else {
                $new_status = config('constants.purchase_order_status.closed');
                $insert_reasons = false;
            }
            //update
            $form->config_status_id = $new_status;
//            $form->audit_office_name = $user->name;
//            $form->audit_office_staff_no = $user->staff_no;
//            $form->audit_office_date = $request->sig_date;
            $form->profile = Auth::user()->profile_id;
            $form->save();
        }

        //FOR NO-ONE
        else {
            //return with an error
            return Redirect::route('hotel.accommodation.home')->with('message', 'Purchase Order ' . $form->code . ' for has been ' . $request->approval . ' successfully');
        }

        //reason
        if ($insert_reasons) {
            //save reason
            $reason = EformApprovalsModel::updateOrCreate(
                [
                    'profile' => $user->profile_id,
                    'title' => $user->profile_id,
                    'name' => $user->name,
                    'staff_no' => $user->staff_no,
                    'reason' => $request->reason,
                    'current_status_id' => $current_status,
                    'action' => $request->approval,
                    'config_eform_id' => config('constants.eforms_id.purchase_order'),
                    'eform_id' => $form->id,
                    'created_by' => $user->id,
                ],
                [
                    'profile' => $user->profile_id,
                    'title' => $user->profile_id,
                    'name' => $user->name,
                    'staff_no' => $user->staff_no,
                    'reason' => $request->reason,
                    'action' => $request->approval,
                    'current_status_id' => $current_status,
                    'action_status_id' => $new_status,
                    'config_eform_id' => config('constants.eforms_id.purchase_order'),
                    'eform_id' => $form->id,
                    'created_by' => $user->id,
                ]

            );
            //send the email
          //  self::nextUserSendMail($new_status, $form);

        }

        //redirect home
        return Redirect::route('hotel.accommodation.home')->with('message', $form->total_payment . ' hotel-accommodation ' . $form->code . ' for ' . $form->claimant_name . ' has been ' . $request->approval . ' successfully');

    }

    /**
     * Send Email to the Next Person/s who are supposed to work on the form next
     * @param $profile
     * @param $stage
     * @param $claim_staff
     */

    public function nextUserSendMail($new_status, $form)
    {
        //get the users
        $user_array = self::nextUsers($new_status, $form->user_unit, $form->user);
        $names = "";
        $claimant_details = User::find($form->created_by);

        //check if this next profile is for a claimant and if the hotel-accommodation needs Acknowledgement
        if ($new_status == config('constants.purchase_order_status.security_approved')) {
            //message details
            $subject = 'hotel-accommodation Voucher Needs Your Attention';
            $title = 'hotel-accommodation Voucher Needs Your Attention';
            $message = 'This is to notify you that there is a <b>ZMW ' . $form->total_payment . '</b>  hotel-accommodation Voucher (' . $form->code . ') raised by ' . $form->claimant_name . ', that needs your attention.
            <br>Please login to e-ZESCO by clicking on the button below to take action on the voucher.<br>The form is currently at ' . $form->status->name . ' stage';
        } //check if this next profile is for a claimant and if the hotel-accommodation is closed
        else if ($new_status == config('constants.purchase_order_status.closed')) {
            $names = $names . '<br>' . $claimant_details->namee;
            //message details
            $subject = 'hotel-accommodation Voucher Closed Successfully';
            $title = 'hotel-accommodation Voucher Closed Successfully';
            $message = ' Congratulation! This is to notify you that hotel-accommodation voucher ' . $form->code . ' has been closed successfully .
            <br>Please login to e-ZESCO by clicking on the button below to view the voucher. <br>The petty cash voucher has now been closed.';
        } // other wise get the users
        else {
            //message details
            $subject = 'hotel-accommodation Voucher Needs Your Attention';
            $title = 'hotel-accommodation Voucher Needs Your Attention';
            $message = 'This is to notify you that there is a <b>ZMW ' . $form->total_payment . '</b>  hotel-accommodation Voucher (' . $form->code . ') raised by ' . $form->claimant_name . ',that needs your attention.
            <br>Please login to e-ZESCO by clicking on the button below to take action on the voucher.<br>The form is currently at ' . $form->status->name . ' stage.';
        }

        /** send email to supervisor */
        $to = [];
        //add hods email addresses
        foreach ($user_array as $item) {
            //use the pay point
            $to[] = ['email' => $item->email, 'name' => $item->name];
            $to[] = ['email' => $claimant_details->email, 'name' => $claimant_details->name];
            $names = $names . '<br>' . $item->name;
        }

        //  dd($user_array);
        $to[] = ['email' => 'nshubart@zesco.co.zm', 'name' => 'Shubart Nyimbili'];
        $to[] = ['email' => 'csikazwe@zesco.co.zm', 'name' => 'Chapuka Sikazwe'];
        $to[] = ['email' => 'bchisulo@zesco.co.zm', 'name' => 'Bwalya Chisulo'];
        //prepare details
        $details = [
            'name' => $names,
            'url' => 'hotel.accommodation.home',
            'subject' => $subject,
            'title' => $title,
            'body' => $message
        ];
        //send mail
        $mail_to_is = Mail::to($to)->send(new SendMail($details));

    }

    /**
     * List the users who are supposed to work on the form next
     * @param $last_profile
     * @param $current_status
     * @param $claimant_man_no
     * @return array
     */


    public function nextUsers($new_status, $user_unit, $user)
    {
//       dd($user_unit);
        $users_array = [];
        $not_claimant = true;

        //FOR MY HOD USERS
        if ($new_status == config('constants.purchase_order_status.new_application')) {
            $superior_user_unit = $user_unit->hod_unit;
            $superior_user_code = $user_unit->hod_code;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_004'));
        } elseif ($new_status == config('constants.purchase_order_status.hod_approved')) {
            $superior_user_code = $user_unit->ca_code;
            $superior_user_unit = $user_unit->ca_unit;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_009'));
        } elseif ($new_status == config('constants.purchase_order_status.chief_accountant_approved')) {
            $superior_user_code = $user_unit->dr_code;
            $superior_user_unit = $user_unit->dr_unit;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_007'));
        } elseif ($new_status == config('constants.purchase_order_status.director_approved')) {
            $superior_user_unit = $user_unit->expenditure_unit;
            $superior_user_code = $user_unit->expenditure_code;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_014'));
        } elseif ($new_status == config('constants.purchase_order_status.funds_disbursement')) {
            $not_claimant = false;
        } elseif ($new_status == config('constants.purchase_order_status.funds_acknowledgement')) {
            $superior_user_unit = $user_unit->security_unit;
            $superior_user_code = $user_unit->security_code;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_013'));
        } elseif ($new_status == config('constants.purchase_order_status.security_approved')) {
            $superior_user_unit = $user_unit->expenditure_unit;
            $superior_user_code = $user_unit->expenditure_code;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_014'));
        } elseif ($new_status == config('constants.purchase_order_status.closed')) {
            $superior_user_unit = $user_unit->audit_unit;
            $superior_user_code = $user_unit->audit_unit;
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_011'));
            // dd(1);
        } else {
            //no one
            $superior_user_unit = "0";
            $superior_user_code = "0";
            $profile = ProfileModel::find(config('constants.user_profiles.EZESCO_002'));
        }

        if ($not_claimant) {
            //SELECT USERS

            $users_list[] = '';
            //[A]check if the users in my user unit have this assigned profile
            $assigned_users = ProfileAssigmentModel::
            where('eform_id', config('constants.eforms_id.purchase_order'))
                ->where('profile', $profile->code)
                ->get();
            //loop through assigned users
            foreach ($assigned_users as $item) {
                if ($profile->id == config('constants.user_profiles.EZESCO_014') ||
                    $profile->id == config('constants.user_profiles.EZESCO_013') ||
                    $profile->id == config('constants.user_profiles.EZESCO_011')) {
                    //expenditure, audit and security
                    $my_superiors = User::where('user_unit_code', $superior_user_unit)
                        ->where('id', $item->user_id)
                        ->get();
                    foreach ($my_superiors as $item) {
                        $users_array[] = $item;
                    }
                } else {
                    //hod, hr, ca
                    $my_superiors = User::where('user_unit_code', $superior_user_unit)
                        ->where('job_code', $superior_user_code)
                        ->where('id', $item->user_id)
                        ->get();
                    foreach ($my_superiors as $item) {
                        $users_array[] = $item;
                    }
                }

            }
            //[B]check if one the users with the profile have this delegated profile
            $delegated_users = ProfileDelegatedModel::
            where('eform_id', config('constants.eforms_id.purchase_order'))
                ->where('delegated_profile', $profile->id)
                ->where('delegated_job_code', $superior_user_code)
                ->where('delegated_user_unit', $superior_user_unit)
                ->where('config_status_id',  config('constants.active_state'))
                ->get();
            //loop through delegated users
            foreach ($delegated_users as $item) {
                $user = User::find($item->delegated_to);
                $users_array[] = $user;
            }

        } else {
            $users_array[] = $user;
            // $hods_array[] = $user;
        }

        //[3] return the list of users
        return $users_array;
    }


    public function reports(Request $request, $value)
    {
        //get the accounts
        $title = "";

        if ($value == config('constants.all')) {
            if (Auth::user()->type_id == config('constants.user_types.developer')) {
                $list = DB::select("SELECT * FROM eform_purchase_order_account  ");
                $list = PurchaseOrderModel::hydrate($list);
            } else {
                $list = PurchaseOrderModel::all();
            }
            $title = "ALl";
        } elseif ($value == config('constants.purchase_order_status.not_exported')) {
            if (Auth::user()->type_id == config('constants.user_types.developer')) {
                $status = config('constants.purchase_order_status.not_exported');
                $list = DB::select("SELECT * FROM eform_purchase_order_account where status_id = {$status} ");
                $list = PurchaseOrderModel::hydrate($list);
            } else {
                $list = PurchaseOrderModel::where('status_id', config('constants.purchase_order_status.not_exported'))->get();
            }
            $title = "Not Exported";
        } elseif ($value == config('constants.purchase_order_status.exported')) {
            if (Auth::user()->type_id == config('constants.user_types.developer')) {
                $status = config('constants.purchase_order_status.exported');
                $list = DB::select("SELECT * FROM eform_purchase_order_account where status_id = {$status} ");
                $list = PurchaseOrderModel::hydrate($list);
            } else {
                $list = PurchaseOrderModel::where('status_id', config('constants.purchase_order_status.exported'))->get();
            }
            $title = " Exported";
        } elseif ($value == config('constants.purchase_order_status.export_failed')) {
            if (Auth::user()->type_id == config('constants.user_types.developer')) {
                $status = config('constants.purchase_order_status.export_failed');
                $list = DB::select("SELECT * FROM eform_purchase_order_account where status_id = {$status} ");
                $list = PurchaseOrderModel::hydrate($list);
            } else {
                $list = PurchaseOrderModel::where('status_id', config('constants.purchase_order_status.export_failed'))->get();
            }
            $title = "Failed Export";
        }


        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();

        //data to send to the view
        $params = [
            'title' => $title,
            'totals_needs_me' => $totals_needs_me,
            'list' => $list
        ];
        //  dd($list);
        return view('eforms.hotel.accommodation.report')->with($params);
    }

    public function reportsExport(Request $request)
    {
        $fileName = 'purchase_order_Accounts.csv';
        if (Auth::user()->type_id == config('constants.user_types.developer')) {
            $tasks = PurchaseOrderModel::where('status_id', config('constants.purchase_order_status.not_exported'))->get();
        } else {
            $not_exported = config('constants.purchase_order_status.not_exported');
            $tasks = DB::select("SELECT * FROM eform_purchase_order_account
                        WHERE status_id = {$not_exported}
                        ORDER BY eform_purchase_order_id ASC ");
            $tasks = PurchaseOrderModel::hydrate($tasks);
        }


        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = array('Code',
            'Claimant',
            'Claim Date',
            'Company',
            'Business Unit',
            'Cost Center',
            'Account',
            'Project',
            'Intra-Company',
            'Spare',
            'PEMS Project',
            'Debit',
            'Credit',
            'Line Description'
        );

        $callback = function () use ($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $item) {

                //mark the item as exported
//                $item->status_id = config('constants.purchase_order_status.exported');
//                $item->save();

                //Make the update on the petty cash account
                $previous_status = config('constants.purchase_order_status.exported');
                $id = $item->id;
                $eform_purchase_order_item = DB::table('eform_purchase_order_account')
                    ->where('id', $id)
                    ->update(['status_id' => $previous_status]);

                $row['Code'] = $item->purchase_order_code;
                $row['Claimant'] = $item->claimant_name;
                $row['Claim Date'] = $item->claim_date;
                $row['Company'] = $item->company;
                $row['Business Unit'] = $item->business_unit_code;
                $row['Cost Center'] = $item->cost_center;
                $row['Account'] = $item->account;
                $row['Project'] = $item->project;
                $row['Intra-Company'] = $item->intra_company;
                $row['Spare'] = $item->spare;
                $row['PEMS Project'] = $item->pems_project;
                $row['Debit'] = $item->debitted_amount;
                $row['Credit'] = $item->creditted_amount;
                $row['Line Description'] = $item->description;


                fputcsv($file, array(

                    $row['Code'],
                    $row['Claimant'],
                    $row['Claim Date'],
                    $row['Company'],
                    $row['Business Unit'],
                    $row['Cost Center'],
                    $row['Account'],
                    $row['Project'],
                    $row['Intra-Company'],
                    $row['Spare'],
                    $row['PEMS Project'],
                    $row['Debit'],
                    $row['Credit'],
                    $row['Line Description']

                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function charts(Request $request)
    {
        //get the accounts
        $list = PurchaseOrderModel:: select(DB::raw('cost_centre, name_of_claimant, count(id) as total_forms , sum(total_payment) as forms_sum '))
            //->where('status', '<>', 1)
            ->groupBy('sig_of_claimant', 'name_of_claimant', 'cost_centre')
            ->get();

        // dd($list);

        //test
        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();
        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me,
            'list' => $list
        ];
        return view('eforms.hotel.accommodation.chart')->with($params);
        //  dd($request);
    }

    public function sync($id)
    {

        //SYNC ONE
        //get the form
        $form = DB::table('eform_hotel_accomodation')
            ->where('id', $id)
            ->get()->first();


        //get the claimant with the user unit which has the workflow details
//        $user_unit = ConfigWorkFlow::where('user_unit_code',$form->user_unit_code )
//            ->where('user_unit_cc_code',$form->cost_center)
//            ->where('user_unit_bc_code',$form->business_unit_code)->first();

        $user = User::find($form->created_by);
        $user_unit = $user->user_unit;
        try {
            $test = $user_unit->user_unit_cc_code;
        } catch (\Exception $exception) {
//            $user = User::find($form->created_by);
//            $user_unit = $user->user_unit;
            //redirect home
            return Redirect::back()->with('error', 'Purchase Order Voucher did not sync, because of the user-unit problem.');
        }

        //make the update
        $update_eform_purchase_order = DB::table('eform_hotel_accomodation')
            ->where('id', $form->id)
            ->update([

                'cost_center' => $user_unit->user_unit_cc_code,
                'business_unit_code' => $user_unit->user_unit_bc_code,
                'user_unit_code' => $user_unit->user_unit_code,

                'hod_code' => $user_unit->hod_code,
                'hod_unit' => $user_unit->hod_unit,
                'ca_code' => $user_unit->ca_code,
                'ca_unit' => $user_unit->ca_unit,
                'hrm_code' => $user_unit->hrm_code,
                'hrm_unit' => $user_unit->hrm_unit,
                'expenditure_code' => $user_unit->expenditure_code,
                'expenditure_unit' => $user_unit->expenditure_unit,
                'security_code' => $user_unit->security_code,
                'security_unit' => $user_unit->security_unit
            ]);


        // SYNC ALL
//        $eform_purchase_order_all = DB::select("SELECT * FROM eform_purchase_orders  ");

//        foreach ($eform_purchase_order_all as $form) {
//
//            //get the form
//            $eform_purchase_order = DB::table('eform_hotel_accomodation')
//                ->where('id', $form->id)
//                ->get()->first();
//
//            //get the claimant with the user unit which has the workflow details
//            $user_unit = ConfigWorkFlow::where('user_unit_code',$form->user_unit_code )
//                ->where('user_unit_cc_code',$form->cost_center)
//                ->where('user_unit_bc_code',$form->business_unit_code)->first();
//
//            try {
//               $test =  $user_unit->user_unit_cc_code ;
//            }catch (\Exception $exception){
//                $user = User::find($form->created_by);
//             $user_unit = $user->user_unit;
//            }
//
//            //make the update
//            $update_eform_purchase_order = DB::table('eform_hotel_accomodation')
//                ->where('id', $form->id )
//                ->update([
//
//                    'cost_center' => $user_unit->user_unit_cc_code,
//                    'business_unit_code' => $user_unit->user_unit_bc_code,
//                    'user_unit_code' => $user_unit->user_unit_code,
//
//                    'hod_code' => $user_unit->hod_code,
//                    'hod_unit' => $user_unit->hod_unit,
//                    'ca_code' =>  $user_unit->ca_code,
//                    'ca_unit' =>  $user_unit->ca_unit,
//                    'hrm_code' => $user_unit->hrm_code,
//                    'hrm_unit' => $user_unit->hrm_unit,
//                    'expenditure_code' => $user_unit->expenditure_code,
//                    'expenditure_unit' => $user_unit->expenditure_unit,
//                    'security_code' => $user_unit->security_code,
//                    'security_unit' => $user_unit->security_unit
//                ]);
//
//          //  dd($update_eform_purchase_order);
//
//        }
        //  dd($eform_purchase_order_all);


//        $eform_purchase_order = DB::select("SELECT * FROM eform_purchase_orders where id =  {$id} ");
//        $eform_purchase_order = PurchaseOrderModel::hydrate($eform_purchase_order);
//
//        $claimant = User::find($eform_purchase_order[0]->created_by);
//        $user_unit_code = $claimant->user_unit->code;
//        $superior_code = $claimant->position->superior_code;
//        $eform_purchase_order = DB::table('eform_hotel_accomodation')
//            ->where('id', $id)
//            ->update(['code_superior' => $superior_code,
//                'user_unit_code' => $user_unit_code,
//            ]);

        //redirect home
        return Redirect::route('hotel.accommodation.home')->with('message', 'Purchase Order Voucher have been synced successfully');

        dd($claimant->position->superior_code ?? "");
    }

    public function reportsExportUnmarkExported($value)
    {
        //get a list of forms with the above status
        $tasks = PurchaseOrderModel::find($value);
        //umark them
        dd($tasks);
    }

    public function reportsExportUnmarkExportedAll()
    {
        //get a list of forms with the above status
        // $tasks = PurchaseOrderModel::where('status_id', config('constants.purchase_order_status.exported'))->get();
        $exported = config('constants.purchase_order_status.exported');
        $tasks = DB::select("SELECT * FROM eform_purchase_order_account
                        WHERE status_id = {$exported}
                        ORDER BY eform_purchase_order_id ASC ");
        $tasks = PurchaseOrderModel::hydrate($tasks);

        foreach ($tasks as $item) {
//            $item->status_id = config('constants.purchase_order_status.not_exported');
//            $item->save();

            $previous_status = config('constants.purchase_order_status.not_exported');
            $id = $item->id;
            $eform_purchase_order_item = DB::table('eform_purchase_order_account')
                ->where('id', $id)
                ->update(['status_id' => $previous_status]);

        }
        //redirect home
        return Redirect::back()->with('message', 'Purchase Order Exported Accounts have been reversed successfully');
    }

    public function markAccountLinesAsDuplicates($id)
    {
        //$id = 124 ;
        $account_line = DB::select("SELECT * FROM eform_purchase_order_account where id =  {$id} ");
        $account_line = PurchaseOrderModel::hydrate($account_line);
        $size = sizeof($account_line);
        if ($size > 0) {
            $item = $account_line[$size - 1];
            $item->status_id = config('constants.purchase_order_status.void');
            $item->save();
        }
        //redirect home
        return Redirect::back()->with('message', 'Purchase Order Account Line have been Marked as Duplicate successfully');

    }

    public function reverse(Request $request, $id)
    {

        try {
            // get the form using its id
            $eform_purchase_order = DB::select("SELECT * FROM eform_purchase_orders where id =  {$id} ");
            $eform_purchase_order = PurchaseOrderModel::hydrate($eform_purchase_order);

            //get current status id
            $status_model = StatusModel::where('id', $eform_purchase_order[0]->config_status_id)
                ->where('eform_id', config('constants.eforms_id.purchase_order'))->first();
            $current_status = $status_model->id;

            //new status
            $new_status_id = $current_status - 1;
            $status_model = StatusModel::where('id', $new_status_id)
                ->where('eform_id', config('constants.eforms_id.purchase_order'))->first();
            $previous_status = $status_model->id;

            //  $eform_purchase_order = DB::select("UPDATE eform_purchase_orders SET config_status_id = {$previous_status} where id =  {$id} ");
            $eform_purchase_order = DB::table('eform_hotel_accomodation')
                ->where('id', $id)
                ->update(['config_status_id' => $previous_status]);

            $user = Auth::user();
            //save reason
//            $reason = EformApprovalsModel::updateOrCreate(
//                [
//                    'profile' => $user->profile_id,
//                    'title' => $user->profile_id,
//                    'name' => $user->name,
//                    'staff_no' => $user->staff_no,
//                    'reason' => $request->reason,
//                    'action' => $request->approval,
//                    'current_status_id' => $current_status,
//                    'action_status_id' => $previous_status,
//                    'config_eform_id' => config('constants.eforms_id.purchase_order'),
//                    'eform_id' => $eform_purchase_order[0]->id,
//                    'created_by' => $user->id,
//                ]);
            return Redirect::back()->with('message', 'Purchase Order Account Line have been dropped to the previous stage successfully');
        } catch (Exception $exception) {
            return Redirect::back()->with('error', 'Sorry an error happened');
        }
    }

    public function reportsSync()
    {
        try {

//            /*
//             * NEEDED AS A FUNCTION SOMEWHERE IN PETTY CASH CONTROLLER

            //UPDATE ONE  - Update all petty cash accounts with the user unit and work-flow details
            //get a list of all the petty cash account models
            $tasks = DB::select("SELECT * FROM eform_purchase_order_account
                            ORDER BY eform_purchase_order_id ASC ");
            $tasks = PurchaseOrderModel::hydrate($tasks);

            foreach ($tasks as $account){
                //get associated petty cash
                $purchase_order_id = $account->eform_purchase_order_id ;
                $tasks_ht = DB::select("SELECT * FROM eform_hotel_accomodation
                            WHERE id = {$purchase_order_id}  ");
                $tasks_ht = PurchaseOrderModel::hydrate($tasks_ht)->first();

                //update account with the petty cash details
                $eform_purchase_order_account = DB::table('eform_purchase_order_account')
                    ->where('id', $account->id)
                    ->update([
                        'cost_center' => $tasks_ht->cost_center,
                        'business_unit_code' => $tasks_ht->business_unit_code,
                        'user_unit_code' => $tasks_ht->user_unit_code,

                        'claimant_name'=> $tasks_ht->claimant_name,
                        'claimant_staff_no'=> $tasks_ht->claimant_staff_no,
                        'claim_date'=> $tasks_ht->claim_date,
                        'purchase_order_code'=> $tasks_ht->code,

                        'hod_code' => $tasks_ht->hod_code,
                        'hod_unit' => $tasks_ht->hod_unit,
                        'ca_code' => $tasks_ht->ca_code,
                        'ca_unit' => $tasks_ht->ca_unit,
                        'hrm_code' => $tasks_ht->hrm_code,
                        'hrm_unit' => $tasks_ht->hrm_unit,
                        'expenditure_code' => $tasks_ht->expenditure_code,
                        'expenditure_unit' => $tasks_ht->expenditure_unit,
                        'dr_code' => $tasks_ht->dr_code,
                        'dr_unit' => $tasks_ht->dr_unit,
                    ]);
            }
//           */


            return Redirect::back()->with('message', 'Purchase Order Account Line have been dropped to the previous stage successfully');
        } catch (Exception $exception) {
            return Redirect::back()->with('error', 'Sorry an error happened');
        }
    }





    public function search(Request $request)
    {
        $search = strtoupper($request->search);
        if (Auth::user()->type_id == config('constants.user_types.developer')) {
            $list = DB::select("SELECT * FROM eform_hotel_accomodation
              where code LIKE '%{$search}%'
              or claimant_name LIKE '%{$search}%'
              or claimant_staff_no LIKE '%{$search}%'
              or config_status_id LIKE '%{$search}%'
            ");
            $list = PurchaseOrderModel::hydrate($list);
        } else {
            //find the petty cash with that id
            $list = PurchaseOrderModel::
            where('code', 'LIKE', "%{$search}%")
                ->orWhere('claimant_name', 'LIKE', "%{$search}%")
                ->orWhere('claimant_staff_no', 'LIKE', "%{$search}%")
                ->orWhere('config_status_id', 'LIKE', "%{$search}%")
                ->paginate(50);
        }

        //count all
        $totals = TotalsModel::where('eform_id', config('constants.eforms_id.purchase_order'))->get();
        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();
        //pending forms for me before i apply again
        $pending = HomeController::pendingForMe();
        $category = "Search Results";

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me,
            'list' => $list,
            'totals' => $totals,
            'pending' => $pending,
            'category' => $category,
        ];

        //return view
        return view('eforms.hotel-accommodation.list')->with($params);
    }

    public function search1(Request $request)
    {
        if (Auth::user()->type_id == config('constants.user_types.developer')) {

            // dd(222);
            $list = DB::select("SELECT * FROM eform_hotel_accomodation
              where code LIKE '%{$request->search}%'
              or claimant_name LIKE '%{$request->search}%'
              or claimant_staff_no LIKE '%{$request->search}%'
              or claim_date LIKE '%{$request->search}%'
              or AUTHORISED_BY LIKE '%{$request->search}%'
              or AUTHORISED_STAFF_NO LIKE '%{$request->search}%'
              or STATION_MANAGER LIKE '%{$request->search}%'
              or STATION_MANAGER LIKE '%{$request->search}%'
              or ACCOUNTANT LIKE '%{$request->search}%'
              or ACCOUNTANT_STAFF_NO LIKE '%{$request->search}%'
              or EXPENDITURE_OFFICE LIKE '%{$request->search}%'
              or EXPENDITURE_OFFICE_STAFF_NO LIKE '%{$request->search}%'
              or SECURITY_NAME LIKE '%{$request->search}%'
              or SECURITY_STAFF_NO LIKE '%{$request->search}%'
              or total_payment LIKE '%{$request->search}%'
              or config_status_id LIKE '%{$request->search}%'
            ");
            $list = PurchaseOrderModel::hydrate($list)->all();
        } else {
            //find the petty cash with that id
            $list = PurchaseOrderModel::
            where('code', 'LIKE', "%{$request->search}%")
                ->orWhere('claimant_name', 'LIKE', "%{$request->search}%")
                ->orWhere('claimant_staff_no', 'LIKE', "%{$request->search}%")
                ->orWhere('claim_date', 'LIKE', "%{$request->search}%")
                ->orWhere('AUTHORISED_BY', 'LIKE', "%{$request->search}%")
                ->orWhere('AUTHORISED_STAFF_NO', 'LIKE', "%{$request->search}%")
                ->orWhere('STATION_MANAGER', 'LIKE', "%{$request->search}%")
                ->orWhere('STATION_MANAGER_STAFF_NO', 'LIKE', "%{$request->search}%")
                ->orWhere('ACCOUNTANT', 'LIKE', "%{$request->search}%")
                ->orWhere('ACCOUNTANT_STAFF_NO', 'LIKE', "%{$request->search}%")
                ->orWhere('EXPENDITURE_OFFICE', 'LIKE', "%{$request->search}%")
                ->orWhere('EXPENDITURE_OFFICE_STAFF_NO', 'LIKE', "%{$request->search}%")
                ->orWhere('SECURITY_NAME', 'LIKE', "%{$request->search}%")
                ->orWhere('SECURITY_STAFF_NO', 'LIKE', "%{$request->search}%")
                ->orWhere('total_payment', 'LIKE', "%{$request->search}%")
                ->orWhere('config_status_id', 'LIKE', "%{$request->search}%")
                ->get();
        }

        //count all
        $totals = TotalsModel::where('eform_id', config('constants.eforms_id.purchase_order'))->get();
        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();
        //pending forms for me before i apply again
        $pending = HomeController::pendingForMe();
        $category = "Search Results";


       // dd($hotel);

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me,
            'list' => $list,
            'totals' => $totals,
            'pending' => $pending,
            'category' => $category,


        ];

        //return view
        return view('eforms.hotel-accommodation.list')->with($params);
    }


}
