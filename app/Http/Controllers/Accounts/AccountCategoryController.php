<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountCategory;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        $name = AccountCategory::normalizeName((string) $request->name);
        if ($name === '') {
            return response()->json([
                'message' => __('Category name is required.'),
            ], 422);
        }

        $existing = AccountCategory::findByTypeAndName($request->type, $name);
        if ($existing) {
            return response()->json([
                'message' => __('This category already exists for the selected type.'),
                'category' => $this->payload($existing),
            ], 422);
        }

        $category = AccountCategory::create([
            'name' => $name,
            'type' => $request->type,
            'is_system' => false,
        ]);

        return response()->json([
            'message' => __('Category added.'),
            'category' => $this->payload($category),
        ]);
    }

    public function destroy(AccountCategory $category)
    {
        if ($category->is_system) {
            return response()->json([
                'message' => __('System categories cannot be deleted.'),
            ], 422);
        }

        $used = Account::query()
            ->where('type', $category->type)
            ->where('category', $category->name)
            ->count();

        if ($used > 0) {
            return response()->json([
                'message' => __('This category is used by :count account(s). Change those accounts before deleting it.', [
                    'count' => $used,
                ]),
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => __('Category deleted.'),
        ]);
    }

    private function payload(AccountCategory $category): array
    {
        return [
            'category_id' => (int) $category->category_id,
            'name' => $category->name,
            'type' => $category->type,
            'is_system' => $category->is_system ? 1 : 0,
        ];
    }
}
