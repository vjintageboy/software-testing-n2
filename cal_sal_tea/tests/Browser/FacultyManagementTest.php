<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Faculty;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Test;

class FacultyManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['email' => 'admin@example.com']);
    }

    #[Test]
    public function admin_can_successfully_create_faculty(): void
    {
        $this->browse(function (Browser $browser) {
            $facultyName = 'Khoa học và Kỹ thuật Máy tính';
            $browser->loginAs($this->adminUser)
                ->visit(route('faculties.create'))
                ->type('name', $facultyName)
                ->type('abbreviation', 'KH&KTTT')
                ->type('description', 'Mô tả cho khoa mới.')
                ->click('@submit-create-faculty')
                ->assertPathIs('/admin/faculties')
                ->assertSee('Tạo mới khoa thành công.')
                ->assertSee($facultyName);
        });
    }

    #[Test]
    public function create_faculty_fails_with_empty_data(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                ->visit(route('faculties.create'))
                ->click('@submit-create-faculty')
                ->assertPathIs('/admin/faculties/create')
                // SỬA LỖI 1: Assert đúng thông báo lỗi "required"
                ->assertSee('The name field is required.')
                ->assertSee('The abbreviation field is required.')
                ->assertSee('The description field is required.');
        });
    }

    #[Test]
    public function create_faculty_fails_with_duplicate_name(): void
    {
        Faculty::factory()->create(['name' => 'Khoa Công nghệ Thông tin']);
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                ->visit(route('faculties.create'))
                ->type('name', 'Khoa Công nghệ Thông tin')
                ->type('abbreviation', 'CNTT-NEW')
                ->type('description', 'Một mô tả.')
                ->click('@submit-create-faculty')
                ->waitForText('The name has already been taken.', 5)
                ->assertPathIs('/admin/faculties/create');
        });
    }

    #[Test]
    public function admin_can_successfully_edit_faculty(): void
    {
        $faculty = Faculty::factory()->create();
        $this->browse(function (Browser $browser) use ($faculty) {
            $newName = 'Khoa Công nghệ Thông tin Đã Sửa';
            $browser->loginAs($this->adminUser)
                ->visit(route('faculties.index'))
                ->click('@edit-faculty-' . $faculty->id)
                ->assertPathIs('/admin/faculties/' . $faculty->id . '/edit')
                ->type('name', $newName)
                ->type('description', 'Mô tả đã được cập nhật.')
                ->click('@submit-update-faculty')
                ->assertPathIs('/admin/faculties')
                ->assertSee('Cập nhật khoa thành công.')
                ->assertSee($newName);
        });
    }

    #[Test]
    public function admin_can_successfully_delete_faculty(): void
    {
        $facultyToDelete = Faculty::factory()->create(['name' => 'Khoa Sẽ Bị Xóa']);
        $this->browse(function (Browser $browser) use ($facultyToDelete) {
            $browser->loginAs($this->adminUser)
                ->visit(route('faculties.index'))
                ->click('@delete-faculty-' . $facultyToDelete->id)
                ->acceptDialog()
                ->waitUntilMissingText($facultyToDelete->name, 5)
                ->assertSee('Đã xóa khoa thành công.')
                ->assertDontSee($facultyToDelete->name);
        });
    }

    #[Test]
    public function search_functionality_works_correctly(): void
    {
        Faculty::factory()->create(['name' => 'Khoa Kỹ thuật Phần mềm']);
        Faculty::factory()->create(['name' => 'Khoa Mạng máy tính']);
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                ->visit(route('faculties.index'))
                ->type('search', 'Phần mềm')
                ->press('Tìm kiếm')
                ->waitForText('Khoa Kỹ thuật Phần mềm', 5)
                ->assertSee('Khoa Kỹ thuật Phần mềm')
                ->assertDontSee('Khoa Mạng máy tính');
        });
    }

    #[Test]
    public function pagination_works_correctly(): void
    {
        $oldestFaculty = Faculty::factory()->create([
            'name' => 'Khoa Lâu Đời Nhất',
            'created_at' => now()->subDay(), // Đặt ngày tạo là hôm qua
        ]);
        // Tạo thêm 10 khoa mới hơn với ngày tạo là hôm nay
        Faculty::factory()->count(10)->create();

        $this->browse(function (Browser $browser) use ($oldestFaculty) {
            $browser->loginAs($this->adminUser)
                ->visit(route('faculties.index'))
                ->assertPresent('.pagination')
                ->click('.pagination a[href$="page=2"]')
                ->waitUntil('window.location.search.includes("page=2")')
                ->assertSee($oldestFaculty->name);
        });
    }
}