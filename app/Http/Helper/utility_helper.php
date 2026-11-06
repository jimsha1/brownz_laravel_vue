<?php

use App\Models\Account;
use Illuminate\Support\Facades\DB;

function getTaxslab(){
    $tax_slab = DB::table('global_taxes')->get()->first();
    if($tax_slab != null){
        return $tax_slab->tax;
    }else{
        return 0;
    }
}

function getDefaultShift(){
    $shift = DB::table('shifts')->where('title', 'SHIFT:24 Hour')->first();
    if($shift != null){
        return $shift->shift_id;
    }else{
        return 0;
    }
}

function getAccount($name){
    $account = Account::select('account_id')->where('name', $name)->first();
    if($account != null){
        return $account->account_id;
    }else{
        return 0;
    }    
}

function getCurrentFinancialYearLabel(): string
{
    $month = (int) date('m');
    $year = (int) date('Y');

    if ($month < 4) {
        return ($year - 1).'-'.$year;
    }

    return $year.'-'.($year + 1);
}

function reportingTransactionSqlConstraint(): string
{
    return 'COALESCE((SELECT is_audit_only FROM transaction_groups WHERE transaction_groups.id = transactions.transaction_group_id), 0) = 0';
}