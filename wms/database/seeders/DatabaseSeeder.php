<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\Inventory;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        foreach (['Admin', 'Manager', 'Staff'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Users
        $admin = User::firstOrCreate(['email' => 'admin@wms.com'], [
            'name' => 'System Admin', 'password' => Hash::make('password'), 'phone' => '+92 300 0000000', 'is_active' => true,
        ]);
        $admin->syncRoles(['Admin']);

        $manager = User::firstOrCreate(['email' => 'manager@wms.com'], [
            'name' => 'Warehouse Manager', 'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $manager->syncRoles(['Manager']);

        $staff = User::firstOrCreate(['email' => 'staff@wms.com'], [
            'name' => 'Sales Staff', 'password' => Hash::make('password'), 'is_active' => true,
        ]);
        $staff->syncRoles(['Staff']);

        // Warehouses
        $wh1 = Warehouse::firstOrCreate(['code' => 'WH-001'], ['name' => 'Main Warehouse', 'city' => 'Karachi',   'phone' => '+92-21-1234567', 'manager_id' => $manager->id, 'is_active' => true]);
        $wh2 = Warehouse::firstOrCreate(['code' => 'WH-002'], ['name' => 'North Branch',   'city' => 'Lahore',    'phone' => '+92-42-7654321', 'is_active' => true]);
        $wh3 = Warehouse::firstOrCreate(['code' => 'WH-003'], ['name' => 'South Depot',    'city' => 'Hyderabad', 'is_active' => true]);

        // Units
        $unitData = [
            'PCS' => 'Piece', 'KG' => 'Kilogram', 'G' => 'Gram', 'L' => 'Liter',
            'M' => 'Meter', 'BOX' => 'Box', 'CTN' => 'Carton', 'DOZ' => 'Dozen',
        ];
        $units = [];
        foreach ($unitData as $sym => $name) {
            $units[$sym] = Unit::firstOrCreate(['symbol' => $sym], ['name' => $name, 'is_active' => true]);
        }

        // Categories
        $catNames = ['Electronics','Clothing','Food & Beverages','Hardware','Furniture','Stationery','Pharmaceuticals'];
        $cats = [];
        foreach ($catNames as $c) {
            $cats[$c] = Category::firstOrCreate(['name' => $c], ['is_active' => true]);
        }

        // Brands
        $brandNames = ['Samsung','Apple','Sony','LG','Haier','Generic'];
        $brands = [];
        foreach ($brandNames as $b) {
            $brands[$b] = Brand::firstOrCreate(['name' => $b], ['is_active' => true]);
        }

        // Items + Initial Inventory
        $itemsData = [
            ['name'=>'LCD Monitor 24"',      'pp'=>18000,'sp'=>22000,'cat'=>'Electronics',      'unit'=>'PCS','brand'=>'Samsung','alert'=>5],
            ['name'=>'Wireless Keyboard',    'pp'=>1200, 'sp'=>1800, 'cat'=>'Electronics',      'unit'=>'PCS','brand'=>'Generic','alert'=>10],
            ['name'=>'USB-C Cable 2m',        'pp'=>200,  'sp'=>350,  'cat'=>'Electronics',      'unit'=>'PCS','brand'=>'Generic','alert'=>20],
            ['name'=>'Office Chair',          'pp'=>8500, 'sp'=>12000,'cat'=>'Furniture',        'unit'=>'PCS','brand'=>'Generic','alert'=>3],
            ['name'=>'Writing Desk 120cm',    'pp'=>12000,'sp'=>16500,'cat'=>'Furniture',        'unit'=>'PCS','brand'=>'Generic','alert'=>2],
            ['name'=>'A4 Paper Ream',         'pp'=>450,  'sp'=>600,  'cat'=>'Stationery',       'unit'=>'BOX','brand'=>'Generic','alert'=>15],
            ['name'=>'Ball Pen Blue 10pk',    'pp'=>80,   'sp'=>120,  'cat'=>'Stationery',       'unit'=>'BOX','brand'=>'Generic','alert'=>30],
            ['name'=>'Cotton T-Shirt M',      'pp'=>350,  'sp'=>700,  'cat'=>'Clothing',         'unit'=>'PCS','brand'=>'Generic','alert'=>20],
            ['name'=>'Mineral Water 1L',      'pp'=>25,   'sp'=>40,   'cat'=>'Food & Beverages', 'unit'=>'PCS','brand'=>'Generic','alert'=>50],
            ['name'=>'Paracetamol 500mg Box', 'pp'=>120,  'sp'=>180,  'cat'=>'Pharmaceuticals',  'unit'=>'BOX','brand'=>'Generic','alert'=>25],
        ];
        foreach ($itemsData as $d) {
            $item = Item::firstOrCreate(['name' => $d['name']], [
                'purchase_price' => $d['pp'], 'selling_price' => $d['sp'],
                'category_id'    => $cats[$d['cat']]->id,
                'unit_id'        => $units[$d['unit']]->id,
                'brand_id'       => $brands[$d['brand']]->id,
                'alert_quantity' => $d['alert'],
                'is_active'      => true,
            ]);
            foreach ([$wh1->id, $wh2->id] as $whId) {
                Inventory::firstOrCreate(
                    ['item_id' => $item->id, 'warehouse_id' => $whId, 'item_variant_id' => null],
                    ['quantity' => rand(20, 150), 'reserved_quantity' => 0]
                );
            }
        }

        // Suppliers
        $supplierData = [
            ['name'=>'TechSource Pakistan','code'=>'VN-001','phone'=>'+92 300 1234567','city'=>'Karachi',  'company'=>'TechSource Pvt Ltd'],
            ['name'=>'Global Imports Ltd', 'code'=>'VN-002','phone'=>'+92 321 9876543','city'=>'Lahore',   'company'=>'Global Imports Ltd'],
            ['name'=>'FastSupply Co',      'code'=>'VN-003','phone'=>'+92 333 5551234','city'=>'Islamabad','company'=>'FastSupply Company'],
            ['name'=>'Pak Furniture House','code'=>'VN-004','phone'=>'+92 311 4445678','city'=>'Karachi',  'company'=>'Pak Furniture House'],
            ['name'=>'MedLine Distributors','code'=>'VN-005','phone'=>'+92 345 7778889','city'=>'Lahore',  'company'=>'MedLine Distributors'],
        ];
        foreach ($supplierData as $s) {
            Supplier::firstOrCreate(['code' => $s['code']], array_merge($s, ['is_active' => true, 'opening_balance' => 0]));
        }

        // Customers
        $customerData = [
            ['name'=>'Zain Enterprises',  'code'=>'CS-001','phone'=>'+92 300 9998887','city'=>'Karachi',   'company'=>'Zain Ent.',       'credit_limit'=>100000],
            ['name'=>'Farhan Trading Co', 'code'=>'CS-002','phone'=>'+92 321 6665554','city'=>'Lahore',    'company'=>'Farhan Trading',  'credit_limit'=>75000],
            ['name'=>'Ali Brothers',      'code'=>'CS-003','phone'=>'+92 333 3332221','city'=>'Faisalabad','company'=>'Ali Brothers',    'credit_limit'=>50000],
            ['name'=>'Sara Retail Shop',  'code'=>'CS-004','phone'=>'+92 311 1112223','city'=>'Multan',    'company'=>'Sara Retail',     'credit_limit'=>30000],
            ['name'=>'NextGen Solutions', 'code'=>'CS-005','phone'=>'+92 345 4445556','city'=>'Islamabad', 'company'=>'NextGen Solutions','credit_limit'=>200000],
        ];
        foreach ($customerData as $c) {
            Customer::firstOrCreate(['code' => $c['code']], array_merge($c, ['is_active' => true, 'opening_balance' => 0]));
        }

        // Expense Categories
        foreach (['Salaries','Utilities','Rent','Transport & Logistics','Marketing','Maintenance','Office Supplies','Miscellaneous'] as $ec) {
            ExpenseCategory::firstOrCreate(['name' => $ec], ['is_active' => true]);
        }

        $this->command->info('');
        $this->command->info('✅ WMS seeded. Login: admin@wms.com / password');
    }
}
