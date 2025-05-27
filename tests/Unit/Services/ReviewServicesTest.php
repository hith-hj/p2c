<?php

declare(strict_types=1);

use App\Http\Services\ReviewServices;
use App\Models\V1\Review;
use App\Models\V1\User;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

beforeEach(function () {
    $this->services = new ReviewServices();
    $this->user = User::factory()->create(['role' => 'producer']);
});

describe('Review Services Class', function () {

    it('get all reviews for user', function () {
        Review::factory(2)->for($this->user->badge, 'belongTo')->create();
        expect($this->user->badge->reviews)->toHaveCount(2);
        expect($this->services->all($this->user->badge))
            ->toBeInstanceOf(Collection::class)
            ->toHaveCount(2);
    });

    it('fail to get all reviews for user when not found', function () {
        expect(fn() => $this->services->all($this->user->badge))->toThrow(NotFoundHttpException::class);
    });

    it('fails to retive reviews with invalid object', function () {
        expect(fn() => $this->services->all((object)[]))->toThrow(\Exception::class);
    });

    it('can create review for user', function () {
        $carrier = User::factory()->create(['role' => 'carrier']);
        $data = Review::factory()->for($carrier->badge,'belongTo')->make()->toArray();
        $res = $this->services->create($this->user->badge,$data);
        expect($res)->toBeInstanceOf(Review::class)
            ->and($res->rate)->toBe($data['rate'])
            ->and($res->content)->toBe($data['content']);
    });

    it('fails to create review for with invalid data', function () {
        $this->services->create($this->user->badge,[]);
    })->throws(\Exception::class);

    it('fails to create review for with invalid reviewed object', function () {
        $data = Review::factory()->make()->toArray();
        $this->services->create($this->user->badge,$data);
    })->throws(\Exception::class);
});
