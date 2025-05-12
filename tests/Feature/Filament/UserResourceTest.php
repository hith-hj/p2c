<?php

declare(strict_types=1);

use function Pest\Laravel\{get, post, put, delete};
use App\Models\V1\Admin;
use App\Filament\Resources\UserResource;
use App\Models\V1\User;

beforeEach(function () {
    $this->actingAs(Admin::factory()->create(), 'admin');
});

describe('User Resource Test', function () {
    it('can render page', function () {
        $this->get(UserResource::getUrl('index'))->assertSuccessful();
    });

    // it('can create a user', function () {
    //     $data = User::factory()->make()->toArray();

    //     $response = post(UserResource::getUrl('create'), $data);

    //     $response->assertCreated();
    //     expect(User::where($data)->exists())->toBeTrue();
    // });

    // it('can update a user', function () {
    //     $user = User::factory()->create();
    //     $updatedData = ['email' => 'updated@example.com'];

    //     $response = put(UserResource::getUrl('edit', ['record' => $user]), $updatedData);

    //     $response->assertOk();
    //     expect($user->refresh()->email)->toBe('updated@example.com');
    // });

    // it('can delete a user', function () {
    //     $user = User::factory()->create();

    //     $response = delete(UserResource::getUrl('delete', ['record' => $user]));

    //     $response->assertNoContent();
    //     expect(User::find($user->id))->toBeNull();
    // });
});
