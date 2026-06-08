# TODO - Car image upload & display (car_img)

- [x] Update `App\Http\Controllers\Admin\CarsController@store` to save `cars.car_img` after FilePond upload.
- [x] Update `App\Http\Controllers\Admin\CarsController@update` to update `cars.car_img` after FilePond updates.
- [x] Update `App\Models\Car::getImageUrlAttribute()` to fallback to `car_img` if no FilePond file exists.
- [x] Update `App\Http\Controllers\HomePagesController@fleet` and `BookingController@show` to eager-load image files so `image_url` works on the website.
- [ ] Run quick test: create car with image -> verify DB row updated + website shows image.


