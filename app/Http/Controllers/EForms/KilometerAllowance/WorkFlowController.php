<?php

namespace App\Http\Controllers\EForms\KilometerAllowance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Main\ActivityLogsController;
use App\Http\Controllers\Main\DivisionalUserUnitController;
use App\Models\Main\ConfigWorkFlow;
use App\Models\Main\DepartmentModel;
use App\Models\Main\UserUnitModel;
use App\Models\Main\UserUnitSpmsSyncModel;
use App\Models\PhrisUserDetailsModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class WorkFlowController extends Controller
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
        session(['eform_id' => config('constants.eforms_id.kilometer_allowance')]);
        session(['eform_code' => config('constants.eforms_name.kilometer_allowance')]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get all the categories
        $list = ConfigWorkFlow::orderBy('user_unit_code')
            ->where('user_unit_status', config('constants.user_unit_active') )->get();

        $users = User::orderBy('name')->get();

        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me ,
            'users' => $users,
            'list' => $list,
        ];

        return view('eforms.kilometer-allowance.workflow')->with($params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentRequest $request)
    {

        $user = Auth::user();
        $model = DepartmentModel::firstOrCreate(
            [
                'name' => $request->name,
                'code' => $request->code,
            ],
            [
                'name' => $request->name,
                'code' => $request->code,
                'business_unit_code' => $request->business_unit_code,
                'cost_center_code' => $request->cost_center_code,
                'code_unit_superior' => $request->code_unit_superior,
                'created_by' => $user->id,
            ]);

        //log the activity
        ActivityLogsController::store($request, "Creating of User Unit", "update", " user unit created", json_encode($model));
        return Redirect::back()->with('message', 'Details for ' . $model->name . ' have been Created successfully');


    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $form_id)
    {
        $workflow = ConfigWorkFlow::find($id);
        $users = User::orderBy('name')->get();

        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me ,
            'users' => $users,
            'form_id' => $form_id,
            'workflow' => $workflow,
        ];

        return view('eforms.kilometer-allowance.showworkflow')->with($params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $form_id)
    {
        // [1] update the workflow
        $model = ConfigWorkFlow::find($request->workflow_id);
        $model->hod_code = $request->hod_code ?? "" ;
        $model->hod_unit = $request->hod_unit ??  "" ;
        $model->ca_code = $request->ca_code ??  "" ;
        $model->ca_unit = $request->ca_unit ??  "" ;
        $model->hrm_code = $request->hrm_code ??  "" ;
        $model->hrm_unit = $request->hrm_unit ??  "" ;
        $model->expenditure_code = $request->expenditure_code ??  "" ;
        $model->expenditure_unit = $request->expenditure_unit ??  "" ;
        $model->security_code = $request->security_code ??  "" ;
        $model->security_unit = $request->security_unit ??  "" ;
        $model->audit_code = $request->audit_code ??  "" ;
        $model->audit_unit = $request->audit_unit ??  "" ;
        $model->save();

        //[2] update the kilometer allowance form
        if($form_id != config('constants.all')){
            $update_eform_kilometer_allowance = DB::table('eform_kilometer_allowance')
                ->where('id', $form_id)
                ->update([
                    'cost_center' => $model->user_unit_cc_code,
                    'business_unit_code' => $model->user_unit_bc_code,
                    'user_unit_code' => $model->user_unit_code,

                    'hod_code' => $model->hod_code,
                    'hod_unit' => $model->hod_unit,
                    'ca_code' => $model->ca_code,
                    'ca_unit' => $model->ca_unit,
                    'hrm_code' => $model->hrm_code,
                    'hrm_unit' => $model->hrm_unit,
                    'expenditure_code' => $model->expenditure_code,
                    'expenditure_unit' => $model->expenditure_unit,
                    'security_code' => $model->security_code,
                    'security_unit' => $model->security_unit,
                    'audit_code' => $model->audit_code,
                    'audit_unit' => $model->audit_unit
                ]);

            //[3] update the kilometer allowance account lines
            $update_eform_kilometer_allowance_account = DB::table('eform_kilometer_allowance_account')
                ->where('eform_kilometer_allowance_id', $form_id)
                ->update([
                    'cost_center' => $model->user_unit_cc_code,
                    'business_unit_code' => $model->user_unit_bc_code,
                    'user_unit_code' => $model->user_unit_code,

                    'hod_code' => $model->hod_code,
                    'hod_unit' => $model->hod_unit,
                    'ca_code' => $model->ca_code,
                    'ca_unit' => $model->ca_unit,
                    'hrm_code' => $model->hrm_code,
                    'hrm_unit' => $model->hrm_unit,
                    'expenditure_code' => $model->expenditure_code,
                    'expenditure_unit' => $model->expenditure_unit,
                    'security_code' => $model->security_code,
                    'security_unit' => $model->security_unit,
                    'audit_code' => $model->audit_code,
                    'audit_unit' => $model->audit_unit
                ]);
        }

        //log the activity
        //  ActivityLogsController::store($request, "Updating of kilometer allowance User Unit Workflow", "update", "kilometer allowance unit user workflow updated", $model->id);
        return Redirect::route('kilometer.allowance.home')->with('message', 'Work Flow for ' . $model->name . ' have been Updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
//        $model = DepartmentModel::find($id);
//        DepartmentModel::destroy($id);
//        //log the activity
//        ActivityLogsController::store($request, "Deleting of User Unit ", "delete", " user unit deleted", json_encode($model));
//        return Redirect::back()->with('message', 'Details for ' . $model->name . ' have been Deleted successfully');

    }

    public function search(Request  $request){
        //capitalise
        $user_unit = strtoupper($request->user_unit_code);
        $form_id = 0;
        $workflow = ConfigWorkFlow::where('user_unit_code', $user_unit)
             ->where('user_unit_status', config('constants.user_unit_active') );



        if($workflow->exists()){
            $users = User::orderBy('name')->get();
        }else{
            return Redirect::back()->with('error', 'Details for ' . $user_unit. ' could not be found');
        }

        //count all that needs me
        $totals_needs_me = HomeController::needsMeCount();

        //data to send to the view
        $params = [
            'totals_needs_me' => $totals_needs_me ,
            'users' => $users,
            'form_id' => $form_id,
            'workflow' => $workflow->first(),
        ];

        return view('eforms.kilometer-allowance.showworkflow')->with($params);
    }


}
