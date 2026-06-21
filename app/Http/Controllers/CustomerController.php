<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\Customer\CustomerMembershipService;
use App\Support\CustomerPhone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerMembershipService $membershipService,
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $customers = Customer::query()
            ->orderByDesc('loyalty_points')
            ->orderBy('name')
            ->get();

        return view('management.customers', [
            'customers' => $customers,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        try {
            $customer = $this->membershipService->createMember(
                $validated['name'],
                $validated['phone']
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->with('lastAct', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Member berhasil didaftarkan.',
                'customer' => $this->formatCustomer($customer),
            ], 201);
        }

        return redirect()
            ->route('customers.index')
            ->with('lastAct', 'Member '.$customer->name.' berhasil didaftarkan.');
    }

    public function lookup(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $customer = $this->membershipService->findByPhone($request->input('phone'));

        if (! $customer) {
            return response()->json([
                'found' => false,
                'message' => 'Member dengan nomor tersebut tidak ditemukan.',
            ]);
        }

        return response()->json([
            'found' => true,
            'customer' => $this->formatCustomer($customer),
        ]);
    }

    private function formatCustomer(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => CustomerPhone::display($customer->phone),
            'loyalty_points' => (int) $customer->loyalty_points,
            'loyalty_discount_percent' => $this->membershipService->calculateDiscountPercent((int) $customer->loyalty_points),
        ];
    }
}
