<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\DeliveryArea;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@lakeview.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'phone' => '+8801722554400',
            'email_verified_at' => now(),
        ]);

        $settings = [
            ['key' => 'site_name', 'value' => 'Lake View Sweets & Bakery', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'The Finest Sweets & Bakery', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Lake View Sweets & Bakery - The finest sweets and bakery in Satkhira. We are committed to delivering the highest quality sweets, cakes, biscuits and bakery products.', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => '', 'group' => 'general'],
            ['key' => 'hero_title', 'value' => 'Lake View Sweets & Bakery', 'group' => 'homepage'],
            ['key' => 'hero_subtitle', 'value' => 'The finest sweets and bakery in Satkhira - delivering the best quality products to your doorstep', 'group' => 'homepage'],
            ['key' => 'hero_image', 'value' => '', 'group' => 'homepage'],
            ['key' => 'about_text', 'value' => 'Lake View Sweets & Bakery was established in 2023. We produce the highest quality sweets, cakes, biscuits and bakery products in Satkhira district. We have 7 branches across Satkhira Sadar and surrounding areas.', 'group' => 'general'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/lakeviewsweets', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => '', 'group' => 'social'],
            ['key' => 'whatsapp_number', 'value' => '+8801722554400', 'group' => 'social'],
            ['key' => 'opening_hours', 'value' => 'Open Daily 7:00 AM - 10:00 PM', 'group' => 'general'],
            ['key' => 'min_order_amount', 'value' => '0', 'group' => 'delivery'],
            ['key' => 'custom_cake_info', 'value' => 'Order your custom cake. Upload your design and we will create the cake exactly as you want it.', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        $categoriesData = [
            ['name' => 'Fast Food', 'sort_order' => 1],
            ['name' => 'Bread', 'sort_order' => 2],
            ['name' => 'Cookies', 'sort_order' => 3],
            ['name' => 'Toast', 'sort_order' => 4],
            ['name' => 'Dessert', 'sort_order' => 5],
            ['name' => 'Cake', 'sort_order' => 6],
            ['name' => 'Order Cake', 'sort_order' => 7],
            ['name' => 'Sweets', 'sort_order' => 8],
        ];

        $categoryMap = [];
        foreach ($categoriesData as $cat) {
            $category = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']) . '-' . Str::random(5),
                'sort_order' => $cat['sort_order'],
                'is_active' => true,
            ]);
            $categoryMap[$cat['name']] = $category->id;
        }

        $products = [
            ['Burger', 'Fast Food', 70],
            ['Cheese Burger', 'Fast Food', 100],
            ['Hot Spicy', 'Fast Food', 60],
            ['Mini Pizza', 'Fast Food', 70],
            ['Pizza', 'Fast Food', 80],
            ['French Pizza', 'Fast Food', 80],
            ['Patties 40', 'Fast Food', 40],
            ['Meatloaf', 'Fast Food', 90],
            ['Pop Roll', 'Fast Food', 70],
            ['Sandwich', 'Fast Food', 70],
            ['Cream Roll', 'Fast Food', 50],
            ['Pastry Cake', 'Fast Food', 150],
            ['Mini Pastry Cake', 'Fast Food', 70],
            ['Fruit Cake', 'Fast Food', 180],
            ['Dry Cake', 'Fast Food', 130],
            ['Honeycomb', 'Bread', 20],
            ['Butterbun', 'Bread', 20],
            ['Bread 300g', 'Bread', 35],
            ['Bread 420g', 'Bread', 50],
            ['Fruit Bread Small', 'Bread', 50],
            ['Fruit Bread Large', 'Bread', 70],
            ['Pop Stick', 'Cookies', 130],
            ['Sugar Free Cookies', 'Cookies', 130],
            ['Almond Cookies', 'Cookies', 160],
            ['Fruit Cookies', 'Cookies', 150],
            ['Cashew Cookies', 'Cookies', 150],
            ['Vermicelli Cookies', 'Cookies', 160],
            ['Nut Cookies', 'Cookies', 150],
            ['Salt Cookies', 'Cookies', 140],
            ['Butter Cookies', 'Cookies', 130],
            ['Sponge Cookies', 'Cookies', 130],
            ['Garlic Toast', 'Toast', 60],
            ['Chili Toast', 'Toast', 60],
            ['Butter Toast', 'Toast', 50],
            ['Bombay Toast', 'Toast', 40],
            ['Donut', 'Dessert', 60],
            ['Muffin', 'Dessert', 60],
            ['Chocolate Ball', 'Dessert', 40],
            ['Swiss Roll', 'Dessert', 70],
            ['Pudding', 'Dessert', 50],
            ['Chocolate Mousse', 'Dessert', 70],
            ['White Mousse', 'Dessert', 70],
            ['White Forest Pastry', 'Dessert', 70],
            ['Black Forest Pastry', 'Dessert', 70],
            ['Chocolate Pastry', 'Dessert', 70],
            ['Orange Pastry', 'Dessert', 90],
            ['Red Velvet Pastry', 'Dessert', 90],
            ['White Forest 1/2kg Cake', 'Cake', 600],
            ['Black Forest 1/2kg Cake', 'Cake', 600],
            ['Chocolate 1/2kg Cake', 'Cake', 600],
            ['1/2kg Vanilla Cake', 'Cake', 600],
            ['Orange 1/2kg Cake', 'Cake', 600],
            ['Strawberry 1/2kg Cake', 'Cake', 600],
            ['Lemon 1/2kg Cake', 'Cake', 600],
            ['Blueberry Cake 1/2kg', 'Cake', 600],
            ['Red Velvet Cake 1/2kg', 'Cake', 700],
            ['Vanilla Mini Cake 300g', 'Cake', 400],
            ['1/2kg Cake', 'Order Cake', 600],
            ['1kg Cake', 'Order Cake', 1200],
            ['1kg Red Velvet Cake', 'Order Cake', 1400],
            ['1.5kg Cake', 'Order Cake', 1800],
            ['1.5kg Red Velvet Cake', 'Cake', 2100],
            ['2kg Cake', 'Order Cake', 2400],
            ['Dry Rasgolla', 'Sweets', 280],
            ['Kalojam', 'Sweets', 280],
            ['Red Chamcham', 'Sweets', 280],
            ['Mini Chamcham', 'Sweets', 350],
            ['Baby Sweets', 'Sweets', 300],
            ['Malaikari', 'Sweets', 350],
            ['Shahi Toast', 'Sweets', 350],
            ['Langcha', 'Sweets', 380],
            ['Creamjam', 'Sweets', 360],
            ['Golap Jam', 'Sweets', 350],
            ['Pora Sandesh', 'Sweets', 420],
            ['Maya Sandesh', 'Sweets', 400],
            ['Hafsi Sandesh', 'Sweets', 500],
            ['White Sandesh', 'Sweets', 400],
            ['Sandwich', 'Sweets', 350],
            ['S.P Rasgolla', 'Sweets', 320],
            ['Kacha Chana', 'Sweets', 450],
            ['Mihi Dana Laddu', 'Sweets', 280],
            ['Orange Laddu', 'Sweets', 280],
            ['Strawberry Laddu', 'Sweets', 280],
            ['Sar Doi 1kg', 'Sweets', 220],
            ['Tok-Mishti Doi 1kg', 'Sweets', 200],
            ['Tok-Mishti Doi 500g', 'Sweets', 110],
            ['Kaf Doi', 'Sweets', 25],
            ['Toffee Chocolate', 'Sweets', 10],
            ['Guji Guava 100g', 'Sweets', 60],
            ['Potato Chanachur 300g', 'Sweets', 120],
            ['Chira', 'Sweets', 120],
            ['Rasmalai 1kg', 'Sweets', 320],
            ['Rasmalai 500g', 'Sweets', 160],
            ['Dudh Sandesh', 'Sweets', 500],
            ['Dodiya Sandesh', 'Sweets', 550],
            ['Sar Malai', 'Sweets', 40],
            ['Malai Pastry', 'Sweets', 550],
            ['Malai Chop', 'Sweets', 500],
            ['Cutlet', 'Sweets', 450],
            ['Muskat Halwa', 'Sweets', 400],
            ['Carrot Barfi', 'Sweets', 460],
            ['Balushai', 'Sweets', 300],
        ];

        $sortOrder = 1;
        $featuredNames = ['1kg Cake', 'Dry Rasgolla', 'Kalojam', 'Chocolate Pastry', 'Black Forest 1/2kg Cake', 'Rasmalai 1kg'];
        foreach ($products as $product) {
            $catName = $product[1];
            $categoryId = $categoryMap[$catName] ?? null;
            if (!$categoryId) continue;

            Product::create([
                'category_id' => $categoryId,
                'name' => $product[0],
                'slug' => Str::slug($product[0]) . '-' . Str::random(5),
                'price' => $product[2],
                'is_available' => true,
                'is_featured' => in_array($product[0], $featuredNames),
                'sort_order' => $sortOrder++,
            ]);
        }

        $branches = [
            ['name' => 'Nalta (Opposite Ahsania Petrol Pump)', 'phones' => ['+8801968101984'], 'sort_order' => 1],
            ['name' => 'AB Bank, Adjacent to Satkhira', 'phones' => ['+8801958216722'], 'sort_order' => 2],
            ['name' => 'Adjacent to Sadar Hospital', 'phones' => ['+8801958611949'], 'sort_order' => 3],
            ['name' => 'Adjacent to Land Office', 'phones' => ['01968-101984'], 'sort_order' => 4],
            ['name' => 'Adjacent to Govt Girls School', 'phones' => ['01958611946'], 'sort_order' => 5],
            ['name' => 'Old Satkhira Hatkhola Mor', 'phones' => ['01958216723'], 'sort_order' => 6],
            ['name' => 'Lake View Cafe & Restaurant (Main)', 'phones' => ['+8801722554400'], 'sort_order' => 7],
        ];

        foreach ($branches as $branch) {
            Branch::create([
                'name' => $branch['name'],
                'slug' => Str::slug($branch['name']) . '-' . Str::random(5),
                'phones' => $branch['phones'],
                'is_active' => true,
                'sort_order' => $branch['sort_order'],
            ]);
        }

        $allBranches = Branch::all();
        foreach ($allBranches as $branch) {
            DeliveryArea::create([
                'branch_id' => $branch->id,
                'name' => 'Satkhira Sadar',
                'zone_type' => 'sadar',
                'delivery_charge' => 30,
                'is_active' => true,
            ]);
            DeliveryArea::create([
                'branch_id' => $branch->id,
                'name' => 'Outside Sadar',
                'zone_type' => 'outside_sadar',
                'delivery_charge' => 60,
                'is_active' => true,
            ]);
        }

        Coupon::create([
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 200,
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'FLAT50',
            'type' => 'fixed',
            'value' => 50,
            'min_order_amount' => 500,
            'is_active' => true,
        ]);
    }
}
