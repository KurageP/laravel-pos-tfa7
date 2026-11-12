<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::orderBy('id')->get();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create', ['customer' => new Customer]);
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // The original TFA6 schema stores an omitted phone as an empty string.
        $data['phone'] = $data['phone'] ?? '';
        Customer::create($data);

        return to_route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = $request->validated();
        $data['phone'] = $data['phone'] ?? '';
        $customer->update($data);

        return to_route('customers.index')->with('success', 'Customer updated successfully.');
    }
}
