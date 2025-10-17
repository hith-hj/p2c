<?php

declare(strict_types=1);

use App\Http\Controllers\V1\BranchController;
use App\Services\BranchServices;
use App\Models\V1\Branch;

beforeEach(function () {
    $this->api('producer');
    $this->url = 'api/v1/branch';
    $this->controller = new BranchController(new BranchServices());
});

describe('Branch Controller', function () {

    it('returns error message for all method', function () {
        $res = $this->controller->all();
        expect($res->status())->toBe(400)
            ->and($res->getData(true)['message'])->toBe(__('main.coming soon'));
    });

    it('returns branches for get method', function () {
        Branch::factory()->for($this->user->badge, 'producer')->create();
        $res = $this->getJson("$this->url");
        expect($res->status())->toBe(200)
            ->and($res->json('payload.branches'))->toHaveCount(2);
    });

    it('fails to returns branches for get method without valid badge', function () {
        $this->user->badge->update(['is_valid' => 0]);
        Branch::factory()->for($this->user->badge, 'producer')->create();
        $res = $this->getJson("$this->url");
        expect($res->status())->toBe(403);
    });

    it('finds a branch correctly', function () {
        $branch = Branch::factory()->create();
        $res = $this->getJson("$this->url/find?branch_id=$branch->id");
        expect($res->status())->toBe(200)
            ->and($res->json('payload.branch.name'))->toBe($branch->name);
    });

    it('fails to finds a branch correctly with invalid id', function () {
        $res = $this->getJson("$this->url/find?branch_id=909090");
        expect($res->status())->toBe(422);
    });

    it('creates a branch', function () {
        $branch = Branch::factory()->make()->toArray();
        $branch = array_merge($branch, ['cords' => ['long' => 31.5555555,  'lat' => 31.55555555]]);
        $res = $this->postJson("$this->url/create", $branch);
        expect($res->status())->toBe(200)
            ->and($res->json('payload.branch.name'))->toBe($branch['name']);
    });

    it('fails to creates a branch with invalid data', function () {
        $branch = Branch::factory()->make()->toArray();
        $res = $this->postJson("$this->url/create", $branch);
        expect($res->status())->toBe(422);
    });

    it('updates a branch if authorized', function () {
        $branch = Branch::factory()->for($this->user->badge, 'producer')->create();
        $res = $this->patchJson("$this->url/update", ['branch_id' => $branch->id, 'name' => 'siko']);

        expect($res->status())->toBe(200)
            ->and($branch->fresh()->name)->toBe('siko');
    });

    it('fails to updates a branch with invalid id or invalid data', function () {
        $branch = Branch::factory()->for($this->user->badge, 'producer')->create();
        $res = $this->patchJson("$this->url/update", []);

        expect($res->status())->toBe(422);
    });

    it('denies unauthorized branch update', function () {
        $branch = Branch::factory()->create();
        $res = $this->patchJson("$this->url/update", ['branch_id' => $branch->id, 'name' => 'siko']);
        expect($res->status())->toBe(403);

    });
});
