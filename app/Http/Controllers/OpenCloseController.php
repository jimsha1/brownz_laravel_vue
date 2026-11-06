<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Service\EmployeeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpenCloseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        try {
            if ($request->isMethod('post')) {
                $validated = $request->validate([
                    'title' => ['required', 'string', 'max:100'],
                    'denomination' => ['required', 'numeric', 'gt:0'],
                    'denomination_id' => ['nullable', 'integer', 'min:0'],
                ]);

                $denominationId = (int) ($validated['denomination_id'] ?? 0);
                $values = [
                    'title' => trim($validated['title']),
                    'denomination' => (float) $validated['denomination'],
                    'updated_date' => date('Y-m-d H:i:s'),
                    'status' => 1,
                ];

                if ($denominationId > 0) {
                    $exists = DB::table('denomination')
                        ->where('denomination_id', $denominationId)
                        ->exists();

                    if (! $exists) {
                        voco_flash()->error(__('Denomination not found.'));

                        return redirect()->route('denomination');
                    }

                    DB::table('denomination')
                        ->where('denomination_id', $denominationId)
                        ->update($values);

                    voco_flash()->success(__('Denomination updated successfully.'));
                } else {
                    $values['added_date'] = date('Y-m-d H:i:s');
                    DB::table('denomination')->insert($values);
                    voco_flash()->success(__('Denomination added successfully.'));
                }

                return redirect()->route('denomination');
            }

            $denomination = DB::table('denomination')
                ->orderByRaw('CAST(denomination AS DECIMAL(18,4)) ASC')
                ->orderBy('title')
                ->get();

            $is_need_row = DB::table('is_denomination_table')->where('id', 1)->first();
            $is_need = isset($is_need_row->is_need) ? (int) $is_need_row->is_need : 0;

            $current_lang = session('user_session_locale');
            if ($current_lang) {
                app()->setLocale($current_lang);
            }

            return view('openCloseMoney.denomination', [
                'denomination' => $denomination,
                'is_need' => $is_need,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to save denomination data', ['exception' => $e]);
            voco_flash()->error(__('Could not save denomination data.'));

            return redirect()->route('denomination');
        }
    }

    public function deleteDenomination(Request $request)
    {
        $denominationId = (int) $request->input('denomination_id', 0);
        if ($denominationId <= 0) {
            voco_flash()->error(__('Invalid denomination.'));

            return redirect()->route('denomination');
        }

        $deleted = DB::table('denomination')
            ->where('denomination_id', $denominationId)
            ->delete();

        if ($deleted) {
            voco_flash()->success(__('Denomination deleted successfully.'));
        } else {
            voco_flash()->error(__('Denomination not found.'));
        }

        return redirect()->route('denomination');
    }

    // Open register
    public function floatAmt(Request $request)
    {
        try {
            $user_id = auth()->id();
            $type  = "";
            $float_amonunt = array();

            if ($request->isMethod('get')) {
                $type =  $request->id;
            }
            if ($request->isMethod('post')) {
                if (hasOpenFloatAmount($user_id)) {
                    return redirect()->route('float_amt')->with('error', 'Register is already open.');
                }

                $values = array(
                    'user_id' => $user_id,
                    'amonut' =>  $request->amonut,
                    'added_date' => date("Y/m/d H:i:s"),
                    'updated_date' => date("Y/m/d H:i:s"),
                    'status' => 1,
                );
                DB::table('float_amount')->insert($values);

                if (isset($request->type) && $request->type == 0) {

                    return redirect('/salon/booking/tab/' . $request->type . '/edit');
                } else {
                    return redirect()->route('float_amt')->with('message', 'successfully added / updated');
                }
            }

            $open_float = getOpenFloatAmount($user_id);
            $open_amount = empty($open_float);
            $amonut_close_btn = !empty($open_float);
            $float_amonunt = $open_float ? collect([$open_float]) : collect([]);

            $is_denomination = false;
            if (!$open_amount && $request->register == 'close') {
                $is_denomination = true;
                $amonut_close_btn = false;
            }
            $denomination = DB::table('denomination')->get();
            $is_need_denomination = DB::table('is_denomination_table')->get();
            $is_need_table = isset($is_need_denomination[0]->is_need) ? $is_need_denomination[0]->is_need : 1;

            $cashier = Auth::user();
            $session_summary = null;
            if (!empty($open_float)) {
                $openedAt = !empty($open_float->added_date)
                    ? \Carbon\Carbon::parse($open_float->added_date)->format('d M Y, h:i A')
                    : null;

                $session_summary = [
                    'float_amt_id' => $open_float->float_amt_id,
                    'open_amount' => $open_float->amonut,
                    'opened_at' => $openedAt,
                    'cashier_name' => $cashier->name ?? __('Cashier'),
                    'is_open' => true,
                ];
            }

            $current_lang = session('user_session_locale');
            app()->setLocale($current_lang);
            return view('openCloseMoney.add_float_amt', compact(
                'is_denomination',
                'open_amount',
                'amonut_close_btn',
                'denomination',
                'float_amonunt',
                'type',
                'is_need_table',
                'user_id',
                'session_summary'
            ));
        } catch (\Exception $e) {
            Log::error("Failed to load float amount page");
            Log::error($e);
            return redirect()->route('dashboard')->with('error', 'Failed to load');
        }
    }


    //force close section ========================
    public function forcefloatAmt(Request $request)
    {
        try {
            $type  = "";
            $user_id = $request->id;
            $float_amt_id = $request->float_amt_id;

            if (empty($float_amt_id)) {
                return redirect()->route('re.print.bill')->with('error', 'Float session id is required for force close.');
            }

            if (!$this->hasValidForceCloseUnlock($float_amt_id, $user_id)) {
                return redirect()->route('re.print.bill')->with('error', __('Password confirmation is required to force close.'));
            }

            $open_float = getOpenFloatAmountById($float_amt_id, $user_id);
            if (!$open_float) {
                $this->clearForceCloseUnlock();
                return redirect()->route('re.print.bill')->with('error', 'No open register found for this float session.');
            }

            $open_amount = false;
            $amonut_close_btn = false;
            $float_amonunt = collect([$open_float]);
            $is_denomination = true;

            $denomination = DB::table('denomination')->get();
            $is_need_denomination = DB::table('is_denomination_table')->get();
            $is_need_table = isset($is_need_denomination[0]->is_need) ? $is_need_denomination[0]->is_need : 1;
            $current_lang = session('user_session_locale');
            app()->setLocale($current_lang);
            return view('openCloseMoney.force_close_denomination', compact(
                'is_denomination',
                'open_amount',
                'amonut_close_btn',
                'denomination',
                'float_amonunt',
                'type',
                'is_need_table',
                'user_id',
                'float_amt_id'
            ));
        } catch (\Exception $e) {
            Log::error("Failed to close float amount");
            Log::error($e);
            return redirect()->route('dashboard')->with('error', 'Could not close float amount');
        }
    }
    //====================================

    public function floatCloseAmt(Request $request)
    {
        try {
            $is_need_row = DB::table('is_denomination_table')
                ->where('id', '=', 1)
                ->first();
            $is_need = isset($is_need_row->is_need) ? $is_need_row->is_need : 1;
            $current_lang = session('user_session_locale');
            app()->setLocale($current_lang);
            return view('openCloseMoney.add_close_amt', compact('is_need'));
        } catch (\Exception $e) {
            Log::error("Failed to close float amount");
            Log::error($e);
            return redirect()->route('dashboard')->with('error', 'Could not close float amount');
        }
    }

    // close register with denomination
    public function closeFloatAmount(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $float_amt_id = $request->float_amt_id;

            if (empty($float_amt_id)) {
                return response('Float session id is required to close the register.', 422);
            }

            $open_float = getOpenFloatAmountById($float_amt_id, $user_id);
            if (!$open_float) {
                return response('No open register found for this float session.', 422);
            }

            // Force-closing another cashier's float requires a prior password unlock
            if ((string) $user_id !== (string) auth()->id()
                && !$this->hasValidForceCloseUnlock($float_amt_id, $user_id)) {
                return response(__('Password confirmation is required to force close.'), 422);
            }

            $float_amt_id = $open_float->float_amt_id;
            $this->insertCloseDenominations($request, $float_amt_id);
            DB::table('float_amount')->where('float_amt_id', $float_amt_id)->update(array(
                'is_closed' => 1,
                'print_bill' => 1,
                'closed_by' => auth()->id(),
                'updated_date' => date("Y/m/d H:i:s")
            ));

            $this->clearForceCloseUnlock();

            return route('close.float.print.bill', ['id' => $user_id, 'float_amt_id' => $float_amt_id]);
        } catch (\Exception $e) {
            Log::error("Failed to close float amount");
            Log::error($e);
            return redirect()->route('dashboard')->with('error', 'Could not close float amount');
        }
    }

    // close register
    public function closeFloatButton(Request $request, EmployeeService $employeeService)
    {

        try {

            if ($request->isMethod('post') && $request->boolean('force_close')) {
                $request->validate([
                    'id' => 'required',
                    'float_amt_id' => 'required',
                    'auth_password' => 'required|string',
                ]);

                $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
                if ($passwordError) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'message' => $passwordError,
                            'errors' => ['auth_password' => [$passwordError]],
                        ], 422);
                    }

                    return redirect()->route('re.print.bill')->with('error', $passwordError);
                }

                $id = $request->id;
                $float_amt_id = $request->float_amt_id;

                $open_float = getOpenFloatAmountById($float_amt_id, $id);
                if (!$open_float) {
                    $message = __('No open register found for this float session.');
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['message' => $message], 422);
                    }
                    return redirect()->route('re.print.bill')->with('error', $message);
                }

                $draftCount = $this->countUnprocessedDraftOrders($id);
                if ($draftCount > 0) {
                    $message = __('All orders should be processed before closing register.');
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['message' => $message], 422);
                    }
                    return redirect()->route('re.print.bill')->with('error', $message);
                }

                $this->storeForceCloseUnlock($float_amt_id, $id);

                $redirectUrl = route('force_close_float_amt', [
                    'id' => $id,
                    'float_amt_id' => $float_amt_id,
                ]);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['gotourl' => $redirectUrl]);
                }

                return redirect()->to($redirectUrl);
            }

            if ($request->isMethod('post')) {
                $id = auth()->id();
            }
            if ($request->isMethod('get')) {
                // Direct URL force-close is blocked; password modal is required.
                if (!empty($request->float_amt_id)) {
                    return redirect()->route('re.print.bill')
                        ->with('error', __('Password confirmation is required to force close.'));
                }
                $id = $request->id;
            }

            $draftCount = $this->countUnprocessedDraftOrders($id);

            if($draftCount > 0){
                $errorRedirect = $request->isMethod('get')
                    ? redirect()->route('re.print.bill')
                    : redirect()->route('float_amt');
                return $errorRedirect->with('error', 'All orders should be processed bofore closing register.');
            }

            return redirect()->route('float_amt', ['register' => 'close']);
        } catch (\Exception $e) {
            Log::error("Failed to close float amount");
            Log::error($e);
            return redirect()->route('dashboard')->with('error', 'Could not close float amount');
        }
    }

    private function countUnprocessedDraftOrders($userId): int
    {
        return DB::table('bookings')
            ->join('booking_services', 'bookings.booking_id', '=', 'booking_services.booking_id')
            ->where('bookings.created_by', $userId)
            ->whereNull('bookings.canceled_by')
            ->where(function ($query) {
                $query->where('bookings.booking_code', 'draft')
                      ->orWhereNull('bookings.booking_code');
            })
            ->where(function ($query) {
                $query->whereNull('bookings.is_booking')
                      ->orWhere('bookings.is_booking', '!=', 1);
            })
            ->where('booking_services.appointment_date', '<', now()->toDateString())
            ->count();
    }

    private function storeForceCloseUnlock($floatAmtId, $userId): void
    {
        session([
            'force_close_unlock' => [
                'float_amt_id' => (string) $floatAmtId,
                'user_id' => (string) $userId,
                'auth_id' => (string) auth()->id(),
                'expires_at' => now()->addMinutes(10)->timestamp,
            ],
        ]);
    }

    private function hasValidForceCloseUnlock($floatAmtId, $userId): bool
    {
        $unlock = session('force_close_unlock');
        if (!is_array($unlock)) {
            return false;
        }

        return (string) ($unlock['float_amt_id'] ?? '') === (string) $floatAmtId
            && (string) ($unlock['user_id'] ?? '') === (string) $userId
            && (string) ($unlock['auth_id'] ?? '') === (string) auth()->id()
            && (int) ($unlock['expires_at'] ?? 0) >= now()->timestamp;
    }

    private function clearForceCloseUnlock(): void
    {
        session()->forget('force_close_unlock');
    }

    public function editClosedFloat(Request $request, EmployeeService $employeeService)
    {
        try {
            if (!$this->userCanEditClosedFloat()) {
                $message = __('You are not allowed to edit a closed register.');
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => $message], 403);
                }
                return redirect()->route('re.print.bill')->with('error', $message);
            }

            if ($request->isMethod('post') && $request->boolean('confirm_edit')) {
                $request->validate([
                    'id' => 'required',
                    'float_amt_id' => 'required',
                    'auth_password' => 'required|string',
                ]);

                $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
                if ($passwordError) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'message' => $passwordError,
                            'errors' => ['auth_password' => [$passwordError]],
                        ], 422);
                    }

                    return redirect()->route('re.print.bill')->with('error', $passwordError);
                }

                $closedFloat = $this->getClosedFloatAmount($request->float_amt_id, $request->id);
                if (!$closedFloat) {
                    $message = __('No closed register found for this float session.');
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['message' => $message], 422);
                    }
                    return redirect()->route('re.print.bill')->with('error', $message);
                }

                $this->storeEditClosedUnlock($request->float_amt_id, $request->id);

                $redirectUrl = route('edit.closed.float', [
                    'id' => $request->id,
                    'float_amt_id' => $request->float_amt_id,
                ]);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['gotourl' => $redirectUrl]);
                }

                return redirect()->to($redirectUrl);
            }

            $user_id = $request->id;
            $float_amt_id = $request->float_amt_id;

            if (empty($float_amt_id) || empty($user_id)) {
                return redirect()->route('re.print.bill')->with('error', __('Float session id is required.'));
            }

            if (!$this->hasValidEditClosedUnlock($float_amt_id, $user_id)) {
                return redirect()->route('re.print.bill')->with('error', __('Password confirmation is required to edit a closed register.'));
            }

            $closedFloat = $this->getClosedFloatAmount($float_amt_id, $user_id);
            if (!$closedFloat) {
                $this->clearEditClosedUnlock();
                return redirect()->route('re.print.bill')->with('error', __('No closed register found for this float session.'));
            }

            $savedRows = DB::table('close_denomination')
                ->where('float_amt_id', $closedFloat->float_amt_id)
                ->orderBy('close_denomination_id', 'asc')
                ->get();

            $prepared = $this->prepareClosedCountForEdit($savedRows);
            $denomination = $prepared['denomination'];
            $is_need_table = $prepared['is_need_table'];
            $prefill_total = $prepared['prefill_total'];

            $cashier = User::find($user_id);
            $float_amonunt = collect([$closedFloat]);
            $current_lang = session('user_session_locale');
            app()->setLocale($current_lang);

            return view('openCloseMoney.edit_closed_denomination', compact(
                'denomination',
                'float_amonunt',
                'is_need_table',
                'user_id',
                'float_amt_id',
                'cashier',
                'prefill_total'
            ));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to open closed register edit');
            Log::error($e);
            return redirect()->route('re.print.bill')->with('error', __('Could not open the closed register for editing.'));
        }
    }

    public function updateClosedFloatCount(Request $request)
    {
        try {
            if (!$this->userCanEditClosedFloat()) {
                return response(__('You are not allowed to edit a closed register.'), 403);
            }

            $user_id = $request->user_id;
            $float_amt_id = $request->float_amt_id;

            if (empty($float_amt_id) || empty($user_id)) {
                return response(__('Float session id is required.'), 422);
            }

            if (!$request->boolean('edit_closed')) {
                return response(__('This action only updates an existing closed count.'), 422);
            }

            if (!$this->hasValidEditClosedUnlock($float_amt_id, $user_id)) {
                return response(__('Password confirmation is required to edit a closed register.'), 422);
            }

            $closedFloat = $this->getClosedFloatAmount($float_amt_id, $user_id);
            if (!$closedFloat) {
                $this->clearEditClosedUnlock();
                return response(__('No closed register found for this float session.'), 422);
            }

            if ((int) $request->denomination_Type === 2) {
                if ($request->total_amount === null || $request->total_amount === '' || !is_numeric($request->total_amount)) {
                    return response(__('Enter the counted total.'), 422);
                }
            }

            $editReason = trim((string) $request->input('edit_reason', ''));
            if ($editReason === '') {
                return response(__('Please enter a reason for this edit.'), 422);
            }
            if (mb_strlen($editReason) > 1000) {
                return response(__('Edit reason cannot exceed 1000 characters.'), 422);
            }

            $float_amt_id = $closedFloat->float_amt_id;

            DB::transaction(function () use ($request, $float_amt_id, $editReason) {
                DB::table('close_denomination')->where('float_amt_id', $float_amt_id)->delete();
                $this->insertCloseDenominations($request, $float_amt_id);
                DB::table('float_amount')->where('float_amt_id', $float_amt_id)->update([
                    'edited_by' => auth()->id(),
                    'edited_at' => date('Y/m/d H:i:s'),
                    'edit_reason' => $editReason,
                ]);
            });

            $this->clearEditClosedUnlock();

            return route('close.float.print.bill', ['id' => $user_id, 'float_amt_id' => $float_amt_id]);
        } catch (\Exception $e) {
            Log::error('Failed to update closed register count');
            Log::error($e);
            return response(__('Could not update the closed register count.'), 422);
        }
    }

    private function insertCloseDenominations(Request $request, $floatAmtId): void
    {
        if ((int) $request->denomination_Type === 2) {
            DB::table('close_denomination')->insert([
                'float_amt_id' => $floatAmtId,
                'total' => $request->total_amount,
                'added_date' => date('Y/m/d H:i:s'),
                'updated_date' => date('Y/m/d H:i:s'),
                'status' => 1,
            ]);
            return;
        }

        $grandTotal = (float) $request->total_amount;
        for ($i = 1; $i <= (int) $request->total_count; $i++) {
            $title = 'title_' . $i;
            $denomination = 'denomination_' . $i;
            $qty = 'qty_' . $i;
            $qtyValue = $request->$qty;

            if ($qtyValue === null || $qtyValue === '' || (float) $qtyValue <= 0) {
                continue;
            }

            DB::table('close_denomination')->insert([
                'float_amt_id' => $floatAmtId,
                'title' => $request->$title,
                'amount' => $request->$denomination,
                'qty' => $qtyValue,
                'total' => $grandTotal,
                'added_date' => date('Y/m/d H:i:s'),
                'updated_date' => date('Y/m/d H:i:s'),
                'status' => 1,
            ]);
        }
    }

    private function getClosedFloatAmount($floatAmtId, $userId)
    {
        if (empty($floatAmtId) || empty($userId)) {
            return null;
        }

        return DB::table('float_amount')
            ->where('float_amt_id', $floatAmtId)
            ->where('user_id', $userId)
            ->where('is_closed', 1)
            ->first();
    }

    private function prepareClosedCountForEdit($savedRows): array
    {
        $denomination = DB::table('denomination')->orderBy('denomination_id')->get();
        $isNeedRow = DB::table('is_denomination_table')->where('id', 1)->first();
        $currentNeed = isset($isNeedRow->is_need) ? (int) $isNeedRow->is_need : 1;

        $usesLines = $savedRows->contains(function ($row) {
            return $row->qty !== null && $row->qty !== '' && (float) $row->qty > 0;
        });

        if ($savedRows->isEmpty()) {
            return [
                'denomination' => $denomination,
                'is_need_table' => $currentNeed,
                'prefill_total' => null,
            ];
        }

        if (!$usesLines) {
            return [
                'denomination' => $denomination,
                'is_need_table' => 0,
                'prefill_total' => $savedRows->first()->total ?? '',
            ];
        }

        $usedSavedIds = [];
        foreach ($denomination as $deno) {
            $match = $savedRows->first(function ($row) use ($deno, $usedSavedIds) {
                if (in_array($row->close_denomination_id, $usedSavedIds, true)) {
                    return false;
                }

                return (string) $row->title === (string) $deno->title
                    && (string) $row->amount === (string) $deno->denomination;
            });

            if (!$match) {
                $match = $savedRows->first(function ($row) use ($deno, $usedSavedIds) {
                    if (in_array($row->close_denomination_id, $usedSavedIds, true)) {
                        return false;
                    }

                    return (string) $row->title === (string) $deno->title
                        && $row->qty !== null
                        && $row->qty !== ''
                        && (float) $row->qty > 0;
                });
            }

            if ($match) {
                $usedSavedIds[] = $match->close_denomination_id;
                $deno->prefill_qty = $match->qty;
            }
        }

        foreach ($savedRows as $row) {
            if (in_array($row->close_denomination_id, $usedSavedIds, true)) {
                continue;
            }
            if ($row->qty === null || $row->qty === '' || (float) $row->qty <= 0) {
                continue;
            }

            $denomination->push((object) [
                'title' => $row->title,
                'denomination' => $row->amount,
                'prefill_qty' => $row->qty,
            ]);
        }

        return [
            'denomination' => $denomination,
            'is_need_table' => 1,
            'prefill_total' => null,
        ];
    }

    private function userCanEditClosedFloat(): bool
    {
        $user = auth()->user();

        return $user && $user->can('edit_closed_count');
    }

    private function storeEditClosedUnlock($floatAmtId, $userId): void
    {
        session([
            'edit_closed_unlock' => [
                'float_amt_id' => (string) $floatAmtId,
                'user_id' => (string) $userId,
                'auth_id' => (string) auth()->id(),
                'expires_at' => now()->addMinutes(10)->timestamp,
            ],
        ]);
    }

    private function hasValidEditClosedUnlock($floatAmtId, $userId): bool
    {
        $unlock = session('edit_closed_unlock');
        if (!is_array($unlock)) {
            return false;
        }

        return (string) ($unlock['float_amt_id'] ?? '') === (string) $floatAmtId
            && (string) ($unlock['user_id'] ?? '') === (string) $userId
            && (string) ($unlock['auth_id'] ?? '') === (string) auth()->id()
            && (int) ($unlock['expires_at'] ?? 0) >= now()->timestamp;
    }

    private function clearEditClosedUnlock(): void
    {
        session()->forget('edit_closed_unlock');
    }

    public function printAmtCloseBill(Request $request)
    {

        try {
            $taxamount_records = array();
            $date = date("Y/m/d");

            if (isset($request->float_amt_id)) {
                $user_id = $request->id;
                $records = DB::table('float_amount')
                    ->join('users', 'float_amount.user_id', '=', 'users.id')
                    ->leftJoin('users as closed_by_user', 'float_amount.closed_by', '=', 'closed_by_user.id')
                    ->leftJoin('users as edited_by_user', 'float_amount.edited_by', '=', 'edited_by_user.id')
                    ->where('users.id', '=', $user_id)
                    ->where('float_amount.is_closed', '=', 1)
                    ->where('float_amount.float_amt_id', $request->float_amt_id)
                    ->orderBy('float_amount.float_amt_id', 'desc')
                    ->select(
                        'float_amount.*',
                        'users.name',
                        'closed_by_user.name as closed_by_name',
                        'edited_by_user.name as edited_by_name'
                    )
                    ->get();
            } else {
                if (isset($request->date)) {
                    $date = $request->date;
                    $where = "where";
                } else {
                    $date = date("Y/m/d");
                    $where = "whereDate";
                }
                $user_id = $request->id;
                $records = DB::table('float_amount')
                    ->join('users', 'float_amount.user_id', '=', 'users.id')
                    ->leftJoin('users as closed_by_user', 'float_amount.closed_by', '=', 'closed_by_user.id')
                    ->leftJoin('users as edited_by_user', 'float_amount.edited_by', '=', 'edited_by_user.id')
                    ->where('users.id', '=', $user_id)
                    ->where('float_amount.is_closed', '=', 1)
                    ->$where('float_amount.added_date', '=', $date)
                    ->orderBy('float_amount.float_amt_id', 'desc')
                    ->select(
                        'float_amount.*',
                        'users.name',
                        'closed_by_user.name as closed_by_name',
                        'edited_by_user.name as edited_by_name'
                    )
                    ->get();
            }

            if ($records->isEmpty()) {
                return redirect()->route('re.print.bill')->with('error', 'Closed float session not found.');
            }

            $foatAmtDetails = $records[0];
            $payment_updated = date('Y-m-d H:i:s', strtotime($foatAmtDetails->updated_date));
            $payment_addeddate = date('Y-m-d H:i:s', strtotime($foatAmtDetails->added_date));
            $denomination_records = DB::table('close_denomination')
                ->where('float_amt_id', '=', $foatAmtDetails->float_amt_id)
                ->select('*')
                ->orderBy('close_denomination_id', 'asc')
                ->get();

            // Payments taken by this cashier during the open float window (inclusive)
            $booking_records = DB::table('booking_payments')
                ->where('created_by', '=', $user_id)
                ->where('updated_at', '>=', $payment_addeddate)
                ->where('updated_at', '<=', $payment_updated)
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->select('*')
                ->get();

            // Refunds for bookings created by this cashier in the same window
            $refund_records = DB::table('booking_payments')
                ->join('bookings', 'booking_payments.booking_id', '=', 'bookings.booking_id')
                ->where('bookings.created_by', '=', $user_id)
                ->whereIn('bookings.booking_type', ['Refund', 'Refunded'])
                ->where('bookings.updated_at', '>=', $payment_addeddate)
                ->where('bookings.updated_at', '<=', $payment_updated)
                ->whereNull('booking_payments.deleted_by')
                ->whereNull('booking_payments.deleted_at')
                ->select('booking_payments.*', 'bookings.booking_type')
                ->get();

            $date = date("Y/m/d", strtotime($date));

            $taxamount_records_service =  DB::table('booking_services')
                ->join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
                ->where('booking_services.status', '!=', 'Refunded')
                ->where('bookings.refund_invoice_no', '=', null)
                ->where('bookings.created_by', '=', $user_id)
                ->where('bookings.updated_at', '>=', $payment_addeddate)
                ->where('bookings.updated_at', '<=', $payment_updated)
                ->select('booking_services.tax_amount')
                ->get();

            $taxamount_records_item =  DB::table('booking_items')
                ->join('bookings', 'booking_items.booking_id', '=', 'bookings.booking_id')
                ->where('bookings.created_by', '=', $user_id)
                ->where('booking_items.status', '!=', 'Refunded')
                ->where('bookings.updated_at', '>=', $payment_addeddate)
                ->where('bookings.updated_at', '<=', $payment_updated)
                ->select('booking_items.tax_amount')
                ->get();

            $bookingsInWindow = DB::table('bookings')
                ->where('created_by', '=', $user_id)
                ->where('updated_at', '>=', $payment_addeddate)
                ->where('updated_at', '<=', $payment_updated)
                ->whereNotNull('booking_code')
                ->whereNotIn('booking_type', ['Refund', 'Refunded'])
                ->select('booking_id', 'payment_status', 'booking_total', 'payment_total')
                ->get();

            $invoice_count = $bookingsInWindow->where('payment_status', 'completed')->count();
            $partial_invoice_count = $bookingsInWindow->filter(function ($booking) {
                return $booking->payment_status !== 'completed'
                    && (float) $booking->payment_total > 0
                    && (float) $booking->payment_total < (float) $booking->booking_total;
            })->count();
            $transaction_count = $booking_records->count();
            $status_total_count = $bookingsInWindow->count();

            if (count($taxamount_records_item) > 0) {
                foreach ($taxamount_records_item as $item) {
                    array_push($taxamount_records, [
                        'tax_amount' => $item->tax_amount
                    ]);
                }
            }

            if (count($taxamount_records_service) > 0) {
                foreach ($taxamount_records_service as $service) {
                    array_push($taxamount_records, [
                        'tax_amount' => $service->tax_amount
                    ]);
                }
            }

            $current_lang = session('user_session_locale');
            app()->setLocale($current_lang);

            return view('openCloseMoney.amount_close_bill', compact(
                'foatAmtDetails',
                'denomination_records',
                'booking_records',
                'refund_records',
                'taxamount_records',
                'invoice_count',
                'partial_invoice_count',
                'transaction_count',
                'status_total_count'
            ));
        } catch (\Exception $e) {
            Log::error("Failed to print float amount closed bill");
            Log::error($e);
            return redirect()->route('dashboard')->with('error', 'Could not print float amount closed bill');
        }
    }


    public function printXoutBill(Request $request)
    {
        try {
            $user_name = 'All';
            $user_id = (int) $request->user_id;

            if (empty($request->start_date) || empty($request->end_date)) {
                return redirect()->route('x.out')->with('error', 'Start date and end date are required.');
            }

            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();

            if ($end_date->lt($start_date)) {
                return redirect()->route('x.out')->with('error', 'End date must be on or after start date.');
            }

            if ($user_id > 0) {
                $user = DB::table('users')
                    ->where('id', '=', $user_id)
                    ->select('name')
                    ->first();
                if (!$user) {
                    return redirect()->route('x.out')->with('error', 'Selected cashier not found.');
                }
                $user_name = $user->name;
            }

            // Tender payments in range (by payment time + cashier who took payment)
            $paymentsQuery = DB::table('booking_payments')
                ->join('bookings', 'booking_payments.booking_id', '=', 'bookings.booking_id')
                ->whereNotNull('bookings.booking_code')
                ->whereNotIn('bookings.booking_type', ['Refund', 'Refunded'])
                ->where('booking_payments.created_at', '>=', $start_date)
                ->where('booking_payments.created_at', '<=', $end_date)
                ->whereNull('booking_payments.deleted_at')
                ->whereNull('booking_payments.deleted_by');

            if ($user_id > 0) {
                $paymentsQuery->where('booking_payments.created_by', '=', $user_id);
            }

            $booking_records = $paymentsQuery->select('booking_payments.*')->get();

            // Refunds in same window, scoped to cashier who processed the refund payment
            $refundsQuery = DB::table('booking_payments')
                ->join('bookings', 'booking_payments.booking_id', '=', 'bookings.booking_id')
                ->whereIn('bookings.booking_type', ['Refund', 'Refunded'])
                ->whereNotNull('bookings.booking_code')
                ->where('booking_payments.created_at', '>=', $start_date)
                ->where('booking_payments.created_at', '<=', $end_date)
                ->whereNull('booking_payments.deleted_at')
                ->whereNull('booking_payments.deleted_by');

            if ($user_id > 0) {
                $refundsQuery->where('booking_payments.created_by', '=', $user_id);
            }

            $refund_records = $refundsQuery->select('booking_payments.*', 'bookings.booking_type')->get();

            // Status counts: bookings that received payments in this cashier/date window
            $paidBookingIds = $booking_records->pluck('booking_id')->filter()->unique()->values();
            $bookingsInWindow = collect();
            if ($paidBookingIds->isNotEmpty()) {
                $bookingsInWindow = DB::table('bookings')
                    ->whereIn('booking_id', $paidBookingIds->all())
                    ->whereNotNull('booking_code')
                    ->whereNotIn('booking_type', ['Refund', 'Refunded'])
                    ->select('booking_id', 'payment_status', 'booking_total', 'payment_total')
                    ->get();
            }

            $invoice_count = $bookingsInWindow->where('payment_status', 'completed')->count();
            $partial_invoice_count = $bookingsInWindow->filter(function ($booking) {
                return $booking->payment_status !== 'completed'
                    && (float) $booking->payment_total > 0
                    && (float) $booking->payment_total < (float) $booking->booking_total;
            })->count();
            $transaction_count = $booking_records->count();
            $status_total_count = $bookingsInWindow->count();

            // Build net tender the same way the print view does (cash = taken - change)
            $netTender = 0.0;
            foreach ($booking_records as $payment) {
                $mode = strtolower(trim((string) ($payment->payment_mode ?? '')));
                $amountTaken = (float) ($payment->amount_taken ?? 0);
                $amountGiven = (float) ($payment->amount_given ?? 0);
                $netTender += ($mode === 'cash') ? ($amountTaken - $amountGiven) : $amountTaken;
            }
            $netRefunded = (float) $refund_records->sum(function ($refund) {
                return (float) ($refund->amount_given ?? 0);
            });
            $netTaken = max(0, $netTender - $netRefunded);

            $tax = (float) getTaxslab();
            $taxamount_records = $tax > 0
                ? ($netTaken - ($netTaken / (1 + ($tax / 100))))
                : 0;

            $current_lang = session('user_session_locale');
            app()->setLocale($current_lang);

            return view('openCloseMoney.x_out_pdf', compact(
                'start_date',
                'end_date',
                'booking_records',
                'refund_records',
                'taxamount_records',
                'user_name',
                'invoice_count',
                'partial_invoice_count',
                'transaction_count',
                'status_total_count'
            ));
        } catch (\Exception $e) {
            Log::error("Failed to print Xout Bill");
            Log::error($e);
            return redirect()->route('x.out')->with('error', 'Could not print Xout bill');
        }
    }

    public function  reprintCloseAmount(Request $request)
    {
        try {
            $filter_name = $request->input('name', '');
            $filter_is_close = $request->input('is_closed', '');
            $filter_start_date = $request->input('start_date');
            $filter_end_date = $request->input('end_date');

            // Default list to current month when no date range is provided
            if (empty($filter_start_date) && empty($filter_end_date)) {
                $filter_start_date = now()->startOfMonth()->toDateString();
                $filter_end_date = now()->endOfMonth()->toDateString();
            }

            $query = DB::table('float_amount')
                ->join('users', 'float_amount.user_id', '=', 'users.id')
                ->leftJoin('users as edited_by_user', 'float_amount.edited_by', '=', 'edited_by_user.id')
                ->when(!empty($filter_name), function ($q) use ($filter_name) {
                    $q->where('users.name', 'like', $filter_name . '%');
                })
                ->when(!empty($filter_start_date), function ($q) use ($filter_start_date) {
                    $q->whereDate('float_amount.added_date', '>=', $filter_start_date);
                })
                ->when(!empty($filter_end_date), function ($q) use ($filter_end_date) {
                    $q->whereDate('float_amount.added_date', '<=', $filter_end_date);
                })
                ->when($filter_is_close === '0' || $filter_is_close === '1', function ($q) use ($filter_is_close) {
                    $q->where('float_amount.is_closed', (int) $filter_is_close);
                })
                ->select(
                    'float_amount.float_amt_id',
                    'float_amount.user_id',
                    'float_amount.amonut',
                    'float_amount.is_closed',
                    'float_amount.print_bill',
                    'float_amount.added_date',
                    'float_amount.updated_date',
                    'float_amount.edited_at',
                    'float_amount.edit_reason',
                    'users.name',
                    'edited_by_user.name as edited_by_name'
                )
                ->orderBy('float_amount.float_amt_id', 'desc');

            $close_amt_records = $query->paginate(10)->appends(array_filter([
                'name' => $filter_name,
                'start_date' => $filter_start_date,
                'end_date' => $filter_end_date,
                'is_closed' => $filter_is_close,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));

            $current_lang = session('user_session_locale');
            app()->setLocale($current_lang);
            return view('openCloseMoney.amount_reprint_close_bill', compact(
                'close_amt_records',
                'filter_start_date',
                'filter_end_date',
                'filter_is_close',
                'filter_name'
            ));
        } catch (\Exception $e) {
            Log::error("Failed to Re print float amount");
            Log::error($e);
            return redirect()->route('dashboard')->with('error', 'Could not re print float amount');
        }
    }

    public function denominationPermission(Request $request)
    {
        try {
            $validated = $request->validate([
                'is_need' => ['required', 'in:0,1'],
            ]);

            $exists = DB::table('is_denomination_table')->where('id', 1)->exists();
            if (! $exists) {
                DB::table('is_denomination_table')->insert([
                    'id' => 1,
                    'is_need' => (int) $validated['is_need'],
                ]);
            } else {
                DB::table('is_denomination_table')->where('id', 1)
                    ->update(['is_need' => (int) $validated['is_need']]);
            }

            voco_flash()->success(__('Denomination setting updated successfully.'));

            return redirect()->route('denomination');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update denomination permission', ['exception' => $e]);
            voco_flash()->error(__('Could not update denomination permission.'));

            return redirect()->route('denomination');
        }
    }



    public function  Xout(Request $request)
    {
        $user = User::join('float_amount', 'users.id', '=', 'float_amount.user_id')
            ->distinct()
            ->orderBy('users.name')
            ->get(['users.name', 'users.id']);

        $start_date = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', now()->endOfMonth()->toDateString());
        $selected_user_id = (string) $request->input('user_id', '0');

        $current_lang = session('user_session_locale');
        app()->setLocale($current_lang);
        return view('openCloseMoney.x_out_bill', compact(
            'user',
            'start_date',
            'end_date',
            'selected_user_id'
        ));
    }
}
