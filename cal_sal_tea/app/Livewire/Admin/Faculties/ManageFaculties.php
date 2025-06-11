<?php

namespace App\Http\Livewire\Admin\Faculties;

use App\Models\Faculty;
use Livewire\Component;
use Livewire\WithPagination;

class ManageFaculties extends Component
{
    use WithPagination;

    // Modal properties
    public $showModal = false;
    public $isEditMode = false;

    // Form properties
    public $facultyId;
    public $name;
    public $abbreviation;
    public $description;

    // Search property
    public $search = '';

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:20|unique:faculties,abbreviation,' . $this->facultyId,
            'description' => 'nullable|string',
        ];
    }
    
    // Reset form fields
    public function resetForm()
    {
        $this->reset(['facultyId', 'name', 'abbreviation', 'description', 'isEditMode']);
    }

    // Show modal for creating a new faculty
    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }
    
    // Show modal for editing an existing faculty
    public function edit(Faculty $faculty)
    {
        $this->facultyId = $faculty->id;
        $this->name = $faculty->name;
        $this->abbreviation = $faculty->abbreviation;
        $this->description = $faculty->description;
        $this->isEditMode = true;
        $this->showModal = true;
    }
    
    // Save or update faculty
    public function save()
    {
        $this->validate();

        Faculty::updateOrCreate(
            ['id' => $this->facultyId],
            [
                'name' => $this->name,
                'abbreviation' => $this->abbreviation,
                'description' => $this->description,
            ]
        );

        session()->flash('message', 'Khoa đã được lưu thành công.');
        $this->showModal = false;
        $this->resetForm();
    }
    
    // Delete faculty
    public function delete(Faculty $faculty)
    {
        // Add checks here if the faculty has related teachers before deleting
        $faculty->delete();
        session()->flash('message', 'Khoa đã được xóa thành công.');
    }

    // Reset search when the search property is updated
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    // Render the component
    public function render()
    {
        $faculties = Faculty::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('abbreviation', 'like', '%' . $this->search . '%')
            ->paginate(10);
            
        return view('livewire.admin.faculties.manage-faculties', [
        'faculties' => $faculties
    ]);
}
}
