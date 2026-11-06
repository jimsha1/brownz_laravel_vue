<?php

namespace Database\Seeders;

use App\Models\AclPermission;
use Illuminate\Database\Seeder;

class PermissionMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // "authorization"
        $this->createPermission(1, 'authorization', 'create_user', 'Create user', 1);
        $this->createPermission(2, 'authorization', 'edit_user', 'Edit user', 2);
        $this->createPermission(3, 'authorization', 'delete_user', 'Delete user', 3);
        $this->createPermission(4, 'authorization', 'create_role', 'Create role', 4);
        $this->createPermission(5, 'authorization', 'delete_role', 'Delete role', 5);

        // "master_data
        $this->createPermission(6, 'master_data', 'outlet', 'Outlet', 6);
        $this->createPermission(7, 'master_data', 'global_taxes', 'Global Taxes', 7);
        $this->createPermission(8, 'master_data', 'service_categories', 'Service Categories', 8);
        $this->createPermission(9, 'master_data', 'services', 'Services', 9);
        $this->createPermission(10, 'master_data', 'shift', 'Shift List', 10);

        // salon
        $this->createPermission(11, 'salon', 'beautician', 'Beautician', 11);
        $this->createPermission(12, 'salon', 'beatician_list', 'Beatician List', 12);
        $this->createPermission(13, 'salon', 'clients', 'Clients', 13);
        $this->createPermission(14, 'salon', 'bookings', 'Bookings', 14);
        $this->createPermission(15, 'salon', 'leave_mgt', 'Leave Management', 15);

        // x/z out
        $this->createPermission(16, 'x/z out', 'register_open_close', 'Register Open/Close', 16);
        $this->createPermission(17, 'x/z out', 'former_zout', 'Former Z-Out', 17);
        $this->createPermission(18, 'x/z out', 'former_xout', 'X-Out', 18);
        $this->createPermission(31, 'x/z out', 'force_close', 'Force Close', 31);
        $this->createPermission(95, 'x/z out', 'edit_closed_count', 'Edit Closed Count', 95);

        // settings
        $this->createPermission(19, 'settings', 'denomination', 'Denomination', 19);

        // pos_booking
        $this->createPermission(20, 'pos_booking', 'create_booking', 'Create booking', 20);
        $this->createPermission(21, 'pos_booking', 'void_cash_transaction', 'Void a cash transaction', 21);
        $this->createPermission(22, 'pos_booking', 'void_card_transaction', 'Void a card transaction', 22);
        $this->createPermission(23, 'pos_booking', 'cancel_booking', 'Cancel booking', 23);
        $this->createPermission(24, 'pos_booking', 'soft_delete_booking', 'Move booking to trash', 24);
        $this->createPermission(25, 'pos_booking', 'force_delete_booking', 'Delete booking permanently', 25);
        $this->createPermission(94, 'pos_booking', 'void_completed_payment', 'Void or correct payment after booking completed', 94);

        // "appointment"
        $this->createPermission(26, 'appointment', 'view_own_appointments', 'View own appointments', 26);
        $this->createPermission(27, 'appointment', 'view_appointment_of_others', 'View appointment of others', 27);
        $this->createPermission(28, 'appointment', 'view_own_calender', 'View own calender', 28);
        $this->createPermission(29, 'appointment', 'view_beautician_of_others', 'View beautician of others', 29);
        $this->createPermission(30, 'settings', 'bussines_profile', 'View Business Profile', 30);

        // "Reports"
        $this->createPermission(32, 'report', 'sales_report', 'Sales Report', 32);
        $this->createPermission(33, 'report', 'client_report', 'Client Report', 33);
        $this->createPermission(64, 'report', 'vendor_report', 'Vendor Report', 64);
        $this->createPermission(34, 'report', 'tax_report', 'Tax Report', 34);
        $this->createPermission(35, 'report', 'service_report', 'Service Report', 35);
        $this->createPermission(36, 'report', 'beautician_report', 'Beautician Report', 36);
        $this->createPermission(89, 'report', 'consultation_report', 'Consultation Report', 89);
        $this->createPermission(96, 'report', 'edit_log_report', 'Edit Log Report', 96);
        $this->createPermission(97, 'report', 'service_used_product_report', 'Service Used Product Report', 97);
        $this->createPermission(98, 'report', 'whatsapp_invoice_report', 'Whatsapp Invoice Report', 98);
        $this->createPermission(99, 'report', 'daily_sales_report', 'Daily Sales Report', 99);

        // "Payrolls"
        $this->createPermission(37, 'payroll', 'emp_create', 'Create Employee', 37);
        $this->createPermission(38, 'payroll', 'emp_edit', 'Edit Employee', 38);
        $this->createPermission(39, 'payroll', 'salary_create', 'Create Employee Salary', 39);
        $this->createPermission(40, 'payroll', 'salary_edit', 'Edit Employee Salary', 40);
        $this->createPermission(41, 'payroll', 'incentive_term_create', 'Create Incentive Terms', 41);
        $this->createPermission(42, 'payroll', 'incentive_term_edit', 'Edit Incentive Terms', 42);
        $this->createPermission(43, 'payroll', 'incentive_create', 'Create Incentive', 43);
        $this->createPermission(44, 'payroll', 'incentive_edit', 'Edit Incentive', 44);
        $this->createPermission(45, 'payroll', 'incentive_view', 'View Incentive', 45);
        $this->createPermission(46, 'payroll', 'commission_create', 'Create Commission', 46);
        $this->createPermission(47, 'payroll', 'commission_edit', 'Edit commission', 47);
        $this->createPermission(48, 'payroll', 'commission_view', 'View Commission', 48);
        $this->createPermission(49, 'payroll', 'attendance_view', 'View Attendance', 49);
        $this->createPermission(50, 'payroll', 'adv_salary_create', 'Create Adv Salary', 50);
        $this->createPermission(51, 'payroll', 'adv_salary_edit', 'Edit Adv Salary', 51);
        $this->createPermission(52, 'payroll', 'payroll_create', 'Create Payroll', 52);
        $this->createPermission(53, 'payroll', 'payment_create', 'Create Payment', 53);
        $this->createPermission(54, 'payroll', 'payment_edit', 'Edit Payment', 54);

        // Payroll Report
        $this->createPermission(55, 'payroll_report', 'salary_report', 'Salary Report', 55);
        $this->createPermission(56, 'payroll_report', 'commission_report', 'Commission Report', 56);
        $this->createPermission(57, 'payroll_report', 'attendance_report', 'Attendance Report', 57);
        $this->createPermission(58, 'payroll_report', 'payroll_report', 'Payroll Report', 58);
        $this->createPermission(59, 'payroll_report', 'incentive_report', 'Incentive Report', 59);
        $this->createPermission(90, 'payroll_report', 'working_hours_report', 'Working Hours Report', 90);

        $this->createPermission(60, 'salon', 'leave_approve', 'Leave Approve', 60);
        $this->createPermission(61, 'appointment', 'split_edit', 'Edit Split', 61);

        // Stock
        $this->createPermission(61, 'stock', 'brands', 'Brands', 61);
        $this->createPermission(62, 'stock', 'item_groupe', 'Item Group', 62);
        $this->createPermission(63, 'stock', 'items', 'Items', 63);
        $this->createPermission(91, 'stock', 'purchase', 'Purchase', 91);
        $this->createPermission(92, 'stock', 'purchase_edit', 'Edit Purchase', 92);
        $this->createPermission(93, 'stock', 'purchase_return', 'Purchase Return', 93);

        // Accounts
        $this->createPermission(65, 'accounts', 'financial_year_view', 'View Financial Year', 65);
        $this->createPermission(66, 'accounts', 'financial_year_initialize', 'Initialize Financial Year', 66);
        $this->createPermission(67, 'accounts', 'financial_year_close', 'Close Financial Year', 67);
        $this->createPermission(68, 'accounts', 'financial_year_open', 'Open Financial Year', 68);

        // Ledger
        $this->createPermission(69, 'accounts', 'ledger_view', 'View Ledger', 69);
        $this->createPermission(70, 'accounts', 'ledger_create', 'Create Ledger', 70);
        $this->createPermission(71, 'accounts', 'ledger_edit', 'Edit Ledger', 71);

        // Voucher
        $this->createPermission(72, 'accounts', 'voucher_view', 'View Voucher', 72);
        $this->createPermission(73, 'accounts', 'voucher_create', 'Create Voucher', 73);
        $this->createPermission(74, 'accounts', 'voucher_edit', 'Edit Voucher', 74);

        // Opening Balance
        $this->createPermission(75, 'accounts', 'opening_balance_view', 'View Opening Balance', 75);
        $this->createPermission(76, 'accounts', 'opening_balance_create', 'Create Opening Balance', 76);
        $this->createPermission(77, 'accounts', 'opening_balance_edit', 'Edit Opening Balance', 77);

        // Expense
        $this->createPermission(78, 'accounts', 'expense_view', 'View Expense', 78);
        $this->createPermission(79, 'accounts', 'expense_create', 'Create Expense', 79);
        $this->createPermission(80, 'accounts', 'expense_edit', 'Edit Expense', 80);


        // Trial Balance
        $this->createPermission(81, 'accounts', 'trial_balance_view', 'View Trial Balance', 81);

        // Profit & Loss
        $this->createPermission(82, 'accounts', 'profit_loss_view', 'View Profit & Loss', 82);

        // Balance Sheet
        $this->createPermission(83, 'accounts', 'balance_sheet_view', 'View Balance Sheet', 83);

        // Day Book
        $this->createPermission(84,'accounts', 'day_book_view', 'View Day Book',84);

        // Vendor
        $this->createPermission(85,'accounts', 'vendor_view', 'View Vendor',85);
        $this->createPermission(86,'accounts', 'vendor_create', 'Create Vendor',86);
        $this->createPermission(87,'accounts', 'vendor_edit', 'Edit Vendor',87);
        $this->createPermission(88,'accounts', 'vendor_delete', 'Delete Vendor',88);

    }

    private function createPermission($id,$group, $name, $description, $display_order)
    {
        $permission = AclPermission::updateOrCreate([
            'id' => $id,
        ],[
            'name' => $name,
            'description' => $description,
            'group' => $group,
            'display_order' => $display_order,
        ]);

    }
}
