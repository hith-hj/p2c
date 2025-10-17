<?php

declare(strict_types=1);

use App\Services\BranchServices;
use App\Models\V1\Branch;
use App\Models\V1\Producer;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    it('fail to retrieves a branch by ID if id is null', function () {
        $this->branchServices->find(null);
    })->throws(TypeError::class);

    it('retrieves branches for a producer', function () {
        $count = 3;
        $producer = Producer::factory()->create();
        $branche = Branch::factory($count)->create();
        $producer->branches()->saveMany($branche);
        $result = $this->branchServices->get($producer->id);

        expect($result)->toBeInstanceOf(Collection::class)->toHaveCount($count);
    });

    it('fails to retrieves branches for invalid producer id', function () {
        $this->branchServices->get(19999);
    })->throws(NotFoundHttpException::class);

    it('fails to retrieves branches for null producer id', function () {
        $this->branchServices->get(null);
    })->throws(TypeError::class);

    it('creates a new branch', function () {
        $producer = Producer::factory()->create();
        $data = [
            'name' => 'Main',
            'phone' => '123456789',
            'cords' => ['long' => '32.11111', 'lat' => '33.22222'],
        ];
        $res = $this->branchServices->create($producer, $data);
        expect($res)->toBeInstanceOf(Branch::class)->phone->toBe('123456789');
    });

    it('falis to creates a new branch with wrong data', function () {
        $producer = Producer::factory()->create();
        $data = [
            'name' => 'Main',
            // 'phone' => '123456789',
        ];
        $this->branchServices->create($producer, $data);
    })->throws(Exception::class);

    it('falis to creates a new branch with invalid badge', function () {
        $data = [
            'name' => 'Main',
            // 'phone' => '123456789',
        ];
        $this->branchServices->create((object) [], $data);
    })->throws(TypeError::class);

    it('updates a branch', function () {
        $branch = Branch::factory()->create();
        $data = ['name' => 'Updated Branch'];
        $res = $this->branchServices->update($branch, $data);
        expect($res)->toBeTrue();
    });

    it('fail to updates a branch with wrong data', function () {
        $branch = Branch::factory()->create();
        $this->branchServices->update($branch, []);
    })->throws(Exception::class);

    it('fail to updates a branch with invalid branch', function () {
        $this->branchServices->update((object) [], []);
    })->throws(TypeError::class);

    it('deletes a branch', function () {
        $branch = Branch::factory()->create();
        $res = $this->branchServices->delete($branch);

        expect($res)->toBeTrue();
    });

    it('fails to deletes an invalid branch', function () {
        $this->branchServices->delete((object) []);
    })->throws(TypeError::class);

    it('fail to deletes a branch with wrong id', function () {
        $this->branchServices->delete(99);
    })->throws(TypeError::class);

    it('sets a branch as default', function () {
        $branch = Branch::factory()->create();
        $res = $this->branchServices->setBranchAsDefault($branch);
        expect($res)->toBeTrue();
    });

    it('fails to sets a branch as default if branch is not valid', function () {
        $this->branchServices->setBranchAsDefault((object) []);
    })->throws(TypeError::class);
});
