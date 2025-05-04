<?php

declare(strict_types=1);

use App\Http\Services\BranchServices;
use App\Models\V1\Branch;
use App\Models\V1\Producer;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->branchServices = new BranchServices();
});

describe('Branch Services', function () {

    it('retrieves a branch by ID', function () {
        $branch = Branch::factory()->create();
        $result = $this->branchServices->find($branch->id);
        expect($result)->toBeInstanceOf(Branch::class)->id->toBe($branch->id);
    });

    it('fail to retrieves a branch by ID if not exists', function () {
        $this->branchServices->find(999);
    })->throws(Exception::class);

    it('retrieves branches for a producer', function () {
        $count = 3;
        $producer = Producer::factory()->create();
        $branche = Branch::factory($count)->create();
        $producer->branches()->saveMany($branche);
        $result = $this->branchServices->get($producer->id);

        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount($count);
    });

    it('fail to retrieves a branch by user if not exists', function () {
        $this->branchServices->get(999);
    })->throws(Exception::class);

    it('creates a new branch', function () {
        $producer = Producer::factory()->create();
        $data = [
            'name' => 'Main',
            'phone' => '123456789',
            'cords' => ['long' => '32.11111', 'lat' => '33.22222'],
        ];
        $result = $this->branchServices->create($producer, $data);
        expect($result)->toBeInstanceOf(Branch::class)->phone->toBe('123456789');
    });

    it('falis to creates a new branch with wrong data', function () {
        $producer = Producer::factory()->create();
        $data = [
            'name' => 'Main',
            // 'phone' => '123456789',
        ];
        $result = $this->branchServices->create($producer, $data);
        expect($result)->toBeInstanceOf(Branch::class)->phone->toBe('123456789');
    })->throws(Exception::class);

    it('updates a branch', function () {
        $branch = Branch::factory()->create();
        $data = ['name' => 'Updated Branch'];
        $result = $this->branchServices->update($branch, $data);
        expect($result)->toBeTrue();
    });

    it('fail to updates a branch with wrong data', function () {
        $branch = Branch::factory()->create();
        $data = [];
        $this->branchServices->update($branch, $data);
    })->throws(Exception::class);

    it('deletes a branch', function () {
        $branch = Branch::factory()->create();
        $result = $this->branchServices->delete($branch);

        expect($result)->toBeTrue();
    });

    it('fail to deletes a branch with wrong id', function () {
        $this->branchServices->delete(99);
    })->throws(TypeError::class);

    it('sets a branch as default', function () {
        $branch = Branch::factory()->create();
        $result = $this->branchServices->setBranchAsDefault($branch);
        expect($result)->toBeTrue();
    });
});
