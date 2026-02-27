<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $boss;
    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->boss = User::factory()->create(['role' => 'boss']);
        $this->employee = User::factory()->create(['role' => 'employee']);

        Storage::fake('public');
    }

    public function test_boss_can_view_settings_page()
    {
        $response = $this->actingAs($this->boss)->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('settings.index');
    }

    public function test_employee_cannot_view_settings_page()
    {
        $response = $this->actingAs($this->employee)->get(route('settings.index'));

        $response->assertStatus(403);
    }

    public function test_boss_can_update_company_settings()
    {
        $response = $this->actingAs($this->boss)->post(route('settings.general.update'), [
            'company_name' => 'Acme Corp',
            'company_email' => 'contact@acme.test',
            'currency_symbol' => '$',
        ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('company_settings', [
            'group' => 'company',
            'key' => 'company_name',
            'value' => '"Acme Corp"', // JSON encoded array cast value
        ]);

        $this->assertDatabaseHas('company_settings', [
            'group' => 'company',
            'key' => 'currency_symbol',
            'value' => '"$"',
        ]);

        $this->assertEquals('$', CompanySetting::getCurrencySymbol());
        $this->assertEquals('Acme Corp', CompanySetting::getCompanyProfile()['company_name'] ?? null);
    }

    public function test_employee_cannot_update_company_settings()
    {
        $response = $this->actingAs($this->employee)->post(route('settings.general.update'), [
            'company_name' => 'Hacked Corp',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('company_settings', [
            'group' => 'company',
            'key' => 'company_name',
            'value' => '"Hacked Corp"',
        ]);
    }
}
