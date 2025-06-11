<?php

namespace App\Livewire\Admin\Faculties;

use Livewire\Component;
use App\Models\Faculty;
use Livewire\Attributes\Layout;
use Livewire\WithPagination; // Import a trait để phân trang

#[Layout('layouts.app')]
class ManageFaculties extends Component
{
    use WithPagination; // Sử dụng trait phân trang

    // Các thuộc tính cho modal và form
    public $showModal = false;
    public $isEditMode = false;
    public $faculty_id;
    public $name, $abbreviation, $description;

    // Quy tắc validation cho các trường trong form
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            // unique:faculties,abbreviation,{id} để bỏ qua check unique cho chính record đang sửa
            'abbreviation' => 'required|string|max:20|unique:faculties,abbreviation,' . $this->faculty_id,
            'description' => 'nullable|string',
        ];
    }
    
    /**
     * Hiển thị modal ở chế độ tạo mới.
     */
    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    /**
     * Hiển thị modal ở chế độ chỉnh sửa và tải dữ liệu.
     * @param int $id
     */
    public function edit($id)
    {
        $faculty = Faculty::findOrFail($id);
        $this->faculty_id = $id;
        $this->name = $faculty->name;
        $this->abbreviation = $faculty->abbreviation;
        $this->description = $faculty->description;
        $this->isEditMode = true;
        $this->showModal = true;
    }

    /**
     * Lưu khoa mới hoặc cập nhật khoa đã có.
     */
    public function store()
    {
        $this->validate();

        Faculty::updateOrCreate(['id' => $this->faculty_id], [
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'description' => $this->description,
        ]);

        session()->flash('message', 
            $this->isEditMode ? 'Cập nhật khoa thành công.' : 'Tạo khoa mới thành công.');

        $this->closeModal();
    }
    
    /**
     * Xóa một khoa.
     * @param int $id
     */
    public function delete($id)
    {
        try {
            Faculty::find($id)->delete();
            session()->flash('message', 'Đã xóa khoa.');
        } catch (\Exception $e) {
            session()->flash('error', 'Không thể xóa khoa này. Có thể do có dữ liệu liên quan.');
        }
    }

    /**
     * Đóng modal và reset các trường.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }
    
    /**
     * Reset các trường nhập liệu của form.
     */
    private function resetInputFields()
    {
        $this->reset(['name', 'abbreviation', 'description', 'faculty_id']);
        $this->isEditMode = false;
    }

    /**
     * Render component và truyền dữ liệu đã phân trang ra view.
     */
    public function render()
    {
        return view('livewire.admin.faculties.manage-faculties', [
            'faculties' => Faculty::orderBy('id', 'desc')->paginate(10) // Sử dụng paginate() để phân trang
        ]);
    }
}

