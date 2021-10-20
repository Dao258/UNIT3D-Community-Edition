<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Torrent;
use App\Models\User;
use Database\Seeders\GroupsTableSeeder;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\BookmarkController
 */
class BookmarkControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(GroupsTableSeeder::class);
    }

    /** @test */
    public function destroy_returns_an_ok_response()
    {
        $user = User::factory()->create();

        $torrent = Torrent::factory()->create([
            'user_id' => $user->id,
            'status'  => 1,
        ]);

        Livewire::test(Bookmark::class)
            ->set('torrent', $torrent)
            ->set('user', $user)
            ->call('destroy');

        $this->assertFalse(Bookmark::where('torrent_id', '=', $torrent->id)->where('user_id', '=', $user->id)->exists());
    }

    /** @test */
    public function index_returns_an_ok_response()
    {
        $user = User::factory()->create();

        Bookmark::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('torrents'));

        $response->assertOk()
            ->assertViewIs('torrent.index')
            ->assertViewHas('user');
    }

    /** @test */
    public function store_returns_an_ok_response()
    {
        $user = User::factory()->create();

        $torrent = Torrent::factory()->create([
            'user_id' => $user->id,
            'status'  => 1,
        ]);

        Livewire::test(Bookmark::class)
            ->set('torrent', $torrent)
            ->set('user', $user)
            ->call('store');

        $this->assertTrue(Bookmark::where('torrent_id', '=', $torrent->id)->where('user_id', '=', $user->id)->exists());
    }
}
