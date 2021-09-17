<?php

return [

    'name' => env('APP_NAME', 'EZESCO'),
    'petty_cash_account_id' => "21",
    'subsistence_account_id' => "82",
    'user_unit_active' => "00",
    'user_unit_not_active' => "01",
    'phris_user_active' => "ACT",
    'phris_user_not_active' => "INA",
    'active' => "0",
    'not_active' => "1",
    'version' => "2.1.0",
    'password_not_changed' => 0 ,
    'password_changed' => 1 ,
    'all' => "all",
    'none' => 0,
    'active_state' => 221,
    'non_active_state' => 222,
    'percentage_reimbursement' => 80,

    'file_type' => [
        'quotation' => "1",
        'receipt' => "0",
        'directors' => 1,
        'general' => 0,
    ],

    'team_email_list' =>  [
        ['email' => 'bchisulo@zesco.co.zm', 'name' => 'Bwalya Chisulo'],
        ['email' => 'nshubart@zesco.co.zm', 'name' => 'Shubart Nyimbili'],
        ['email' => 'pmudenda@zesco.co.zm', 'name' => 'Peter Mudenda'],
        ['email' => 'CCMoonde@zesco.co.zm', 'name' => 'Chimuka Moonde'],
        ['email' => 'CMMusonda@zesco.co.zm', 'name' => 'Musonda'],
        ['email' => 'GSibajene@zesco.co.zm', 'name' => 'Gilbert Sibajene'],
        ['email' => 'jamestembo@zesco.co.zm', 'name' => 'James Tembo'],
        ['email' => 'kChimya@zesco.co.zm', 'name' => 'Kalunga Chimya'],
        ['email' => 'VSingogo@zesco.co.zm', 'name' => 'Vunga Singogo']
    ] ,

    'eforms_id' => [
        'main_dashboard' => "0",
        'petty_cash' => "2",
        'kilometer_allowance' => "21",
        'virement' => "22",
        'datacenter_ca' => "23",
        'subsistence' => "41",
        'trip' => "42",
        'hotel_accommodation' => "62",
        'purchase_order'=> "1"
    ],

    'eforms_name' => [
        'main_dashboard' => "Main Dashboard",
        'petty_cash' => "Petty Cash",
        'kilometer_allowance' => "Kilometer Allowance Claim",
        'virement' => "virement",
        'datacenter_ca'=>"Datacenter Critical Asset Registry",
        'subsistence'=>"Subsistence",
        'trip'=>"Trip Form",
        'hotel_accommodation'=>"Hotel Accommodation Form",
        'purchase_order'=>"Purchase Order Form",
    ],

    'approval' => [
        'approve' => "Approved",
        'reject' => "Rejected",
        'cancelled' => "Cancelled",
        'queried' => "Queried",
        'resolve' => "Resolve"
    ],

    'user_types' => [
        'developer' => "1",
        'mgt' => "2",
        'normal' => "3",
    ],
    'workflow_columns' => [
        'claimant_unit'=> "user_unit_code",
        'claimant_code'=> "id",
        'hod_code'=> "hod_code",
        'hod_unit'=> "hod_unit",
        'ca_code'=> "ca_code",
        'ca_unit'=> "ca_unit",
        'hrm_code'=> "hrm_code",
        'hrm_unit'=> "hrm_unit",
        'expenditure_code'=> "expenditure_code",
        'expenditure_unit'=> "expenditure_unit",
        'security_code'=> "security_code",
        'security_unit'=> "security_unit",
        'audit_code'=> "audit_code",
        'audit_unit'=> "audit_unit",
        'dm_code'=> "dm_code",
        'dm_unit'=> "dm_unit",
        'dr_code'=> "dr_code",
        'dr_unit'=> "dr_unit",
        'bm_code'=> "bm_code",
        'bm_unit'=> "bm_unit",
    ],

    'user_profiles' => [
        'initiator' =>  1,
        'EZESCO_002' => 1,
        'EZESCO_001' => 2,
        'EZESCO_003' => 3,
        'EZESCO_004' => 4,
        'EZESCO_005' => 5,
        'EZESCO_006' => 6,
        'EZESCO_007' => 7,
        'EZESCO_008' => 8,
        'EZESCO_009' => 9,
        'EZESCO_010' => 10,
        'EZESCO_011' => 11,
        'EZESCO_012' => 12,
        'EZESCO_013' => 13,
        'EZESCO_014' => 22,
        'EZESCO_015' => 41,
    ],

    'config_totals' => [
        'directorate' => "directorate",
        'user_unit' => "user_unit",

        'dir_total_closed_count' => "dir_total_closed_count",
        'dir_total_closed_amount' => "dir_total_closed_amount",
        'dir_total_rejected_count' => "dir_total_rejected_count",
        'dir_total_rejected_amount' => "dir_total_rejected_amount",
        'dir_total_pending_count' => "dir_total_pending_count",
        'dir_total_pending_amount' => "dir_total_pending_amount",
        'dir_total_void_count' => "dir_total_void_count",
        'dir_total_void_amount' => "dir_total_void_amount",
        'dir_total_cancelled_count' => "dir_total_cancelled_count",
        'dir_total_cancelled_amount' => "dir_total_cancelled_amount",
        'dir_total_new_count' => "dir_total_new_count",
        'dir_total_new_amount' => "dir_total_new_amount",

        'total_closed_count' => "total_closed_count",
        'total_closed_amount' => "total_closed_amount",
        'total_rejected_count' => "total_rejected_count",
        'total_rejected_amount' => "total_rejected_amount",
        'total_pending_count' => "total_pending_count",
        'total_pending_amount' => "total_pending_amount",
        'total_void_count' => "total_void_count",
        'total_void_amount' => "total_void_amount",
        'total_cancelled_count' => "total_cancelled_count",
        'total_cancelled_amount' => "total_cancelled_amount",
        'total_new_count' => "total_new_count",
        'total_new_amount' => "total_new_amount",

        'daily_closed_count' => "daily_total_closed_count",
        'daily_closed_amount' => "daily_closed_amount",
        'daily_rejected_count' => "daily_rejected_count",
        'daily_rejected_amount' => "daily_rejected_amount",
        'daily_pending_count' => "daily_pending_count",
        'daily_pending_amount' => "daily_pending_amount",
        'daily_void_count' => "daily_void_count",
        'daily_void_amount' => "daily_void_amount",
    ],
    'totals' => [
        'petty_cash_new' => "5",
        'petty_cash_open' => "6",
        'petty_cash_closed' => "7",
        'petty_cash_reject' => "8",

        'kilometer_allowance_new' => "21",
        'kilometer_allowance_open' => "22",
        'kilometer_allowance_closed' => "23",
        'kilometer_allowance_reject' => "24",

        'data_center_ca_new' => "41",
        'data_center_ca_approved' => "42",
        'data_center_ca_rejected' => "43",
        'data_center_ca_all' => "44",

        'subsistence_new' => "61",
        'subsistence_open' => "62",
        'subsistence_closed' => "63",
        'subsistence_reject' => "64",
    ],
    'petty_cash_status' => [
        'new_application' => "21",
        'hod_approved' => "22",
        'hr_approved' => "23",
        'chief_accountant' => "24",
        'funds_disbursement' => "25",
        'funds_acknowledgement' => "26",
        'security_approved' => "27",
        'receipt_approved' => "28",
        'closed' => "235",
        'audited' => "29",
        'reimbursement_box' => "234",
        'await_audit' => "233",
        'audit_box' => "233",
        'rejected' => "30",
        'export_not_ready' => "141",
        'not_exported' => "41",
        'exported' => "42",
        'export_failed' => "43",
        'void' => "101",
        'cancelled' => "161",
        'queried' => "201",
    ],
    'kilometer_allowance_status' => [
        'new_application' => "61",
        'hod_approved' => "62",
        'manager_approved' => "81",
        'hr_approved' => "187",
        'chief_accountant' => "82",
        'funds_disbursement' => "85",
        'funds_acknowledgement' => "86",
        'audit_approved' => "84",
        'security_approved' => "27",
        'receipt_approved' => "87",
        'closed' => "88",
        'rejected' => "83",
        'not_exported' => "41",
        'exported' => "42",
        'export_failed' => "43",
        'void' => "101",
        'cancelled' => "161",
        'queried' => "201",
        'audited' => "29",
    ],
    'data_center_ca_status' => [
        'new_submission' => "89",
        'approved_submission' => "90",
        'reject_submission' => "91",

        'not_exported' => "41",
        'exported' => "42",
        'export_failed' => "43",
    ],
    'subsistence_status' => [
        'new_application' => "121",
        'hod_approved' => "122",
        'station_mgr_approved' => "123",
        'hr_approved' => "124",
        'chief_accountant' => "125",
        'audit_approved' => "126",
        'funds_disbursement' => "127",
        'funds_acknowledgement' => "128",
        'closed' => "129",
        'rejected' => "130",
        'not_exported' => "41",
        'exported' => "42",
        'export_failed' => "43",
        'void' => "101",
        'cancelled' => "161",
        'queried' => "201",
        'audited' => "29",
    ],

    'trip_status' => [
        'new_application' => "21",
        'hod_approved' => "22",
        'hr_approved' => "23",
        'chief_accountant' => "24",
        'funds_disbursement' => "25",
        'funds_acknowledgement' => "26",
        'security_approved' => "27",
        'receipt_approved' => "28",
        'closed' => "28",
        'rejected' => "30",
        'not_exported' => "41",
        'exported' => "42",
        'export_failed' => "43",
        'void' => "101",
    ],

    'hotel_accommodation_status' => [
        'new_application' => "181",
        'hod_approved' => "182",
        'chief_accountant_approved' => "183",
        'director_approved' => "184",
        'closed' => "185",
        'rejected' => "186",
        'audited' => "29",
        'queried' => "201",
        'not_exported' => "41",
        'exported' => "42",
        'export_failed' => "43",
        'void' => "101",
        'cancelled' => "161",

    ],

    'purchase_order_status' => [
        'new_application' => "223",
        'checker_approved' => "225",
        'hod_approved' => "224",
        'requester_approved' => "183",



        'closed' => "185",
        'rejected' => "186",
        'audited' => "29",
        'queried' => "201",
        'not_exported' => "41",
        'exported' => "42",
        'export_failed' => "43",
        'void' => "101",
        'cancelled' => "161",

    ],

];
