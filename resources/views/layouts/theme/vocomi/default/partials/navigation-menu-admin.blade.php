<ul class="nav">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard') }}">
            {{ fa_icon('tachometer', 'nav-icon') }}
            {{ __('Dashboard') }}
        </a>
    </li>


    {{-- ==================slider section ================================= --}}
    @canany(['banner', 'service_type'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('user-secret', 'nav-icon') }}
                {{ __('Home Page') }}
            </a>

            <ul class="nav-dropdown-items">
                @can('banner')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home.slider') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Banner') }}
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="nav-dropdown-items">
                @can('service_type')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('service.type') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Service Type') }}
                        </a>
                    </li>
                @endcan
            </ul>

        </li>
    @endcanany
    {{-- ==================slider section ================================= --}}

    @canany(['bussines_profile'])
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.business.profile') }}">
                {{ fa_icon('address-card-o', 'nav-icon') }}
                {{ __('Business Profile') }}
            </a>
        </li>
    @endcanany
    @canany(['create_role', 'create_user'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('user-secret', 'nav-icon') }}
                {{ __('ACL') }}
            </a>

            <ul class="nav-dropdown-items">
                @can('create_role')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.acl.role.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Roles') }}
                        </a>
                    </li>
                @endcan

                @can('create_user')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.acl.user.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Users') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcanany

    @canany(['outlet', 'global_taxes', 'service_categories', 'services', 'service_type_mgt', 'uom', 'shift'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('database', 'nav-icon') }}
                {{ __('Master Data') }}
            </a>
            <ul class="nav-dropdown-items">

                @can('outlet')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.master.outlet.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Outlet11') }}
                        </a>
                    </li>
                @endcan
                @can('global_taxes')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.master.global.tax.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Global Taxes') }}
                        </a>
                    </li>
                @endcan
                @can('service_categories')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.master.service.category.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Service Categories') }}
                        </a>
                    </li>
                @endcan
                @can('services')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.master.service.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Services') }}
                        </a>
                    </li>
                @endcan
                @can('service_type_mgt')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.master.service.type.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Service Type Management') }}
                        </a>
                    </li>
                @endcan
                @can('uom')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.master.unit.of.measurement.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Unit Of Measurements') }}
                        </a>
                    </li>
                @endcan
                @can('shift')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.master.shift.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Shift') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcanany
    @canany(['former_zout', 'register_open_close', 'former_xout'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('calculator', 'nav-icon') }}
                {{ __('X/Z Out') }}
            </a>
            <ul class="nav-dropdown-items">

                @can(['register_open_close'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('float_amt') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __(' Register Open/Close') }}
                        </a>
                    </li>
                @endcan
                @can(['former_zout'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('re.print.bill') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Former Z-Out') }}
                        </a>
                    </li>
                @endcan
                @can(['former_xout'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('x.out') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('X-Out') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcanany

    @canany(['bookings', 'beautician', 'clients', 'leave_mgt', 'product_delivery'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('heart-o', 'nav-icon') }}
                {{ __('Salon') }}
            </a>
            <ul class="nav-dropdown-items">
                @can(['bookings'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.salon.booking.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Bookings') }}
                        </a>
                    </li>
                @endcan
                @can(['product_delivery'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('salon.booking.product.delivery') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Product Delivery') }}
                        </a>
                    </li>
                @endcan

                @can(['review_booking'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.salon.review.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Review Bookings') }}
                        </a>
                    </li>
                @endcan
                {{-- <li class="nav-item">
        <a class="nav-link" href="{{ route('salon.review.form') }}">
          {{ fa_icon('angle-right', 'nav-icon') }}
          {{ __('Review Form') }}
        </a>
      </li> --}}
                @can(['beatician_list'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.salon.beautician.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Beauticians') }}
                        </a>
                    </li>
                @endcan
                @can(['clients'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.admin.client.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Clients') }}
                        </a>
                    </li>
                @endcan
                @can(['leave_mgt'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.leave.management.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Leave Management') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
        {{-- @endcan --}}
    @endcanany
    @canany(['brands', 'item_groupe', 'items'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('cubes', 'nav-icon') }}
                {{ __('Stock') }}
            </a>
            <ul class="nav-dropdown-items">
                @can(['brands'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.stock.stock.brand.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Brands') }}
                        </a>
                    </li>
                @endcan
                @can(['item_groupe'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.stock.stock.item.group.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Item Groups') }}
                        </a>
                    </li>
                @endcan
                @can(['items'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.stock.stock.item.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Items') }}
                        </a>
                    </li>
                @endcan
                @can(['vendor'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.stock.stock.item.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Vendor') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcanany
    {{-- <li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#">
      {{ fa_icon('volume-control-phone', 'nav-icon') }}
      {{ __('Customer Service') }}
    </a>
    <ul class="nav-dropdown-items">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('tab.admin.complaint.list') }}">
          {{ fa_icon('angle-right', 'nav-icon') }}
          {{ __('Complaints') }}
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{{ route('tab.admin.contact.message.list') }}">
          {{ fa_icon('angle-right', 'nav-icon') }}
          {{ __('Contact Messages') }}
        </a>
      </li>
    </ul>
  </li> --}}
    @canany(['discount_coupons', 'review'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('code', 'nav-icon') }}
                {{ __('Development') }}
            </a>
            <ul class="nav-dropdown-items">
                @foreach (glob(resource_path('views/layouts/theme/vocomi/default/partials/auto-generated') . '/*.php') as $file)
                    @include('layouts.theme.vocomi.default.partials.auto-generated.' . substr(basename($file), 0, -10))
                @endforeach
            </ul>
        </li>
    @endcanany
    @canany(['beautician'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('calculator', 'nav-icon') }}
                {{ __('Makeup Booking') }}
            </a>
            <ul class="nav-dropdown-items">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('makeup.booking.bridal') }}">
                        {{ fa_icon('angle-right', 'nav-icon') }}
                        {{ __('Bridal') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('makeup.booking.groom') }}">
                        {{ fa_icon('angle-right', 'nav-icon') }}
                        {{ __('Groom') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('makeup.booking.party') }}">
                        {{ fa_icon('angle-right', 'nav-icon') }}
                        {{ __('Party') }}
                    </a>
                </li>
            </ul>
        </li>
    @endcanany
    @canany(['view_own_appointments', 'view_appointment_of_others'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('calendar', 'nav-icon') }}
                {{ __('Appointments') }}
            </a>
            <ul class="nav-dropdown-items">

                @can('view_own_appointments')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.my.account.booking.service.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('My Appointments') }}
                        </a>
                    </li>
                @endcan

                @can('view_appointment_of_others')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('report.appointment.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('All Appointments') }}
                        </a>
                    </li>
                @endcan

                @can('view_own_calender')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('beatician.calender', ['user_id' => auth()->id()]) }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('My Calender') }}
                        </a>
                    </li>
                @endcan

                @can('view_beautician_of_others')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('all.beatician.calender') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('All Beautician') }}
                        </a>
                    </li>
                @endcan

            </ul>
        </li>
    @endcanany
    @canany(['translation', 'holiday', 'exchange_rate', 'price_preference', 'denomination'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('cogs', 'nav-icon') }}
                {{ __('Settings') }}
            </a>

            @can(['denomination'])
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('denomination') }}">
                            {{ fa_icon('tachometer', 'nav-icon') }}
                            {{ __('Denomination') }}

                        </a>
                    </li>
                @endcan
                @can(['translation'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.admin.locale.phrase.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Translations') }}
                        </a>
                    </li>
                @endcan
                {{-- <li class="nav-item">
        <a class="nav-link" href="{{ route('close.float.denomination') }}">
          {{ fa_icon('angle-right', 'nav-icon') }}
          {{ __('Close Amount') }}
        </a>
      </li> --}}
                @can(['holiday'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.holiday') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Holidays') }}
                        </a>
                    </li>
                @endcan
                @can(['exchange_rate'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('master.exchange.rate.index') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Exchange Rates') }}
                        </a>
                    </li>
                @endcan
                @can(['price_preference'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tab.admin.print.preference.list') }}">
                            {{ fa_icon('angle-right', 'nav-icon') }}
                            {{ __('Print Preference') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </li>
    @endcanany
    @canany(['sales'])
        <li class="nav-item nav-dropdown">
            <a class="nav-link nav-dropdown-toggle" href="#">
                {{ fa_icon('cogs', 'nav-icon') }}
                {{ __('Sales') }}
            </a>
            <ul class="nav-dropdown-items">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sales.report') }}">
                        {{ fa_icon('tachometer', 'nav-icon') }}
                        {{ __('Sales Report') }}

                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('daily-sales.report') }}">
                        {{ fa_icon('calendar', 'nav-icon') }}
                        {{ __('Daily Sales Report') }}
                    </a>
                </li>
            </ul>
        </li>
    @endcanany

</ul>
