<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Faculty;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FacultyManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_can_login_and_create_faculty()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $facultyName = 'Khoa học và Kỹ thuật Máy tính';

            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password')
                    ->press('Sign In')
                    ->assertPathIs('/dashboard')
                    ->visit('/admin/faculties')
                    ->assertSee('Quản lý Khoa')
                    ->click('a[href$="/admin/faculties/create"]')
                    ->assertPathIs('/admin/faculties/create')
                    ->type('name', $facultyName)
                    ->type('abbreviation', 'KH&KTTT')
                    ->type('description', 'Mô tả cho khoa mới.')
                    ->press('Lưu')
                    ->assertPathIs('/admin/faculties')
                    ->assertSee($facultyName);
        });
    }

    public function test_admin_can_edit_faculty()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $faculty = Faculty::factory()->create([
            'name' => 'Khoa CNTT',
            'abbreviation' => 'CNTT',
            'description' => 'Mô tả ban đầu',
        ]);

        $this->browse(function (Browser $browser) use ($user, $faculty) {
            $newName = 'Khoa Công nghệ Thông tin';

            $browser->loginAs($user)
                    ->visit('/admin/faculties')
                    ->assertSee($faculty->name)
                    ->click('@edit-faculty-' . $faculty->id) // Giả sử bạn có button edit có Dusk selector
                    ->assertPathIs('/admin/faculties/' . $faculty->id . '/edit')
                    ->type('name', $newName)
                    ->type('abbreviation', 'CN-TT')
                    ->type('description', 'Mô tả đã sửa')
                    ->press('Cập nhật')
                    ->assertPathIs('/admin/faculties')
                    ->assertSee($newName);
        });
    }

    public function test_admin_can_delete_faculty()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
        ]);

        $faculty = Faculty::factory()->create([
            'name' => 'Khoa Tạm Xóa',
            'abbreviation' => 'KT-X',
        ]);

        $this->browse(function (Browser $browser) use ($user, $faculty) {
            $browser->loginAs($user)
                    ->visit('/admin/faculties')
                    ->assertSee($faculty->name)
                    ->click('@delete-faculty-' . $faculty->id) // Giả sử có Dusk selector cho nút xóa
                    ->whenAvailable('.modal-confirm', function ($modal) {
                        $modal->press('Xác nhận'); // Hoặc nút xác nhận trong modal
                    })
                    ->pause(1000)
                    ->assertDontSee($faculty->name);
        });
    }
}
