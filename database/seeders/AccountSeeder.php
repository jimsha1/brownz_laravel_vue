<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Upserts by unique account `code` only — never by account_id.
     * Production DBs already have auto-incremented IDs / user-created ledgers,
     * so hard-coded account_id values cause primary-key conflicts.
     * Application code resolves system accounts via getAccount(name) / code.
     */
    public function run(): void
    {
        // Assets
        $this->createAccount('1000', 'Cash', 'asset', 'Current Asset');
        $this->createAccount('1010', 'Bank', 'asset', 'Current Asset');
        $this->createAccount('1020', 'Accounts Receivable', 'asset', 'Current Asset');
        $this->createAccount('1030', 'Opening Stock Adjustment', 'asset', 'Current Asset');
        $this->createAccount('1035', 'Inventory', 'asset', 'Current Asset');
        $this->createAccount('1040', 'Input Tax (GST/VAT Credit)', 'asset', 'Current Asset');
        $this->createAccount('1050', 'Prepaid Expenses', 'asset', 'Current Asset');
        $this->createAccount('1060', 'Fixed Assets', 'asset', 'Non-Current Asset');
        $this->createAccount('1061', 'Accumulated Depreciation', 'asset', 'Non-Current Asset');
        $this->createAccount('1070', 'Salary Advances Receivable', 'asset', 'Current Asset');

        // Liabilities
        $this->createAccount('2000', 'Accounts Payable', 'liability', 'Current Liability');
        $this->createAccount('2010', 'Output Tax Payable (GST/VAT)', 'liability', 'Current Liability');
        $this->createAccount('2020', 'Salaries Payable', 'liability', 'Current Liability');
        $this->createAccount('2030', 'Accrued Expenses', 'liability', 'Current Liability');
        $this->createAccount('2040', 'Loans Payable', 'liability', 'Long-Term Liability');
        $this->createAccount('2050', 'TDS Payable', 'liability', 'Current Liability');
        $this->createAccount('2060', 'Professional Tax Payable', 'liability', 'Current Liability');
        $this->createAccount('2070', 'Commission Payable', 'liability', 'Current Liability');
        $this->createAccount('2080', 'Incentive Payable', 'liability', 'Current Liability');

        // Equity
        $this->createAccount('3000', 'Owner’s Capital', 'equity', 'Capital');
        $this->createAccount('3010', 'Retained Earnings', 'equity', 'Retained Earnings');
        $this->createAccount('3020', 'Drawings', 'equity', 'Contra Equity');
        $this->createAccount('3030', 'Opening Balance Adjustment', 'equity', 'Opening');
        $this->createAccount('3040', 'Profit & Loss Closing account', 'equity', 'Adjustment');

        // Revenue
        $this->createAccount('4000', 'Sales Revenue', 'revenue', 'Operating Income');
        $this->createAccount('4010', 'Discount Received', 'revenue', 'Indirect Income');
        $this->createAccount('4020', 'Other Income', 'revenue', 'Non-Operating');
        $this->createAccount('4030', 'Round Off Gain', 'revenue', 'Adjustment');
        $this->createAccount('4040', 'Inventory Adjustment Gain', 'revenue', 'Other Income');
        $this->createAccount('4041', 'Service Revenue', 'revenue', 'Operating Income');

        // Operating Expenses
        $this->createAccount('6000', 'Salaries & Wages', 'expense', 'Indirect Expense');
        $this->createAccount('6010', 'Rent Expense', 'expense', 'Indirect Expense');
        $this->createAccount('6020', 'Utilities', 'expense', 'Indirect Expense');
        $this->createAccount('6030', 'Office Supplies', 'expense', 'Indirect Expense');
        $this->createAccount('6040', 'Depreciation Expense', 'expense', 'Indirect Expense');
        $this->createAccount('6050', 'Freight Inwards', 'expense', 'Direct Expense');
        $this->createAccount('6060', 'Repairs & Maintenance', 'expense', 'Indirect Expense');
        $this->createAccount('6070', 'Advertising & Marketing', 'expense', 'Indirect Expense');
        $this->createAccount('6080', 'Bank Charges', 'expense', 'Indirect Expense');
        $this->createAccount('6090', 'Round Off Loss', 'expense', 'Adjustment');
        $this->createAccount('6092', 'Sales Discounts', 'expense', 'Indirect Expense');
        $this->createAccount('6094', 'Cost of Goods Sold', 'expense', 'Direct Expense');
        $this->createAccount('6096', 'Employer Payroll Tax Expense', 'expense', 'Indirect Expense');
        $this->createAccount('6098', 'Inventory Adjustment Loss', 'expense', 'Indirect Expense');
        $this->createAccount('6099', 'Purchase Account', 'expense', 'Direct Expense');
        $this->createAccount('6100', 'Commission Expense', 'expense', 'Indirect Expense');
        $this->createAccount('6110', 'Incentive Expense', 'expense', 'Indirect Expense');
    }

    private function createAccount(string $code, string $name, string $type, string $category, float $balance = 0): void
    {
        $account = Account::firstOrNew(['code' => $code]);
        $isNew = ! $account->exists;

        $account->name = $name;
        $account->type = strtolower($type);
        $account->category = $category;
        $account->is_system_defined = 1;
        $account->created_by = $account->created_by ?: 1;

        // Never wipe live ledger balances when re-seeding system accounts.
        if ($isNew) {
            $account->balance = $balance;
        }

        $account->save();
    }
}
