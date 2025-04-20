<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index()
    {
        $menuItems = [
            [
                'name' => 'Caffee Latte',
                'description' => 'A creamy blend of espresso and steamed milk.',
                'category' => 'Coffee',
                'price' => 22000,
                'most_ordered' => true,
                'image' => 'https://media-hosting.imagekit.io/01863d534f344bd6/screenshot_1744966960557.png?Expires=1839574963&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=RBMM10C6pDbqF9jwjrzMhQwEt1GyA3XBj-3On~WCTaLqE9ad~HIuaNXzvBwa4oNQrVuf0RfTOT3bjz1fAD4xP-du3gcHCiK3eVEFBHLhtAUuFExSiIctlmkIV4c0qfuEHxlGbdN7MgjqYAxQe0R~36qfjXQfli9lzPlzA8-iMSi1E0LI0TVwq3bzwmd99iPc794IIylmhJjOC8tAS3EOByFMVpvKLJfe97nyltj0Uqj5FxA8y3~GiTc67XgqIoy1Z3NuIu6T52yTssmx~bvSVKCkIW-yEd07lfMGUUhJC55B305lKcuU0ZX3pKQfzJYXDBna-XizrL~PRrjpava0Rw__'
            ],
            [
                'name' => 'Espresso',
                'description' => 'Strong and bold single shot of coffee.',
                'category' => 'Coffee',
                'price' => 18000,
                'most_ordered' => false,
                'image' => ''
            ],
            [
                'name' => 'Friench Fries',
                'description' => 'Crispy golden fries served with sauce.',
                'category' => 'Snack',
                'price' => 22000,
                'most_ordered' => true,
                'image' => 'https://media-hosting.imagekit.io/592487a6bb274b54/screenshot_1744967010087.png?Expires=1839575012&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=xg1lbUqCwf7LnSwuXtyaxmLrxilAq93XT2zp7P6ljfFIHMAskNi4vpqqP3g1LlDGX~tMqkpna8gh5p5arJr7cLEwpANZTijeC6xMK6hi2IGg4p~iTquJNSPuKrPoPTK9aGLFAXYNNLN1hUf8hOmsM1iL26tK1K3M9q4keujjn0zrX50Au0wZ~gXnuKRX8R-4OsBYqOt4lUh3IOZRfyXawnLE5CoHn0YwKLtaFGOFkZXDwlUXpavIhXbOuKJNWhuc1xtlkKG1DCbgIPvjbmgxJ5xYEWuSrP~EJ9nYHWMv80CDv-nCCaF9~seGKFyYci985drLYk0CLmsBb~STcfCgCA__'
            ],
        ];

        // Group by category manually
        $grouped = collect($menuItems)->groupBy('category');

        return view('welcome', ['menuItems' => $grouped]);
        // $menuItems = MenuItem::all()->groupBy('category');
        // return view('menu.index', compact('menuItems'));
    }

    public function orderPage()
    {
        $menuItems = [
            [
                'id' => 1,
                'name' => 'Caffee Latte',
                'category' => 'Coffee',
                'price' => 22000,
                'image' => 'https://media-hosting.imagekit.io/01863d534f344bd6/screenshot_1744966960557.png?Expires=1839574963&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=RBMM10C6pDbqF9jwjrzMhQwEt1GyA3XBj-3On~WCTaLqE9ad~HIuaNXzvBwa4oNQrVuf0RfTOT3bjz1fAD4xP-du3gcHCiK3eVEFBHLhtAUuFExSiIctlmkIV4c0qfuEHxlGbdN7MgjqYAxQe0R~36qfjXQfli9lzPlzA8-iMSi1E0LI0TVwq3bzwmd99iPc794IIylmhJjOC8tAS3EOByFMVpvKLJfe97nyltj0Uqj5FxA8y3~GiTc67XgqIoy1Z3NuIu6T52yTssmx~bvSVKCkIW-yEd07lfMGUUhJC55B305lKcuU0ZX3pKQfzJYXDBna-XizrL~PRrjpava0Rw__'
            ],
            [
                'id' => 2,
                'name' => 'Espresso',
                'category' => 'Coffee',
                'price' => 22000,
                'image' => 'https://media-hosting.imagekit.io/01863d534f344bd6/screenshot_1744966960557.png?Expires=1839574963&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=RBMM10C6pDbqF9jwjrzMhQwEt1GyA3XBj-3On~WCTaLqE9ad~HIuaNXzvBwa4oNQrVuf0RfTOT3bjz1fAD4xP-du3gcHCiK3eVEFBHLhtAUuFExSiIctlmkIV4c0qfuEHxlGbdN7MgjqYAxQe0R~36qfjXQfli9lzPlzA8-iMSi1E0LI0TVwq3bzwmd99iPc794IIylmhJjOC8tAS3EOByFMVpvKLJfe97nyltj0Uqj5FxA8y3~GiTc67XgqIoy1Z3NuIu6T52yTssmx~bvSVKCkIW-yEd07lfMGUUhJC55B305lKcuU0ZX3pKQfzJYXDBna-XizrL~PRrjpava0Rw__'
            ],
            [
                'id' => 3,
                'name' => 'Moccachino',
                'category' => 'Coffee',
                'price' => 22000,
                'image' => 'https://media-hosting.imagekit.io/01863d534f344bd6/screenshot_1744966960557.png?Expires=1839574963&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=RBMM10C6pDbqF9jwjrzMhQwEt1GyA3XBj-3On~WCTaLqE9ad~HIuaNXzvBwa4oNQrVuf0RfTOT3bjz1fAD4xP-du3gcHCiK3eVEFBHLhtAUuFExSiIctlmkIV4c0qfuEHxlGbdN7MgjqYAxQe0R~36qfjXQfli9lzPlzA8-iMSi1E0LI0TVwq3bzwmd99iPc794IIylmhJjOC8tAS3EOByFMVpvKLJfe97nyltj0Uqj5FxA8y3~GiTc67XgqIoy1Z3NuIu6T52yTssmx~bvSVKCkIW-yEd07lfMGUUhJC55B305lKcuU0ZX3pKQfzJYXDBna-XizrL~PRrjpava0Rw__'
            ],
            [
                'id' => 4,
                'name' => 'Caffee Latte',
                'category' => 'Coffee',
                'price' => 22000,
                'image' => 'https://media-hosting.imagekit.io/01863d534f344bd6/screenshot_1744966960557.png?Expires=1839574963&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=RBMM10C6pDbqF9jwjrzMhQwEt1GyA3XBj-3On~WCTaLqE9ad~HIuaNXzvBwa4oNQrVuf0RfTOT3bjz1fAD4xP-du3gcHCiK3eVEFBHLhtAUuFExSiIctlmkIV4c0qfuEHxlGbdN7MgjqYAxQe0R~36qfjXQfli9lzPlzA8-iMSi1E0LI0TVwq3bzwmd99iPc794IIylmhJjOC8tAS3EOByFMVpvKLJfe97nyltj0Uqj5FxA8y3~GiTc67XgqIoy1Z3NuIu6T52yTssmx~bvSVKCkIW-yEd07lfMGUUhJC55B305lKcuU0ZX3pKQfzJYXDBna-XizrL~PRrjpava0Rw__'
            ],
            [
                'id' => 5,
                'name' => 'Caffee Latte',
                'category' => 'Coffee',
                'price' => 22000,
                'image' => 'https://media-hosting.imagekit.io/01863d534f344bd6/screenshot_1744966960557.png?Expires=1839574963&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=RBMM10C6pDbqF9jwjrzMhQwEt1GyA3XBj-3On~WCTaLqE9ad~HIuaNXzvBwa4oNQrVuf0RfTOT3bjz1fAD4xP-du3gcHCiK3eVEFBHLhtAUuFExSiIctlmkIV4c0qfuEHxlGbdN7MgjqYAxQe0R~36qfjXQfli9lzPlzA8-iMSi1E0LI0TVwq3bzwmd99iPc794IIylmhJjOC8tAS3EOByFMVpvKLJfe97nyltj0Uqj5FxA8y3~GiTc67XgqIoy1Z3NuIu6T52yTssmx~bvSVKCkIW-yEd07lfMGUUhJC55B305lKcuU0ZX3pKQfzJYXDBna-XizrL~PRrjpava0Rw__'
            ],
            [
                'id' => 6,
                'name' => 'Caffee Latte',
                'category' => 'Coffee',
                'price' => 22000,
                'image' => 'https://media-hosting.imagekit.io/01863d534f344bd6/screenshot_1744966960557.png?Expires=1839574963&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=RBMM10C6pDbqF9jwjrzMhQwEt1GyA3XBj-3On~WCTaLqE9ad~HIuaNXzvBwa4oNQrVuf0RfTOT3bjz1fAD4xP-du3gcHCiK3eVEFBHLhtAUuFExSiIctlmkIV4c0qfuEHxlGbdN7MgjqYAxQe0R~36qfjXQfli9lzPlzA8-iMSi1E0LI0TVwq3bzwmd99iPc794IIylmhJjOC8tAS3EOByFMVpvKLJfe97nyltj0Uqj5FxA8y3~GiTc67XgqIoy1Z3NuIu6T52yTssmx~bvSVKCkIW-yEd07lfMGUUhJC55B305lKcuU0ZX3pKQfzJYXDBna-XizrL~PRrjpava0Rw__'
            ],
            [
                'id' => 11,
                'name' => 'Friench Fries',
                'category' => 'Snack',
                'price' => 22000,
                'image' => 'https://media-hosting.imagekit.io/592487a6bb274b54/screenshot_1744967010087.png?Expires=1839575012&Key-Pair-Id=K2ZIVPTIP2VGHC&Signature=xg1lbUqCwf7LnSwuXtyaxmLrxilAq93XT2zp7P6ljfFIHMAskNi4vpqqP3g1LlDGX~tMqkpna8gh5p5arJr7cLEwpANZTijeC6xMK6hi2IGg4p~iTquJNSPuKrPoPTK9aGLFAXYNNLN1hUf8hOmsM1iL26tK1K3M9q4keujjn0zrX50Au0wZ~gXnuKRX8R-4OsBYqOt4lUh3IOZRfyXawnLE5CoHn0YwKLtaFGOFkZXDwlUXpavIhXbOuKJNWhuc1xtlkKG1DCbgIPvjbmgxJ5xYEWuSrP~EJ9nYHWMv80CDv-nCCaF9~seGKFyYci985drLYk0CLmsBb~STcfCgCA__'
            ]
        ];

        // Duplicate for layout preview
        // $menuItems = array_merge($menuItems);

        $groupedItems = collect($menuItems)->groupBy('category');

        return view('cashier-layout.main-order.index', compact('groupedItems'));
    }
}
