<?php

declare(strict_types=1);

use App\Services\ProducerServices;
use App\Models\V1\Producer;
use App\Models\V1\User;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->producerServices = new ProducerServices();
});

describe('Producer Service', function () {

    it('retrieves all producers', function () {
        Producer::factory(2)->create();
        $result = $this->producerServices->all();
        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount(2);
    });

    it('throw exception when no producers found', function () {
        $this->producerServices->all();
    })->throws(Exception::class);

    it('paginates producers', function () {
        $mockRequest = Mockery::mock('Illuminate\Http\Request');
        $mockRequest->shouldReceive('filled')
            ->with('perPage')
            ->andReturnFalse();

        Producer::factory(15)->create();
        $count = 4;
        $result = $this->producerServices->paginate($mockRequest, $count);

        expect($result)
            ->toBeInstanceOf('Illuminate\Pagination\LengthAwarePaginator')
            ->toHaveCount($count);
    });

    it('throw exception when no producers found for pagination', function () {
        $mockRequest = Mockery::mock('Illuminate\Http\Request');
        $mockRequest->shouldReceive('filled')
            ->with('perPage')
            ->andReturnFalse();
        $count = 4;
        $this->producerServices->paginate($mockRequest, $count);
    })->throws(Exception::class);

    it('retrieves a producer by ID', function () {
        $producer = Producer::factory()->create();
        $result = $this->producerServices->find($producer->id);
        expect($result)->toBeInstanceOf(Producer::class)->id->toBe($producer->id);
    });

    it('fail to retrieves a producer by invalid  ID', function () {
        $result = $this->producerServices->find(999);
    })->throws(Exception::class);

    it('retrieves a producer by user', function () {
        $user = User::factory()->create(['role' => 'producer']);
        $result = $this->producerServices->get($user->id);
        expect($result)->toBeInstanceOf(Producer::class)->id->toBe($user->badge->id);
        expect($result)->toBeInstanceOf(Producer::class)->user_id->toBe($user->id);
    });

    it('fail to retrieves a producer by invalid user', function () {
        $result = $this->producerServices->get(999);
    })->throws(Exception::class);

    it('creates a new producer', function () {
        $user = User::factory()->create(['role' => 'producer']);
        $user->badge()->delete();
        $data = [
            'brand' => 'Brand A',
            'phone' => '123456789',
            'cords' => [
                'long' => '33.55555',
                'lat' => '31.4444',
            ],
        ];
        $result = $this->producerServices->create($user, $data);

        expect($result)->toBeInstanceOf(Producer::class);
    });

    it('fail to creates a new producer with invalid data', function () {
        $user = User::factory()->create(['role' => 'producer']);
        $user->badge()->delete();
        $data = [
            'brand' => 'Brand A',
            'phone' => '123456789',
            // 'cords' => [
            //     'long' => '33.55555',
            //     'lat' => '31.4444'
            // ],
        ];
        $this->producerServices->create($user, $data);
    })->throws(Exception::class);

    it('updates an existing producer', function () {
        $producer = Producer::factory()->create();
        $data = ['brand' => 'Brand A'];
        $result = $this->producerServices->update($producer, $data);
        expect($result)->toBeTrue();
    });

    it('deletes a producer', function () {
        $producer = Producer::factory()->create();
        $result = $this->producerServices->delete($producer);
        expect($result)->toBeTrue();
    });

    it('fail to deletes a producer with invalid carrier', function () {
        $this->producerServices->delete(999);
    })->throws(TypeError::class);
});
