<?php

namespace App\Http\Controllers\EForms\Subsistence;

use App\Http\Controllers\Controller;
use App\Models\EForms\Subsistence\SubsistenceModel;
use App\Models\EForms\Trip\Destinations;
use App\Models\EForms\Trip\Invitation;
use App\Models\Main\ProfileAssigmentModel;
use App\Models\Main\ProfileDelegatedModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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
        session(['eform_id' => config('constants.eforms_id.subsistence')]);
        session(['eform_code' => config('constants.eforms_name.subsistence')]);

    }


    /**
     * Show the main application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        //count new forms
        $new_forms = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.new_application'))
            ->count();
        //count pending forms
        $pending_forms = SubsistenceModel::
        where('config_status_id', '!=', config('constants.subsistence_status.new_application'))
            ->orWhere('config_status_id',  '!=', config('constants.subsistence_status.closed'))
            ->orWhere('config_status_id',  '!=', config('constants.subsistence_status.void'))
            ->orWhere('config_status_id',  '!=', config('constants.subsistence_status.cancelled'))
            ->orWhere('config_status_id',  '!=', config('constants.subsistence_status.queried'))
            ->orWhere('config_status_id',  '!=', config('constants.subsistence_status.rejected'))
            ->count();
        //count closed forms
        $closed_forms = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.closed'))
            ->count();
        //count rejected forms
        $rejected_forms = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.rejected'))
            ->count();

        //add to totals
        $totals['new_forms'] = $new_forms;
        $totals['pending_forms'] = $pending_forms;
        $totals['closed_forms'] = $closed_forms;
        $totals['rejected_forms'] = $rejected_forms;

        //list all that needs me
        //   $get_profile = self::getMyProfile();

        //list all that needs me
        $list = self::needsMeList();
        //count all that needs me
        $totals_needs_me = $list->count();
        //pending forms for me before i apply again
        $pending = self::pendingForMe();

        // dd($list);

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me,
            'list' => $list,
            'totals' => $totals,
            'pending' => $pending,
        ];
        //return view
        return view('eforms.subsistence.dashboard')->with($params);
    }


    public static function needsMeCount(){
        $list = self::needsMeList();
        return $list->count() ;
    }


    public static function needsMeList()
    {
        $user = Auth::user();

        $list_inv = Invitation::select('subsistence_id')
            ->where('man_no', $user->staff_no)
            ->where('status_id', config('constants.trip_status.accepted') )
            ->get();
        $list_inv = $list_inv->pluck('subsistence_id')->toArray() ;


        //for the SYSTEM ADMIN
        if ($user->profile_id == config('constants.user_profiles.EZESCO_001')) {
            $list = SubsistenceModel::whereDate('updated_at', \Carbon::today())
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);
            dd(1);

        } //for the REQUESTER
        elseif ($user->profile_id == config('constants.user_profiles.EZESCO_002')) {
            $list = SubsistenceModel::where('config_status_id', '=', config('constants.subsistence_status.new_application'))
                ->orWhere('config_status_id', '=', config('constants.subsistence_status.funds_disbursement'))
                ->orWhere('config_status_id', '=', config('constants.trip_status.accepted'))
//                ->orWhere('config_status_id', '=', config('constants.trip_status.hod_approved_trip'))
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);
            //   dd(2) ;hod_approved_trip
        } //for the HOD
        elseif ($user->profile_id == config('constants.user_profiles.EZESCO_004')) {

            $list = SubsistenceModel::where('config_status_id', config('constants.trip_status.accepted'))
                ->orWhere('config_status_id', config('constants.subsistence_status.destination_approval'))
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);


        } //for the HR
        elseif ($user->profile_id == config('constants.user_profiles.EZESCO_009')) {
            $list = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.hod_approved'))
                ->orWhere('config_status_id', '=', config('constants.trip_status.hod_approved_trip'))
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);

        } //for the SNR MANAGER
        elseif ($user->profile_id == config('constants.user_profiles.EZESCO_015')) {
            $list = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.hr_approved'))
                ->orWhere('config_status_id', '=', config('constants.trip_status.hr_approved_trip'))
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);

        } //for the CHIEF ACCOUNTANT
        elseif ($user->profile_id == config('constants.user_profiles.EZESCO_007')) {
            $list = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.station_mgr_approved'))
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);
            //  dd(5) ;
        }
        //for the EXPENDITURE OFFICE
        elseif ($user->profile_id == config('constants.user_profiles.EZESCO_014')) {
            $list = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.pre_audited'))
                ->orWhere('config_status_id', config('constants.subsistence_status.queried'))
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);

        } //for the APPROVALS
        elseif ($user->profile_id == config('constants.user_profiles.EZESCO_013')) {
            $list = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.funds_acknowledgement'))
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);
        }//for the AUDIT
        elseif ($user->profile_id == config('constants.user_profiles.EZESCO_011')) {
            $list = SubsistenceModel::where('config_status_id', config('constants.subsistence_status.chief_accountant'))
                ->where('config_status_id', config('constants.subsistence_status.chief_accountant'))
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);
        }
        else {
            $list = SubsistenceModel::where('config_status_id', 0)
                ->orWhereIn('id', $list_inv)
                ->orderBy('code')->paginate(50);
            //  dd(8) ;
        }
        return $list;
    }


    public static function pendingForMe()
    {
        $user = Auth::user();
        $pending = 0;
        //
        $list_inv = Invitation::select('subsistence_id')
            ->where('man_no', $user->staff_no)
            ->where('status_id', config('constants.trip_status.pending') )
            ->get();
        $list_inv = $list_inv->pluck('subsistence_id')->toArray() ;

        //for the REQUESTER
        if ($user->profile_id == config('constants.user_profiles.EZESCO_002')) {
            //count pending applications
            $pending = SubsistenceModel::where('config_status_id', '=', config('constants.subsistence_status.new_application'))
                ->orWhere('config_status_id', '=', config('constants.trip_status.accepted'))
//                ->orWhere('config_status_id', '=', config('constants.trip_status.hod_approved_trip'))
                ->orWhereIn('id', $list_inv)
                ->count();
        }

        return $pending;
    }


}
