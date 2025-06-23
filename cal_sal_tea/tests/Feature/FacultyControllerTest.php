<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Faculty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Feature test for Faculty Management.
 *
 * This test suite covers all CRUD operations for the FacultyController,
 * including validation, edge cases, and authorization.
 */
class FacultyControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * The authenticated user for tests.
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Set up the test environment.
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate a user for all tests in this class.
        // In a real application, this user would have an 'admin' role.
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    //======================================================================
    // 1. INDEX (LIST) TESTS
    //======================================================================

    /**
     * @test
     * Test if an authenticated user can view the faculty list page.
     */
    public function an_authenticated_user_can_view_the_faculty_list()
    {
        $faculty = Faculty::factory()->create(['name' => 'Khoa học Máy tính']);

        $response = $this->get(route('faculties.index'));

        $response->assertStatus(200);
        $response->assertSee('Quản lý Khoa');
        $response->assertSee('Khoa học Máy tính');
    }

    /**
     * @test
     * Test that unauthenticated users are redirected to the login page.
     */
    public function unauthenticated_user_is_redirected_to_login()
    {
        auth()->logout(); // Log out the user set up in setUp()

        $response = $this->get(route('faculties.index'));
        $response->assertRedirect('/login');

        $response = $this->get(route('faculties.create'));
        $response->assertRedirect('/login');
    }

    /**
     * @test
     * Test that the faculty list is paginated correctly.
     */
    public function faculty_list_is_paginated()
    {
        // Create 11 faculties. Default pagination is 10.
        Faculty::factory()->count(11)->create();

        $response = $this->get(route('faculties.index'));

        // The 'faculties' view variable should contain a paginator instance with 10 items.
        $response->assertViewHas('faculties', function ($faculties) {
            return $faculties->count() === 10;
        });
    }

    /**
     * @test
     * Test searching for faculties by name.
     */
    public function user_can_search_faculties_by_name()
    {
        $facultyToFind = Faculty::factory()->create(['name' => 'Khoa Công nghệ Phần mềm']);
        $facultyToHide = Faculty::factory()->create(['name' => 'Khoa Mạng Máy tính']);

        $response = $this->get(route('faculties.index', ['search' => 'Phần mềm']));

        $response->assertSee($facultyToFind->name);
        $response->assertDontSee($facultyToHide->name);
    }

    /**
     * @test
     * Test searching for faculties by abbreviation.
     */
    public function user_can_search_faculties_by_abbreviation()
    {
        $facultyToFind = Faculty::factory()->create(['abbreviation' => 'CNPM']);
        $facultyToHide = Faculty::factory()->create(['abbreviation' => 'MMT']);

        $response = $this->get(route('faculties.index', ['search' => 'CNPM']));

        $response->assertSee($facultyToFind->abbreviation);
        $response->assertDontSee($facultyToHide->abbreviation);
    }

    //======================================================================
    // 2. CREATE (STORE) TESTS
    //======================================================================

    /**
     * @test
     * Test if a user can view the create faculty page.
     */
    public function user_can_view_the_create_faculty_page()
    {
        $response = $this->get(route('faculties.create'));

        $response->assertStatus(200);
        $response->assertSee('Tạo Khoa mới');
    }

    /**
     * @test
     * Test if a user can create a faculty with valid data (happy path).
     */
    public function user_can_create_a_faculty_with_valid_data()
    {
        $facultyData = [
            'name' => 'Khoa Trí tuệ Nhân tạo',
            'abbreviation' => 'AI',
            'description' => 'Mô tả chi tiết về khoa Trí tuệ Nhân tạo.',
        ];

        $response = $this->post(route('faculties.store'), $facultyData);

        $response->assertRedirect(route('faculties.index'));
        $response->assertSessionHas('success', 'Tạo mới khoa thành công.');
        $this->assertDatabaseHas('faculties', ['name' => 'Khoa Trí tuệ Nhân tạo']);
    }

    /**
     * @test
     * @dataProvider invalidStoreDataProvider
     * Test that creating a faculty fails with invalid data.
     */
    public function store_fails_with_invalid_data(array $invalidData, array $expectedErrors)
    {
        // Arrange: create an existing faculty to test 'unique' rule
        $existingFacultyName = 'Existing Faculty';
        Faculty::factory()->create(['name' => $existingFacultyName, 'abbreviation' => 'EF']);
        
        // Đếm số lượng khoa có tên này trước khi thực hiện request
        $countBefore = Faculty::where('name', $existingFacultyName)->count();

        $facultyData = array_merge([
            'name' => 'Test Faculty',
            'abbreviation' => 'TF',
            'description' => 'A valid description.',
        ], $invalidData);

        $response = $this->post(route('faculties.store'), $facultyData);

        $response->assertSessionHasErrors($expectedErrors);

        // Nếu test case là về 'unique name', hãy kiểm tra rằng không có bản ghi mới nào được tạo
        if (isset($invalidData['name']) && $invalidData['name'] === $existingFacultyName) {
            $this->assertDatabaseCount('faculties', $countBefore);
        }
    }

    //======================================================================
    // 3. UPDATE TESTS
    //======================================================================

    /**
     * @test
     * Test if a user can view the edit faculty page.
     */
    public function user_can_view_the_edit_faculty_page()
    {
        $faculty = Faculty::factory()->create();

        $response = $this->get(route('faculties.edit', $faculty));

        $response->assertStatus(200);
        $response->assertSee('Chỉnh sửa Khoa');
        $response->assertSee($faculty->name); // Check if existing data is displayed
    }

    /**
     * @test
     * Test if a user can update a faculty with valid data (happy path).
     */
    public function user_can_update_a_faculty_with_valid_data()
    {
        $faculty = Faculty::factory()->create();
        $updatedData = [
            'name' => 'Tên khoa đã cập nhật',
            'abbreviation' => 'TKDC',
            'description' => 'Mô tả đã được cập nhật.',
        ];

        $response = $this->put(route('faculties.update', $faculty), $updatedData);

        $response->assertRedirect(route('faculties.index'));
        $response->assertSessionHas('success', 'Cập nhật khoa thành công.');
        $this->assertDatabaseHas('faculties', ['id' => $faculty->id, 'name' => 'Tên khoa đã cập nhật']);
    }

    /**
     * @test
     * @dataProvider invalidUpdateDataProvider
     * Test that updating a faculty fails with invalid data.
     */
    public function update_fails_with_invalid_data(array $invalidData, array $expectedErrors)
    {
        // Arrange: create faculties for testing unique constraints
        $facultyToUpdate = Faculty::factory()->create(['name' => 'Original Name', 'abbreviation' => 'ON']);
        Faculty::factory()->create(['name' => 'Existing Name', 'abbreviation' => 'EN']);

        $updateData = array_merge([
            'name' => 'A new valid name',
            'abbreviation' => 'AVN',
            'description' => 'A valid description.',
        ], $invalidData);

        $response = $this->put(route('faculties.update', $facultyToUpdate), $updateData);

        $response->assertSessionHasErrors($expectedErrors);
        // Ensure the original data was not changed
        $this->assertDatabaseHas('faculties', ['id' => $facultyToUpdate->id, 'name' => 'Original Name']);
    }

   /**
     * @test
     * Test that updating a faculty with its own existing unique data does not fail.
     */
    public function update_succeeds_with_its_own_unique_data()
    {
        $faculty = Faculty::factory()->create([
            'name' => 'Khoa hoc May tinh' // Dùng một tên chắc chắn hợp lệ với regex
        ]);

        $data = [
            'name' => $faculty->name, // Same name
            'abbreviation' => $faculty->abbreviation, // Same abbreviation
            'description' => 'New description',
        ];

        $response = $this->put(route('faculties.update', $faculty), $data);

        $response->assertSessionHasNoErrors(); // Lần này sẽ PASS
        $response->assertRedirect(route('faculties.index'));
        $this->assertDatabaseHas('faculties', ['id' => $faculty->id, 'description' => 'New description']);
    }


    //======================================================================
    // 4. DELETE TESTS
    //======================================================================

    /**
     * @test
     * Test if a user can delete a faculty.
     */
    public function user_can_delete_a_faculty()
    {
        $faculty = Faculty::factory()->create();

        $response = $this->delete(route('faculties.destroy', $faculty));

        $response->assertRedirect(route('faculties.index'));
        $response->assertSessionHas('success', 'Đã xóa khoa thành công.');
        $this->assertDatabaseMissing('faculties', ['id' => $faculty->id]);
    }

    /**
     * @test
     * Test that an attempt to update a non-existent faculty returns a 404.
     */
    public function updating_a_non_existent_faculty_returns_404()
    {
        $response = $this->put(route('faculties.update', 9999), [
            'name' => 'Non-existent',
            'abbreviation' => 'NE',
            'description' => 'This should fail.'
        ]);
        $response->assertStatus(404);
    }
    
    /**
     * @test
     * Test that an attempt to delete a non-existent faculty returns a 404.
     */
    public function deleting_a_non_existent_faculty_returns_404()
    {
        $response = $this->delete(route('faculties.destroy', 9999));
        $response->assertStatus(404);
    }
    
    //======================================================================
    // 5. DATA PROVIDERS FOR VALIDATION
    //======================================================================

    /**
     * Data provider for invalid store (create) requests.
     * @return array
     */
    public static function invalidStoreDataProvider(): array
    {
        return [
            'name is null' => [['name' => ''], ['name']],
            'name is not unique' => [['name' => 'Existing Faculty'], ['name']],
            'name is too long' => [['name' => str_repeat('a', 256)], ['name']],
            'name contains invalid characters' => [['name' => 'Faculty with$ymbols'], ['name']],
            'abbreviation is null' => [['abbreviation' => ''], ['abbreviation']],
            'abbreviation is not unique' => [['abbreviation' => 'EF'], ['abbreviation']],
            'abbreviation is too long' => [['abbreviation' => str_repeat('a', 21)], ['abbreviation']],
            'description is null' => [['description' => ''], ['description']],
        ];
    }
    
    /**
     * Data provider for invalid update requests.
     * @return array
     */
    public static function invalidUpdateDataProvider(): array
    {
        return [
            'name is null' => [['name' => ''], ['name']],
            'name is not unique' => [['name' => 'Existing Name'], ['name']],
            'name is too long' => [['name' => str_repeat('a', 256)], ['name']],
            'name contains invalid characters' => [['name' => 'Faculty with$ymbols'], ['name']],
            'abbreviation is null' => [['abbreviation' => ''], ['abbreviation']],
            'abbreviation is not unique' => [['abbreviation' => 'EN'], ['abbreviation']],
            'abbreviation is too long' => [['abbreviation' => str_repeat('a', 21)], ['abbreviation']],
            'description is null' => [['description' => ''], ['description']],
        ];
    }
}