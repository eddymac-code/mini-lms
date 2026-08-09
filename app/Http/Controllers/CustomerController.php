<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Customers/Index', [
            'customers' => Customer::query()
                ->latest()
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:13'],
            'id_number' => ['required', 'string', 'max:50', 'unique:customers,id_number'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $validated['phone'] = $this->normalizePhone($validated['phone']);

        if (
            Customer::query()->where('phone', $validated['phone'])->exists()
        ) {
            return back()
                ->withErrors(['phone' => 'This phone number is already registered.'])
                ->withInput();
        }

        Customer::create($validated);

        return to_route('customers')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): Response
    {
        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }

    public function edit(Customer $customer): Response
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:13'],
            'id_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('customers', 'id_number')->ignore($customer->id),
            ],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $validated['phone'] = $this->normalizePhone($validated['phone']);

        if (
            Customer::query()
                ->where('phone', $validated['phone'])
                ->where('id', '!=', $customer->id)
                ->exists()
        ) {
            return back()
                ->withErrors(['phone' => 'This phone number is already registered.'])
                ->withInput();
        }

        $customer->update($validated);

        return to_route('customers')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return to_route('customers')
            ->with('success', 'Customer deleted successfully.');
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-()]/', '', $phone);

        if (str_starts_with($phone, '+254')) {
            return '254' . substr($phone, 4);
        }

        if (str_starts_with($phone, '254')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        return '254' . $phone;
    }
}
