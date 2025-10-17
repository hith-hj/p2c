<?php

arch()
	->expect('App')
	->toUseStrictTypes()
	// ->toHaveMethodsDocumented()
	->not->toUse(['die', 'dd', 'dump']);

arch()
	->expect('App\Models')
	->toBeClasses()
	->toExtend('Illuminate\Database\Eloquent\Model')
	->toHaveLineCountLessThan(100)
	->toBeFinal();

arch()->expect('App\Http')->toOnlyBeUsedIn('App\Http');
arch()->expect('App\Traits')->toBeTraits();
arch()->expect('App\Services')->toExtendNothing()->toBeClasses();
arch()->expect('App\Validators')->toExtendNothing()->toBeClasses();
arch()->expect('App\Enums')->toHaveMethod(['names', 'values']);


// arch()->preset()->security()->ignoring('md5');
// arch()->preset()->php();
