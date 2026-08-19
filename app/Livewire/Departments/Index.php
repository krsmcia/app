<?php

namespace App\Livewire\Departments;

use App\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showCreateModal = false;
    public bool $showEditModal = false;

    public string $name = '';
    public string $code = '';
    public bool $isActive = true;

    public ?int $editingDepartmentId = null;
    public string $editName = '';
    public string $editCode = '';
    public bool $editIsActive = true;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function createDepartment()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:departments,name',
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:departments,code',
            ],
            'isActive' => [
                'boolean',
            ],
        ]);

        Department::create([
            'name' => $this->name,
            'code' => $this->code,
            'is_active' => $this->isActive,
        ]);

        $this->resetCreateForm();

        $this->showCreateModal = false;

        session()->flash(
            'success',
            'Department created successfully.'
        );
    }

    public function editDepartment(int $departmentId)
    {
        $department = Department::findOrFail($departmentId);

        $this->editingDepartmentId = $department->id;
        $this->editName = $department->name;
        $this->editCode = $department->code;
        $this->editIsActive = $department->is_active;

        $this->showEditModal = true;
    }

    public function updateDepartment()
    {
        $department = Department::findOrFail(
            $this->editingDepartmentId
        );

        $this->validate([
            'editName' => [
                'required',
                'string',
                'max:255',
                'unique:departments,name,' . $department->id,
            ],
            'editCode' => [
                'required',
                'string',
                'max:255',
                'unique:departments,code,' . $department->id,
            ],
            'editIsActive' => [
                'boolean',
            ],
        ]);

        $department->update([
            'name' => $this->editName,
            'code' => $this->editCode,
            'is_active' => $this->editIsActive,
        ]);

        $this->reset([
            'editingDepartmentId',
            'editName',
            'editCode',
        ]);

        $this->editIsActive = true;

        $this->showEditModal = false;

        session()->flash(
            'success',
            'Department updated successfully.'
        );
    }

    public function deleteDepartment(int $departmentId)
    {
        $department = Department::findOrFail($departmentId);

        if ($department->users()->exists()) {
            session()->flash(
                'error',
                'This department cannot be deleted because employees are assigned to it.'
            );

            return;
        }

        $department->delete();

        session()->flash(
            'success',
            'Department deleted successfully.'
        );
    }

    protected function resetCreateForm()
    {
        $this->reset([
            'name',
            'code',
        ]);

        $this->isActive = true;
    }

    public function render()
    {
        $departments = Department::query()
            ->withCount('users')
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';

                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('code', 'like', $search);
                });
            })
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.departments.index', [
            'departments' => $departments,
        ]);
    }
}