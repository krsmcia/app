<?php

namespace App\Livewire\Procurements;

use App\Models\Vendor;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Vendors extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public ?int $editingVendorId = null;

    public string $code = '';

    public string $name = '';

    public string $legalName = '';

    public string $type = 'supplier';

    public string $contactPerson = '';

    public string $email = '';

    public string $phone = '';

    public string $website = '';

    public string $address = '';

    public string $taxNumber = '';

    public string $paymentTerms = '';

    public string $description = '';

    public bool $isActive = true;

    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    protected function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vendors', 'code')->ignore($this->editingVendorId),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'legalName' => [
                'nullable',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'in:supplier,customer',
            ],

            'contactPerson' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:255',
            ],

            'website' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'taxNumber' => [
                'nullable',
                'string',
                'max:100',
            ],

            'paymentTerms' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'isActive' => [
                'boolean',
            ],
        ];
    }


    public function updatedSearch(): void
    {
        $this->resetPage();
    }


    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }


    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): void
    {
        $this->resetForm();

        $this->code = $this->generateCode();

        $this->type = 'supplier';

        $this->isActive = true;

        $this->resetValidation();

        $this->showCreateModal = true;
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(int $id): void
    {
        $vendor = Vendor::findOrFail($id);

        $this->editingVendorId = $vendor->id;

        $this->code = $vendor->code;

        $this->name = $vendor->name;

        $this->legalName = $vendor->legal_name ?? '';

        $this->type = $vendor->type;

        $this->contactPerson = $vendor->contact_person ?? '';

        $this->email = $vendor->email ?? '';

        $this->phone = $vendor->phone ?? '';

        $this->website = $vendor->website ?? '';

        $this->address = $vendor->address ?? '';

        $this->taxNumber = $vendor->tax_number ?? '';

        $this->paymentTerms = $vendor->payment_terms ?? '';

        $this->description = $vendor->description ?? '';

        $this->isActive = (bool) $vendor->is_active;

        $this->resetValidation();

        $this->showEditModal = true;
    }


    /*
    |--------------------------------------------------------------------------
    | Create / Update
    |--------------------------------------------------------------------------
    */

    public function createVendor(): void
    {
        $this->editingVendorId = null;

        $validated = $this->validate();

        Vendor::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'legal_name' => $validated['legalName'],
            'type' => $validated['type'],
            'contact_person' => $validated['contactPerson'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'website' => $validated['website'],
            'address' => $validated['address'],
            'tax_number' => $validated['taxNumber'],
            'payment_terms' => $validated['paymentTerms'],
            'description' => $validated['description'],
            'is_active' => $validated['isActive'],
        ]);

        $this->showCreateModal = false;

        $this->resetForm();

        $this->resetValidation();

        session()->flash(
            'success',
            'Vendor created successfully.'
        );
    }


    public function updateVendor(): void
    {
        $vendor = Vendor::findOrFail($this->editingVendorId);

        $validated = $this->validate();

        $vendor->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'legal_name' => $validated['legalName'],
            'type' => $validated['type'],
            'contact_person' => $validated['contactPerson'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'website' => $validated['website'],
            'address' => $validated['address'],
            'tax_number' => $validated['taxNumber'],
            'payment_terms' => $validated['paymentTerms'],
            'description' => $validated['description'],
            'is_active' => $validated['isActive'],
        ]);

        $this->showEditModal = false;

        $this->resetForm();

        $this->resetValidation();

        session()->flash(
            'success',
            'Vendor updated successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function deleteVendor(int $id): void
    {
        $vendor = Vendor::findOrFail($id);

        $vendor->delete();

        session()->flash(
            'success',
            'Vendor deleted successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;

        $this->resetForm();

        $this->resetValidation();
    }


    public function closeEditModal(): void
    {
        $this->showEditModal = false;

        $this->resetForm();

        $this->resetValidation();
    }


    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    private function resetForm(): void
    {
        $this->editingVendorId = null;

        $this->code = '';

        $this->name = '';

        $this->legalName = '';

        $this->type = 'supplier';

        $this->contactPerson = '';

        $this->email = '';

        $this->phone = '';

        $this->website = '';

        $this->address = '';

        $this->taxNumber = '';

        $this->paymentTerms = '';

        $this->description = '';

        $this->isActive = true;
    }


    /*
    |--------------------------------------------------------------------------
    | Code
    |--------------------------------------------------------------------------
    */

    private function generateCode(): string
    {
        $lastCode = Vendor::query()
            ->where('code', 'like', 'V%')
            ->orderByDesc('id')
            ->value('code');

        if (! $lastCode) {
            return 'V001';
        }

        $number = (int) preg_replace('/\D/', '', $lastCode);

        return 'V' . str_pad(
            $number + 1,
            3,
            '0',
            STR_PAD_LEFT
        );
    }
    public function sortBy(string $field): void
    {
        $allowedFields = [
            'name',
            'code',
            'type',
            'is_active',
        ];
        if (! in_array($field, $allowedFields, true)) {
            return;
        }
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc'
                ? 'desc'
                : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $vendors = Vendor::query()
            ->when($this->search !== '', function ($query) {

                $query->where(function ($query) {

                    $query
                        ->where('code', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%")
                        ->orWhere('legal_name', 'like', "%{$this->search}%")
                        ->orWhere('contact_person', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('address', 'like', "%{$this->search}%");

                });

            })
            ->when($this->typeFilter !== '', function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {

                $query->where(
                    'is_active',
                    $this->statusFilter === 'active'
                );

            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view(
            'livewire.procurements.vendors',
            [
                'vendors' => $vendors,
            ]
        );
    }
}