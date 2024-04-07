<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Sample;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

/**
 * @see \App\Http\Controllers\SampleController
 */
class SampleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $admin;
    private $editor;
    private $viewer;

    // セットアップ
    protected function setUp(): void
    {
        parent::setUp();

        // ユーザにリレーションを追加している場合は適宜シーダーを追記してください。
        // $this->seed('MasterDepartmentSeeder');

        $this->admin = User::factory()->create();
    }

    /**
     * @test
     */
    public function 一覧_表示できる()
    {
        $samples = Sample::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('samplesChainCase.index'));

        $response->assertOk();
        $response->assertViewIs('samplesChainCase.index');
        $response->assertViewHas('samples');
    }


    /**
     * @test
     */
    public function 新規登録_表示できる()
    {
        $this->be($this->admin);

        $response = $this->get(route('samplesChainCase.create'));

        $response->assertOk();
        $response->assertViewIs('samplesChainCase.create');
    }

    /**
     * @test
     */
    public function 新規登録_保存できる()
    {
        $this->be($this->admin);

        $data = [
            %%COLUMNS%%
        ];

        $response = $this->post(route('samplesChainCase.store'), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('samplesChainCase.index'));

        $this->assertDatabaseHas(Sample::class, $data);
    }


    /**
     * @test
     */
    public function 詳細_表示できる()
    {
        $sample = Sample::factory()->create();

        $this->be($this->admin);

        $response = $this->get(route('samplesChainCase.show', $sample));

        $response->assertOk();
        $response->assertViewIs('samplesChainCase.show');
        $response->assertViewHas('sample');
    }

    /**
     * @test
     */
    public function 編集_表示できる()
    {
        $sample = Sample::factory()->create();

        $this->be($this->admin);

        $response = $this->get(route('samplesChainCase.edit', $sample));

        $response->assertOk();
        $response->assertViewIs('samplesChainCase.edit');
        $response->assertViewHas('sample');
    }

    /**
     * @test
     */
    public function 編集_保存できる()
    {
        $sample = Sample::factory()->create();

        $this->be($this->admin);

        $data = [
            %%COLUMNS%%
        ];

        $response = $this->put(route('samplesChainCase.update', $sample), $data);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('samplesChainCase.index'));

        $sample->refresh();
        $this->assertDatabaseHas(Sample::class, [
            %%ASSERT_COLUMNS%%
        ]);
    }


    /**
     * @test
     */
    public function 削除_削除できる()
    {
        $sample = Sample::factory()->create();

        $this->be($this->admin);

        $response = $this->delete(route('samplesChainCase.destroy', $sample));

        $response->assertSessionHasNoErrors();

        $response->assertRedirect(route('samplesChainCase.index'));
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($sample))) {
            $this->assertNotNull($sample->fresh()->deleted_at);
        } else {
            $this->assertModelMissing($sample);
        }
    }
}
