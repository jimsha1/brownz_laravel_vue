@php
$page_name = isset($page_name) ? $page_name : '';
$category_name = isset($category_name) ? $category_name : '';
@endphp

<ul class="list-unstyled scroll-nav-side-bar">
    <li><a class="nav-link-d {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fa fa-tachometer" aria-hidden="true"></i>
            <span class="nav-link-text">Dashboard</span></a></li>
    @canany(['bussines_profile'])
    <li><a class="nav-link-d {{ Request::routeIs('admin.business.profile') ? 'active' : '' }}"
            href="{{ route('admin.business.profile') }}"><i class="fa fa-id-card" aria-hidden="true"></i> <span
                class="nav-link-text">Business Profile</span></a></li>
    @endcanany
    @canany(['create_role', 'create_user'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('tab.acl.role.list', 'tab.acl.user.list') ? 'active' : '' }} "
            role="button">
            <i class="fa fa-user-secret" aria-hidden="true"></i>
            <span class="nav-link-text">ACL</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('tab.acl.role.list', 'tab.acl.user.list') ? 'opening' : '' }} {{ $category_name == 'acl' ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can('create_role')
                <a class="{{ Request::routeIs('tab.acl.role.list') ? 'active' : '' }} {{ $page_name == 'role_details' ? 'active' : '' }}"
                    href="{{ route('tab.acl.role.list') }}"><i class="fa fa-server" aria-hidden="true"></i>
                    <p>Roles</p>
                </a>
                @endcan
                @can('create_user')
                <a class="{{ Request::routeIs('tab.acl.user.list') ? 'active' : '' }} {{ $page_name == 'user_details' ? 'active' : '' }}"
                    href="{{ route('tab.acl.user.list') }}"><i class="fa fa-user" aria-hidden="true"></i>
                    <p>Users</p>
                </a>
                @endcan
            </div>

        </div>
    </li>
    @endcanany
    @canany(['outlet', 'global_taxes', 'service_categories', 'services', 'service_type_mgt', 'uom', 'shift'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('tab.master.outlet.list', 'tab.master.global.tax.list', 'tab.master.service.category.list', 'tab.master.service.list', 'tab.master.shift.list', 'tab.admin.vendor.list') ? 'active' : '' }}"
            role="button">
            <i class="fa fa-database" aria-hidden="true"></i>
            <span class="nav-link-text">Master Data</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('tab.master.outlet.list', 'tab.master.global.tax.list', 'tab.master.service.category.list', 'tab.master.service.list', 'tab.master.shift.list', 'tab.admin.vendor.list') ? 'opening' : '' }} {{ $category_name == 'master' ? 'opening' : '' }}">
            <div class="sub-nav-links">
                {{-- @can('outlet')
                        <a
                            class="{{ Request::routeIs('tab.master.outlet.list') ? 'active' : '' }} {{ $page_name == 'outlet_edit' ? 'active' : '' }}"href="{{ route('tab.master.outlet.list') }}"><i
                    class="fa fa-building" aria-hidden="true"></i>
                <p>Branch</p>
                </a>
                @endcan --}}
                @can('global_taxes')
                <a
                    class="{{ Request::routeIs('tab.master.global.tax.list') ? 'active' : '' }} {{ $page_name == 'global_tax_edit' ? 'active' : '' }} " href="{{ route('tab.master.global.tax.list') }}"><i
                        class="fa fa-globe" aria-hidden="true"></i>
                    <p>Global Taxes</p>
                </a>
                @endcan
                @can('service_categories')
                <a
                    class="{{ Request::routeIs('tab.master.service.category.list') ? 'active' : '' }} {{ $page_name == 'service_category_edit' ? 'active' : '' }}" href="{{ route('tab.master.service.category.list') }}"><i
                        class="fa fa-wrench" aria-hidden="true"></i>
                    <p>Service Categories</p>
                </a>
                @endcan
                @can('services')
                <a
                    class="{{ Request::routeIs('tab.master.service.list') ? 'active' : '' }} {{ $page_name == 'service_details' ? 'active' : '' }}" href="{{ route('tab.master.service.list') }}"><i
                        class="fa fa-wrench" aria-hidden="true"></i>
                    <p>Service</p>
                </a>
                @endcan
                @can('shift')
                <a
                    class="{{ Request::routeIs('tab.master.shift.list') ? 'active' : '' }} {{ $page_name == 'shift_details' ? 'active' : '' }}" href="{{ route('tab.master.shift.list') }}"><i
                        class="fa fa-random" aria-hidden="true"></i>
                    <p>Shift</p>
                </a>
                @endcan
                {{-- @can('shift') --}}
                {{-- <a
                        class="{{ Request::routeIs('tab.admin.vendor.list') ? 'active' : '' }} {{ $page_name == 'vendor_details' ? 'active' : '' }}"href="{{ route('tab.admin.vendor.list') }}"><i
                    class="fa fa-random" aria-hidden="true"></i>
                <p>Vendors</p>
                </a> --}}
                {{-- @endcan --}}
            </div>
        </div>
    </li>
    @endcanany
    @canany(['former_zout', 'register_open_close', 'former_xout'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('float_amt', 're.print.bill', 'x.out') ? 'active' : '' }}"
            role="button">
            <i class="fa fa-openid" aria-hidden="true"></i>
            <span class="nav-link-text">X/Z Out</span>
        </a>
        <div class="dropdown-content {{ Request::routeIs('float_amt', 're.print.bill', 'x.out') ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can(['register_open_close'])
                <a class="{{ Request::routeIs('float_amt') ? 'active' : '' }}" href="{{ route('float_amt') }}"><i
                        class="fa fa-minus-square-o" aria-hidden="true"></i>
                    <p>Register Open/Close</p>
                </a>
                @endcan
                @can(['former_zout'])
                <a class="{{ Request::routeIs('re.print.bill') ? 'active' : '' }}"
                    href="{{ route('re.print.bill') }}"><i class="fa fa-book" aria-hidden="true"></i>
                    <p>Former Z-Out</p>
                </a>
                @endcan
                @can(['former_xout'])
                <a class="{{ Request::routeIs('x.out') ? 'active' : '' }}" href="{{ route('x.out') }}"><i
                        class="fa fa-sign-out" aria-hidden="true"></i>
                    <p>X-Out</p>
                </a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany
    @canany(['bookings', 'beautician', 'clients', 'leave_mgt'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('tab.salon.booking.list', 'tab.salon.beautician.list', 'tab.admin.client.list', 'admin.leave.management.list') ? 'active' : '' }}"
            role="button">
            <i class="fa fa-scissors" aria-hidden="true"></i>
            <span class="nav-link-text">Salon</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('tab.salon.booking.list', 'tab.salon.beautician.list', 'tab.admin.client.list', 'admin.leave.management.list') ? 'opening' : '' }} {{ $category_name == 'salon' ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can(['bookings'])
                <a class="{{ Request::routeIs('tab.salon.booking.list') ? 'active' : '' }} {{ $page_name == 'booking_details' ? 'active' : '' }}"
                    href="{{ route('tab.salon.booking.list') }}"><i class="fa fa-book" aria-hidden="true"></i>
                    <p>Bookings</p>
                </a>
                @endcan
                @can(['beautician'])
                <a class="{{ Request::routeIs('tab.salon.beautician.list') ? 'active' : '' }} {{ $page_name == 'salon_beautician_details' ? 'active' : '' }}"
                    href="{{ route('tab.salon.beautician.list') }}"><i class="fa fa-user-md" aria-hidden="true"></i>
                    <p>Beauticians</p>
                </a>
                @endcan
                @can(['clients'])
                <a class="{{ Request::routeIs('tab.admin.client.list') ? 'active' : '' }} {{ $page_name == 'client_details' ? 'active' : '' }}"
                    href="{{ route('tab.admin.client.list') }}"><i class="fa fa-user" aria-hidden="true"></i>
                    <p>Clients</p>
                </a>
                @endcan
                @can(['leave_mgt'])
                <a class="{{ Request::routeIs('admin.leave.management.list') ? 'active' : '' }}"
                    href="{{ route('admin.leave.management.list') }}"><i class="fa fa-level-down"
                        aria-hidden="true"></i>
                    <p>Leave Management</p>
                </a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany
    @canany(['beautician'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('makeup.booking.service', 'makeup.booking.bridal', 'makeup.booking.groom', 'makeup.booking.party') ? 'active' : '' }} {{ $category_name == 'makeup' ? 'active' : '' }}"
            role="button">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span class="nav-link-text">Makeup Booking</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('makeup.booking.service', 'makeup.booking.bridal', 'makeup.booking.groom', 'makeup.booking.party') ? 'opening' : '' }}  {{ $category_name == 'makeup' ? 'opening' : '' }}">
            <div class="sub-nav-links">
                <a class="{{ Request::routeIs('makeup.booking.service') ? 'active' : '' }} {{ $page_name == 'makeup_services' ? 'active' : '' }} "
                    href="{{ route('makeup.booking.service') }}"><i class="fa fa-male" aria-hidden="true"></i>
                    <p>{{ __('Service') }}</p>
                </a>

                <a class="{{ Request::routeIs('makeup.booking.bridal') ? 'active' : '' }}"
                    href="{{ route('makeup.booking.bridal') }}"><i class="fa fa-user-secret" aria-hidden="true"></i>
                    <p>{{ __('Booking') }}</p>
                </a>

                {{-- <a class="{{ Request::routeIs('makeup.booking.groom') ? 'active' : '' }}"
                href="{{ route('makeup.booking.groom') }}"><i class="fa fa-male" aria-hidden="true"></i>
                <p>{{ __('Groom') }}</p>
                </a>
                <a class="{{ Request::routeIs('makeup.booking.party') ? 'active' : '' }}"
                    href="{{ route('makeup.booking.party') }}"><i class="fa fa-smile-o" aria-hidden="true"></i>
                    <p>{{ __('Party') }}</p>
                </a> --}}
            </div>
        </div>
    </li>
    @endcanany
    @canany(['view_own_appointments', 'view_appointment_of_others', 'view_beautician_of_others', 'view_own_calender'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('tab.my.account.booking.service.list', 'report.appointment.list', 'beatician.calender', 'all.beatician.calender') ? 'active' : '' }}"
            role="button">
            <i class="fa fa-calendar" aria-hidden="true"></i></i>
            <span class="nav-link-text">Appointments</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('tab.my.account.booking.service.list', 'report.appointment.list', 'beatician.calender', 'all.beatician.calender') ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can('view_own_appointments')
                <a class="{{ Request::routeIs('tab.my.account.booking.service.list') ? 'active' : '' }}"
                    href="{{ route('tab.my.account.booking.service.list') }}"><i class="fa fa-user-secret"
                        aria-hidden="true"></i>
                    <p>My Appointments</p>
                </a>
                @endcan
                @can('view_appointment_of_others')
                <a class="{{ Request::routeIs('report.appointment.list') ? 'active' : '' }}"
                    href="{{ route('report.appointment.list') }}"><i class="fa fa-book" aria-hidden="true"></i>
                    <p>All Appointments</p>
                </a>
                @endcan
                @can('view_own_calender')
                <a class="{{ Request::routeIs('beatician.calender') ? 'active' : '' }}"
                    href="{{ route('beatician.calender', ['user_id' => auth()->id()]) }}"><i class="fa fa-calendar"
                        aria-hidden="true"></i>
                    <p>My Calender</p>
                </a>
                @endcan
                @can('view_beautician_of_others')
                <a class="{{ Request::routeIs('all.beatician.calender') ? 'active' : '' }}"
                    href="{{ route('all.beatician.calender') }}"><i class="fa fa-smile-o" aria-hidden="true"></i>
                    <p> All Beautician</p>
                </a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    @canany(['brands', 'item_groupe', 'items', 'purchase'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('tab.stock.stock.brand.list', 'tab.stock.stock.item.group.list', 'tab.stock.stock.item.list', 'tab.stock.stock.purchase.list') ? 'active' : '' }} {{ strtolower($category_name) == 'stock' && !in_array($page_name, ['voucher_details', 'open_balance_details']) ? 'active' : '' }}"
            role="button">
            <i class="fa fa-scissors" aria-hidden="true"></i>
            <span class="nav-link-text">Stock</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('tab.stock.stock.brand.list', 'tab.stock.stock.item.group.list', 'tab.stock.stock.item.list', 'tab.stock.stock.purchase.list') ? 'opening' : '' }} {{ strtolower($category_name) == 'stock' && !in_array($page_name, ['voucher_details', 'open_balance_details']) ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can('brands')
                <a class="{{ Request::routeIs('tab.stock.stock.brand.list') ? 'active' : '' }} {{ $page_name == 'brand_details' ? 'active' : '' }}"
                    href="{{ route('tab.stock.stock.brand.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Brands</p>
                </a>
                @endcan
                @can('item_groupe')
                <a class="{{ Request::routeIs('tab.stock.stock.item.group.list') ? 'active' : '' }} {{ $page_name == 'item_group_details' ? 'active' : '' }}"
                    href="{{ route('tab.stock.stock.item.group.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Item Groups</p>
                </a>
                @endcan
                @can('items')
                <a class="{{ Request::routeIs('tab.stock.stock.item.list') ? 'active' : '' }} {{ $page_name == 'item_details' ? 'active' : '' }}"
                    href="{{ route('tab.stock.stock.item.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Items</p>
                </a>
                @endcan
                @can('purchase')
                <a class="{{ Request::routeIs('tab.stock.stock.purchase.list') ? 'active' : '' }} {{ $page_name == 'purchase_details' ? 'active' : '' }}"
                    href="{{ route('tab.stock.stock.purchase.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Purchase</p>
                </a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    @canany(['vendor_view', 'vendor_create', 'vendor_edit', 'vendor_delete'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('tab.vendors.vendor.list') ? 'active' : '' }}" role="button">
            <i class="fa fa-scissors" aria-hidden="true"></i>
            <span class="nav-link-text">Vendor</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('tab.vendors.vendor.list') ? 'opening' : '' }} {{ $category_name == 'vendors' ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @canany(['vendor_view', 'vendor_create', 'vendor_edit', 'vendor_delete'])
                <a class="{{ Request::routeIs('tab.vendors.vendor.list') ? 'active' : '' }} {{ $page_name == 'vendor_details' ? 'active' : '' }}"
                    href="{{ route('tab.vendors.vendor.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Vendor</p>
                </a>
                @endcanany
            </div>
        </div>
    </li>
    @endcanany

    @canany(['ledger_view', 'voucher_view', 'opening_balance_view', 'expense_view', 'trial_balance_view', 'profit_loss_view', 'balance_sheet_view', 'day_book_view', 'financial_year_view'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('tab.accounts.account.list', 'tab.stock.stock.voucher.list', 'tab.stock.open.balance.list', 'tab.expenses.expense.list', 'trial-balance', 'profit-loss', 'balance-sheet', 'day-book', 'financial-year') ? 'active' : '' }} {{ $category_name == 'accounts' || in_array($page_name, ['voucher_details', 'open_balance_details', 'expense_details']) ? 'active' : '' }}"
            role="button">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span class="nav-link-text">Accounts</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('tab.accounts.account.list', 'tab.stock.stock.voucher.list', 'tab.stock.open.balance.list', 'tab.expenses.expense.list', 'trial-balance', 'profit-loss', 'balance-sheet', 'day-book', 'financial-year') ? 'opening' : '' }} {{ $category_name == 'accounts' || in_array($page_name, ['voucher_details', 'open_balance_details', 'expense_details']) ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can('ledger_view')
                <a class="{{ Request::routeIs('tab.accounts.account.list') ? 'active' : '' }} {{ $page_name == 'account_details' ? 'active' : '' }}"
                    href="{{ route('tab.accounts.account.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Ledgers</p>
                </a>
                @endcan
                @can('voucher_view')
                <a class="{{ Request::routeIs('tab.stock.stock.voucher.list') ? 'active' : '' }} {{ $page_name == 'voucher_details' ? 'active' : '' }}"
                    href="{{ route('tab.stock.stock.voucher.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Voucher</p>
                </a>
                @endcan
                @can('opening_balance_view')
                <a class="{{ Request::routeIs('tab.stock.open.balance.list') ? 'active' : '' }} {{ $page_name == 'open_balance_details' ? 'active' : '' }}"
                    href="{{ route('tab.stock.open.balance.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Opening Balance</p>
                </a>
                @endcan
                @can('expense_view')
                <a class="{{ Request::routeIs('tab.expenses.expense.list') ? 'active' : '' }} {{ $page_name == 'expense_details' ? 'active' : '' }}"
                    href="{{ route('tab.expenses.expense.list') }}"><i class="fa fa-dot-circle-o"
                        aria-hidden="true"></i>
                    <p>Expense</p>
                </a>
                @endcan
                @can('trial_balance_view')
                <a class="{{ Request::routeIs('trial-balance') ? 'active' : '' }}"
                    href="{{ route('trial-balance') }}"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>
                    <p>Trial Balance</p>
                </a>
                @endcan
                @can('profit_loss_view')
                <a class="{{ Request::routeIs('profit-loss') ? 'active' : '' }}"
                    href="{{ route('profit-loss') }}"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>
                    <p>Profit And Loss</p>
                </a>
                @endcan
                @can('balance_sheet_view')
                <a class="{{ Request::routeIs('balance-sheet') ? 'active' : '' }}"
                    href="{{ route('balance-sheet') }}"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>
                    <p>Balance Sheet</p>
                </a>
                @endcan
                @can('day_book_view')
                <a class="{{ Request::routeIs('day-book') ? 'active' : '' }}"
                    href="{{ route('day-book') }}"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>
                    <p>Day Book</p>
                </a>
                @endcan
                @can('financial_year_view')
                <a class="{{ Request::routeIs('financial-year') ? 'active' : '' }}"
                    href="{{ route('financial-year') }}"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>
                    <p>Financial Year</p>
                </a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany
    @canany(['denomination'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('denomination') ? 'active' : '' }}" role="button">
            <i class="fa fa-cogs" aria-hidden="true"></i>
            <span class="nav-link-text">Settings</span>
        </a>
        <div class="dropdown-content {{ Request::routeIs('denomination') ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can(['denomination'])
                <a class="{{ Request::routeIs('denomination') ? 'active' : '' }}"
                    href="{{ route('denomination') }}"><i class="fa fa-laptop" aria-hidden="true"></i>
                    <p>Denomination</p>
                </a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany
    @canany(['sales_report', 'daily_sales_report', 'client_report', 'vendor_report', 'tax_report', 'service_report', 'service_used_product_report', 'beautician_report', 'consultation_report', 'edit_log_report'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('sales.report', 'daily-sales.report', 'client.report', 'vendor.report', 'tax.report', 'service.report', 'service-used-product.report', 'beautician.report', 'consultation.report', 'edit-log.report') ? 'active' : '' }}"
            role="button">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span class="nav-link-text">Reports</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('sales.report', 'daily-sales.report', 'client.report', 'vendor.report', 'tax.report', 'service.report', 'service-used-product.report', 'beautician.report', 'consultation.report', 'edit-log.report', 'whatsapp-invoice.report') ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can('sales_report')
                <a class="{{ Request::routeIs('sales.report') ? 'active' : '' }}"
                    href="{{ route('sales.report') }}"><i class="fa fa-usd" aria-hidden="true"></i>
                    <p>Sales Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('daily_sales_report')
                <a class="{{ Request::routeIs('daily-sales.report') ? 'active' : '' }}"
                    href="{{ route('daily-sales.report') }}"><i class="fa fa-calendar" aria-hidden="true"></i>
                    <p>Daily Sales Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('client_report')
                <a class="{{ Request::routeIs('client.report') ? 'active' : '' }}"
                    href="{{ route('client.report') }}"><i class="fa fa-user" aria-hidden="true"></i>
                    <p>Client Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('vendor_report')
                <a class="{{ Request::routeIs('vendor.report') ? 'active' : '' }}"
                    href="{{ route('vendor.report') }}"><i class="fa fa-truck" aria-hidden="true"></i>
                    <p>Vendor Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('tax_report')
                <a class="{{ Request::routeIs('tax.report') ? 'active' : '' }}" href="{{ route('tax.report') }}">
                    <i class="fa fa-percent" aria-hidden="true"></i>
                    <p>Tax Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('service_report')
                <a class="{{ Request::routeIs('service.report') ? 'active' : '' }}"
                    href="{{ route('service.report') }}"><i class="fa fa-scissors" aria-hidden="true"></i>
                    <p>Service Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('service_used_product_report')
                <a class="{{ Request::routeIs('service-used-product.report') ? 'active' : '' }}"
                    href="{{ route('service-used-product.report') }}"><i class="fa fa-cubes" aria-hidden="true"></i>
                    <p>Service Used Products</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('beautician_report')
                <a class="{{ Request::routeIs('beautician.report') ? 'active' : '' }}"
                    href="{{ route('beautician.report') }}"><i class="fa fa-user-secret" aria-hidden="true"></i>
                    <p>Beautician Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('consultation_report')
                <a class="{{ Request::routeIs('consultation.report') ? 'active' : '' }}"
                    href="{{ route('consultation.report') }}"><i class="fa fa-user-secret" aria-hidden="true"></i>
                    <p>Consultation Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('edit_log_report')
                <a class="{{ Request::routeIs('edit-log.report') ? 'active' : '' }}"
                    href="{{ route('edit-log.report') }}"><i class="fa fa-history" aria-hidden="true"></i>
                    <p>Edit Log Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('whatsapp_invoice_report')
                <a class="{{ Request::routeIs('whatsapp-invoice.report') ? 'active' : '' }}"
                    href="{{ route('whatsapp-invoice.report') }}">
                    <i class="fa fa-whatsapp" aria-hidden="true"></i>
                    <p>Whatsapp Invoice Report</p>
                </a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    @canany(['emp_create', 'emp_edit', 'salary_create', 'salary_edit', 'incentive_term_create', 'incentive_term_edit',
    'incentive_create', 'incentive_edit', 'incentive_view', 'commission_create', 'commission_edit', 'commission_view',
    'attendance_view', 'adv_salary_create', 'adv_salary_edit', 'payroll_create', 'payment_create', 'payment_edit'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('employee', 'incentive-terms', 'incentive', 'commission', 'attendance', 'advance-salary', 'payroll') ? 'active' : '' }}" role="button">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span class="nav-link-text">Payroll Management</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('employee', 'incentive-terms', 'incentive', 'commission', 'attendance', 'advance-salary', 'payroll') ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @canany(['emp_create', 'emp_edit', 'salary_create', 'salary_edit'])
                <a class="{{ Request::routeIs('employee') ? 'active' : '' }}" href="{{ route('employee') }}"><i
                        class="fa fa-users" aria-hidden="true"></i>
                    <p>Employee</p>
                </a>
                @endcanany
            </div>
            <div class="sub-nav-links">
                @canany(['incentive_term_create', 'incentive_term_edit'])
                <a class="{{ Request::routeIs('incentive-terms') ? 'active' : '' }}"
                    href="{{ route('incentive-terms') }}"><i class="fa fa-info-circle" aria-hidden="true"></i>
                    <p>Incentive Terms</p>
                </a>
                @endcanany
            </div>
            <div class="sub-nav-links">
                @canany(['incentive_create', 'incentive_edit', 'incentive_view'])
                <a class="{{ Request::routeIs('incentive') ? 'active' : '' }}" href="{{ route('incentive') }}"><i
                        class="fa fa-gift" aria-hidden="true"></i>
                    <p>Incentive</p>
                </a>
                @endcanany
            </div>
            <div class="sub-nav-links">
                @canany(['commission_create', 'commission_edit', 'commission_view'])
                <a class="{{ Request::routeIs('commission') ? 'active' : '' }}" href="{{ route('commission') }}"><i
                        class="fa fa-percent" aria-hidden="true"></i>
                    <p>Commission</p>
                </a>
                @endcanany
            </div>
            <div class="sub-nav-links">
                @can('attendance_view')
                <a class="{{ Request::routeIs('attendance') ? 'active' : '' }}"
                    href="{{ route('attendance') }}"><i class="fa fa-calendar-o" aria-hidden="true"></i>
                    <p>Attendance</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @canany(['adv_salary_edit', 'adv_salary_create'])
                <a class="{{ Request::routeIs('advance-salary') ? 'active' : '' }}"
                    href="{{ route('advance-salary') }}"><i class="fa fa-usd" aria-hidden="true"></i>
                    <p>Advance Salary</p>
                </a>
                @endcanany
            </div>
            <div class="sub-nav-links">
                @canany(['payroll_create', 'payment_edit', 'payment_create'])
                <a class="{{ Request::routeIs('payroll') ? 'active' : '' }}" href="{{ route('payroll') }}"><i
                        class="fa fa-money" aria-hidden="true"></i>
                    <p>Payroll</p>
                </a>
                @endcanany
            </div>
        </div>
    </li>
    @endcanany

    @canany(['salary_report', 'commission_report', 'attendance_report', 'payroll_report', 'incentive_report', 'working_hours_report'])
    <li class="bronwz-dropdown">
        <a class="nav-link-d {{ Request::routeIs('salary.report', 'commission.report', 'attendance.report', 'payroll.report', 'incentive.report', 'working-hours.report') ? 'active' : '' }}"
            role="button">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span class="nav-link-text">Payroll Reports</span>
        </a>
        <div
            class="dropdown-content {{ Request::routeIs('salary.report', 'commission.report', 'attendance.report', 'payroll.report', 'incentive.report', 'working-hours.report') ? 'opening' : '' }}">
            <div class="sub-nav-links">
                @can('salary_report')
                <a class="{{ Request::routeIs('salary.report') ? 'active' : '' }}"
                    href="{{ route('salary.report') }}"><i class="fa fa-usd" aria-hidden="true"></i>
                    <p>Salary Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('commission_report')
                <a class="{{ Request::routeIs('commission.report') ? 'active' : '' }}"
                    href="{{ route('commission.report') }}"><i class="fa fa-percent" aria-hidden="true"></i>
                    <p>Commission Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('attendance_report')
                <a class="{{ Request::routeIs('attendance.report') ? 'active' : '' }}"
                    href="{{ route('attendance.report') }}"><i class="fa fa-calendar-check-o"
                        aria-hidden="true"></i>
                    <p>Attendance Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('payroll_report')
                <a class="{{ Request::routeIs('payroll.report') ? 'active' : '' }}"
                    href="{{ route('payroll.report') }}"><i class="fa fa-money" aria-hidden="true"></i>
                    <p>Payroll Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('incentive_report')
                <a class="{{ Request::routeIs('incentive.report') ? 'active' : '' }}"
                    href="{{ route('incentive.report') }}"><i class="fa fa-gift" aria-hidden="true"></i>
                    <p>Incentive Report</p>
                </a>
                @endcan
            </div>
            <div class="sub-nav-links">
                @can('working_hours_report')
                <a class="{{ Request::routeIs('working-hours.report') ? 'active' : '' }}"
                    href="{{ route('working-hours.report') }}"><i class="fa fa-gift" aria-hidden="true"></i>
                    <p>Working Hours Report</p>
                </a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany
</ul>