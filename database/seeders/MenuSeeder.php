<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            'Signature' => [
                ['name' => 'Salted Caramel Mist', 'cost_price' => 12000, 'selling_price' => 30000, 'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400'],
                ['name' => 'Misty Lake Butterscotch', 'cost_price' => 12000, 'selling_price' => 30000, 'image' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=400'],
            ],
            'Coffee' => [
                ['name' => 'Cappuccino (Hot)', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=400'],
                ['name' => 'Cappuccino (Ice)', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=400'],
                ['name' => 'Cafe Latte (Hot)', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=400'],
                ['name' => 'Cafe Latte (Ice)', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400'],
                ['name' => 'Mochaccino (Hot)', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1607681034540-2c46cc71896d?w=400'],
                ['name' => 'Mochaccino (Ice)', 'cost_price' => 8000, 'selling_price' => 20000, 'image' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=400'],
                ['name' => 'Americano (Hot)', 'cost_price' => 5000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=400'],
                ['name' => 'Americano (Ice)', 'cost_price' => 6000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1517701632951-d025dd31dd50?w=400'],
                ['name' => 'Piccolo', 'cost_price' => 4000, 'selling_price' => 10000, 'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=400'],
            ],
            'Coffee Flav' => [
                ['name' => 'Butterscotch Latte (Hot)', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1585494156145-1c60a4fe9d2b?w=400'],
                ['name' => 'Butterscotch Latte (Ice)', 'cost_price' => 8000, 'selling_price' => 20000, 'image' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=400'],
                ['name' => 'Hazelnut Latte (Hot)', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=400'],
                ['name' => 'Hazelnut Latte (Ice)', 'cost_price' => 8000, 'selling_price' => 20000, 'image' => 'https://images.unsplash.com/photo-1517701632951-d025dd31dd50?w=400'],
                ['name' => 'Caramel Latte (Hot)', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?w=400'],
                ['name' => 'Caramel Latte (Ice)', 'cost_price' => 8000, 'selling_price' => 20000, 'image' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=400'],
                ['name' => 'Dirty Latte (Ice)', 'cost_price' => 8000, 'selling_price' => 20000, 'image' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=400'],
            ],
            'Non Coffee' => [
                ['name' => 'Matcha Latte (Hot)', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?w=400'],
                ['name' => 'Matcha Latte (Ice)', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1515823662972-da6a2e4d3002?w=400'],
                ['name' => 'Chocolate (Hot)', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=400'],
                ['name' => 'Chocolate (Ice)', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1517578239113-b03992dcdd25?w=400'],
                ['name' => 'Strawberry Matcha (Ice)', 'cost_price' => 10000, 'selling_price' => 25000, 'image' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=400'],
                ['name' => 'Green Milktea (Ice)', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1558857563-b37cf5a2d64f?w=400'],
            ],
            'Juice' => [
                ['name' => 'Strawberry Juice', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=400'],
                ['name' => 'Avocado Juice', 'cost_price' => 7000, 'selling_price' => 18000, 'image' => 'https://images.unsplash.com/photo-1623065422902-30a2d299bbe4?w=400'],
            ],
            'Snack' => [
                ['name' => 'French Fries', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1576107232684-1279f3908594?w=400'],
                ['name' => 'Sosis Bakar', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1585325701165-351af916e581?w=400'],
                ['name' => 'Pisang Cokelat', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1528975604071-b4dc52a2d18c?w=400'],
                ['name' => 'Sosis Goreng', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?w=400'],
            ],
            'Dessert' => [
                ['name' => 'Cheese Cake', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?w=400'],
                ['name' => 'Iceberg Cake', 'cost_price' => 8000, 'selling_price' => 20000, 'image' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400'],
                ['name' => 'Choco Banana Cake', 'cost_price' => 6000, 'selling_price' => 15000, 'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=400'],
            ],
            'Food & Noodles' => [
                ['name' => 'Nasi Goreng', 'cost_price' => 8000, 'selling_price' => 20000, 'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=400'],
                ['name' => 'Mie Goreng / Kuah & Sayur', 'cost_price' => 5000, 'selling_price' => 12000, 'image' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400'],
                ['name' => 'Pop Mie', 'cost_price' => 5000, 'selling_price' => 10000, 'image' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=400'],
            ],
        ];

        foreach ($menus as $categoryName => $products) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );

            foreach ($products as $item) {
                Product::updateOrCreate(
                    [
                        'name'        => $item['name'],
                        'category_id' => $category->id,
                    ],
                    [
                        'sku'           => 'RC-' . strtoupper(Str::random(5)),
                        'cost_price'    => $item['cost_price'],
                        'selling_price' => $item['selling_price'],
                        'stock'         => 50,
                        'is_active'     => true,
                        'image'         => $item['image'],
                    ]
                );
            }
        }
    }
}