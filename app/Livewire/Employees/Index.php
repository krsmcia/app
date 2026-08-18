<?php

namespace App\Livewire\Employees;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showCreateModal = false;

    public array $employees = [];

    public bool $showEditModal = false;
    public ?int $editingUserId = null;
    public string $editName = '';
    public string $editEmail = '';
    public string $editPassword = '';
    public string $editRole = 'staff';
    public array $editDepartmentIds = [];

    public function mount()
    {
        $this->addEmployeeRow();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addEmployeeRow()
    {
        $this->employees[] = [
            'name' => '',
            'email' => '',
            'password' => '',
            'role' => 'staff',
            'department_ids' => [],
        ];
    }

    public function removeEmployeeRow(int $index)
    {
        unset($this->employees[$index]);

        $this->employees = array_values($this->employees);

        if (empty($this->employees)) {
            $this->addEmployeeRow();
        }
    }

    public function createEmployees()
    {
        $this->validate([
            'employees' => ['required', 'array', 'min:1'],

            'employees.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'employees.*.email' => [
                'required',
                'email',
                'max:255',
                'distinct',
            ],

            'employees.*.password' => [
                'required',
                'string',
                'min:8',
            ],

            'employees.*.role' => [
                'required',
                'exists:roles,name',
            ],

            'employees.*.department_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'employees.*.department_ids.*' => [
                'integer',
                'exists:departments,id',
            ],
        ]);

        DB::transaction(function () {
            foreach ($this->employees as $employee) {
                $user = User::create([
                    'name' => $employee['name'],
                    'email' => $employee['email'],
                    'password' => Hash::make($employee['password']),
                ]);

                $user->syncRoles([$employee['role']]);

                $user->departments()->sync(
                    $employee['department_ids']
                );
            }
        });

        $this->resetEmployees();

        $this->showCreateModal = false;

        session()->flash(
            'success',
            'Employees created successfully.'
        );
    }

    protected function resetEmployees()
    {
        $this->employees = [];

        $this->addEmployeeRow();
    }

    public function editEmployee(int $userId)
    {
        $user = User::with(['roles', 'departments'])
            ->findOrFail($userId);

        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            abort(403);
        }

        $this->editingUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editPassword = '';
        $this->editRole = $user->roles->first()?->name ?? 'staff';

        $this->editDepartmentIds = $user->departments
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $this->showEditModal = true;
    }

    public function updateEmployee()
    {
        $user = User::findOrFail($this->editingUserId);
        if ($user->hasAnyRole(['super-admin', 'admin'])) {
            abort(403);
        }
        $this->validate([
            'editName' => [
                'required',
                'string',
                'max:255',
            ],
            'editEmail' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'editPassword' => [
                'nullable',
                'string',
                'min:8',
            ],
            'editDepartmentIds' => [
                'required',
                'array',
                'min:1',
            ],
            'editDepartmentIds.*' => [
                'integer',
                'exists:departments,id',
            ],
            'editRole' => [
                'required',
                'exists:roles,name',
            ],
        ]);
        $user->name = $this->editName;
        $user->email = $this->editEmail;
        if ($this->editPassword !== '') {
            $user->password = Hash::make(
                $this->editPassword
            );
        }
        $user->current_department_id = $this->editDepartmentIds[0];
        $user->save();
        $user->syncRoles([
            $this->editRole,
        ]);
        $user->departments()->sync(
            $this->editDepartmentIds
        );
        $this->reset([
            'editingUserId',
            'editName',
            'editEmail',
            'editPassword',
            'editDepartmentIds',
        ]);
        $this->editRole = 'staff';
        $this->showEditModal = false;
        session()->flash(
            'success',
            'Employee updated successfully.'
        );
    }
    public function render()
    {
        $users = User::with([
            'roles',
            'departments',
        ])
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', [
                    'super-admin',
                    'admin',
                ]);
            })
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('roles', function ($query) use ($search) {
                            $query->where('name', 'like', $search);
                        })
                        ->orWhereHas('departments', function ($query) use ($search) {
                            $query->where('name', 'like', $search);
                        });
                });
            })
            ->paginate(20);
        return view('livewire.employees.index', [
            'users' => $users,
            'departments' => Department::orderBy('name')->get(),
            'roles' => Role::whereNotIn(
                'name',
                ['super-admin', 'admin']
            )->get(),
        ]);
    }
}