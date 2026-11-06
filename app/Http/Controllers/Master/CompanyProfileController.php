<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BusinessConfig;
use Database\Seeders\BusinessConfigTableSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CompanyProfileController extends Controller
{
    private $business_configs = null;

    public function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->can('bussines_profile'), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->ensureBusinessConfigsExist();

        $business_configs = $this->getBusinessConfigs();
        $config_groups = $this->getConfigGroups();
        $config_labels = $this->getConfigLabels();
        $config_hints = $this->getConfigHints();

        return view('master.company-profile', compact(
            'business_configs',
            'config_groups',
            'config_labels',
            'config_hints'
        ));
    }

    public function save(Request $request)
    {
        $this->ensureBusinessConfigsExist();

        $this->applyValidation($request);

        try {
            \DB::transaction(function () use ($request) {
                $this->saveFromRequest($request);
            });
        } catch (QueryException $e) {
            throw $this->toValidationException($e);
        }

        return response()->json([
            'gotourl' => route('admin.business.profile'),
        ]);
    }

    private function ensureBusinessConfigsExist(): void
    {
        if (BusinessConfig::count() === 0) {
            (new BusinessConfigTableSeeder())->run();
            $this->business_configs = null;
        }
    }

    private function applyValidation(Request $request): void
    {
        $rules = [];
        $messages = [];

        foreach ($this->getBusinessConfigs() as $config) {
            $key = $config->config_key;
            $rules[$key] = $this->resolveValidationRules($config);
            $messages = array_merge($messages, $this->resolveValidationMessages($key));
        }

        $request->validate($rules, $messages, $this->getConfigLabels());
    }

    private function resolveValidationRules(BusinessConfig $config): string
    {
        $key = $config->config_key;
        $dbRules = trim((string) $config->validation);

        $extraRules = match ($key) {
            'email' => 'email:rfc,dns|max:255',
            'address_english' => 'string|max:1000',
            'paid_leave_limit' => 'numeric|min:0',
            'salary_days_in_a_month' => 'numeric|min:1|max:31',
            'gst_number' => 'string|max:15',
            default => 'string|max:255',
        };

        if ($dbRules === '') {
            return 'nullable|'.$extraRules;
        }

        return $dbRules.'|'.$extraRules;
    }

    private function resolveValidationMessages(string $key): array
    {
        $messages = [
            "{$key}.required" => __('Please enter :attribute.'),
            "{$key}.email" => __('Please enter a valid email address for :attribute.'),
            "{$key}.numeric" => __(':attribute must be a valid number.'),
            "{$key}.string" => __(':attribute must be text.'),
            "{$key}.max" => __(':attribute cannot exceed the maximum allowed length.'),
        ];

        if ($key === 'paid_leave_limit') {
            $messages["{$key}.min"] = __('Paid leave limit cannot be negative.');
        } elseif ($key === 'salary_days_in_a_month') {
            $messages["{$key}.min"] = __('Salary days in a month must be at least 1.');
            $messages["{$key}.max"] = __('Salary days in a month cannot exceed 31.');
        } else {
            $messages["{$key}.min"] = __(':attribute must be at least :min.');
        }

        return $messages;
    }

    private function toValidationException(QueryException $exception): ValidationException|\Throwable
    {
        $message = $exception->getMessage();

        if (preg_match("/Column 'config_value' cannot be null.*`config_key` = ([a-z0-9_]+)/i", $message, $matches)) {
            $key = $matches[1];
            $label = $this->getConfigLabels()[$key] ?? ucwords(str_replace('_', ' ', $key));

            return ValidationException::withMessages([
                $key => [__('Please enter :attribute.', ['attribute' => $label])],
            ]);
        }

        return $exception;
    }

    private function saveFromRequest(Request $request)
    {
        $record_updated = false;

        foreach ($this->getBusinessConfigs() as $key => $config) {
            if (! $request->has($key)) {
                continue;
            }

            $value = $request->input($key);
            $config->config_value = is_scalar($value) ? trim((string) $value) : '';
            $config->save();

            if ($record_updated === false && $config->wasChanged()) {
                $record_updated = true;
            }
        }

        if ($record_updated) {
            voco_flash()->success(__('Company profile updated!'));
        } else {
            voco_flash()->info(__('No configurations were altered!'));
        }
    }

    private function getBusinessConfigs()
    {
        if ($this->business_configs === null) {
            $this->business_configs = BusinessConfig::orderBy('display_order')
                ->get()
                ->keyBy('config_key');
        }

        return $this->business_configs;
    }

    private function getConfigGroups(): array
    {
        return [
            'business' => [
                'title' => __('Business Information'),
                'description' => __('Company name, contact details, and address shown on invoices and communications.'),
                'keys' => [
                    'business_title_english',
                    'phone',
                    'email',
                    'address_english',
                    'location_english',
                    'city_english',
                ],
            ],
            'operations' => [
                'title' => __('Operating Hours'),
                'description' => __('Hours and ordering times displayed to customers.'),
                'keys' => [
                    'opening_hours',
                    'telephone_ordering_time',
                ],
            ],
            'payment' => [
                'title' => __('Payment & Tax'),
                'description' => __('UPI and GST details used on invoices and receipts.'),
                'keys' => [
                    'upi_id',
                    'upi_name',
                    'gst_number',
                    'is_send_whatsapp_invoice',
                ],
            ],
            'payroll' => [
                'title' => __('Payroll Settings'),
                'description' => __('Defaults used for leave and salary calculations.'),
                'keys' => [
                    'paid_leave_limit',
                    'salary_days_in_a_month',
                ],
            ],
        ];
    }

    private function getConfigLabels(): array
    {
        return [
            'business_title_english' => __('Business Name'),
            'phone' => __('Phone Number'),
            'email' => __('Email Address'),
            'address_english' => __('Address'),
            'location_english' => __('Location / Area'),
            'city_english' => __('City'),
            'opening_hours' => __('Opening Hours'),
            'telephone_ordering_time' => __('Telephone Ordering Time'),
            'upi_id' => __('UPI ID'),
            'upi_name' => __('UPI Display Name'),
            'gst_number' => __('GST Number'),
            'paid_leave_limit' => __('Paid Leave Limit (days)'),
            'salary_days_in_a_month' => __('Salary Days in a Month'),
            'is_send_whatsapp_invoice' => __('Is Send whatsapp Invoice'),
        ];
    }

    private function getConfigHints(): array
    {
        return [
            'opening_hours' => __('Example: Mon–Sat 9:00 AM – 8:00 PM'),
            'telephone_ordering_time' => __('Example: 10:00 AM – 7:00 PM'),
            'upi_id' => __('Example: business@upi'),
            'paid_leave_limit' => __('Maximum paid leave days allowed per employee per year.'),
            'salary_days_in_a_month' => __('Number of days used to calculate daily salary (typically 30).'),
        ];
    }
}
