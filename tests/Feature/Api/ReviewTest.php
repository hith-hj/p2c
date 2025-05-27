<?php

use App\Models\V1\Review;
use App\Models\V1\User;

beforeEach(function () {
	$this->api('producer');
	$this->url = 'api/v1/review';
});


describe('Review Controller', function () {

	it('fetches all reviews', function () {
		$rev = Review::factory()->for($this->user->badge, 'belongTo')->create();
		$res = $this->getJson("$this->url/all");
		expect($res->status())->toBe(200)
			->and($res->json('payload.reviews'))->toHaveCount(1)
			->and($res->json('payload.reviews.0.content'))->toBe($rev->content);
	});

	it('creates a review', function () {
		$carrier = User::factory()->create(['role' => 'carrier']);
		$rev = Review::factory()->for($carrier->badge, 'belongTo')->make()->toArray();
		$res = $this->postJson("$this->url/create", $rev);
		$res->dump();
		expect($res->status())->toBe(200)
			->and($res->json('payload.review'))->not->toBeNull()
			->and($res->json('payload.review.content'))->toBe($rev['content']);
	});

	// it('returns empty list when there are no reviews', function () { });

	// it('prevents creating a review with invalid data', function () { });

	// it('handles unauthorized access for fetching reviews', function () { });

	// it('handles unauthorized access for creating a review', function () { });

	// it('ensures review creation returns expected structure', function () { });
});
