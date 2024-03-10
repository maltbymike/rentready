<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $manufacturerHaulotte = DB::table('product_manufacturers')->insertGetId([ 
            'name' => 'Haulotte',
        ]);

        $manufacturerDeWalt = DB::table('product_manufacturers')->insertGetId([
            'name' => 'DeWalt',
        ]);

        $product4527Header = DB::table('products')->insertGetId(
            [
                'reference' => '3813',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'manufacturer_id' => $manufacturerHaulotte,
                'model' => '4527A',
                'is_header' => TRUE,
            ]);
        
        DB::table('products')->insert([
            [
                'reference' => '3813-03',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'parent_id' => $product4527Header,
                'manufacturer_id' => $manufacturerHaulotte,
                'model' => '4527A',
                'serial_number' => '45A16-00277',
                'model_year' => 2016,
            ],[
                'reference' => '3813-04',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'parent_id' => $product4527Header,
                'manufacturer_id' => $manufacturerHaulotte,
                'model' => '4527A',
                'serial_number' => '2083888-00180',
                'model_year' => NULL,
            ],[
                'reference' => '3813-05',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'parent_id' => $product4527Header,
                'manufacturer_id' => $manufacturerHaulotte,
                'model' => '4527A',
                'serial_number' => '2084742-00200',
                'model_year' => NULL,
            ],[
                'reference' => '3813-06',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'parent_id' => $product4527Header,
                'manufacturer_id' => $manufacturerHaulotte,
                'model' => '4527A',
                'serial_number' => '2122514-00621',
                'model_year' => 2022,
            ]
        ]);

        $product65lbBreakerHeader = DB::table('products')->insertGetId([
            'reference' => '1505',
            'name' => 'BREAKER ELECTRIC 65 LB',
            'manufacturer_id' => $manufacturerDeWalt,
            'model' => 'D25980',
            'is_header' => TRUE,            
        ]);

        DB::table('products')->insert([
            [
                'reference' => '1505-10',
                'name' => 'BREAKER ELECTRIC 65 LB',
                'parent_id' => $product65lbBreakerHeader,
                'manufacturer_id' => $manufacturerDeWalt,
                'model' => 'D25980',
                'model_type' => '3',
                'serial_number' => '007269',
            ],[
                'reference' => '1505-11',
                'name' => 'BREAKER ELECTRIC 65 LB',
                'parent_id' => $product65lbBreakerHeader,
                'manufacturer_id' => $manufacturerDeWalt,
                'model' => 'D25980',
                'model_type' => '4',
                'serial_number' => '014932',
            ],[
                'reference' => '1505-12',
                'name' => 'BREAKER ELECTRIC 65 LB',
                'parent_id' => $product65lbBreakerHeader,
                'manufacturer_id' => $manufacturerDeWalt,
                'model' => 'D25980',
                'model_type' => NULL,
                'serial_number' => '027420',
            ],[
                'reference' => '1505-13',
                'name' => 'BREAKER ELECTRIC 65 LB',
                'parent_id' => $product65lbBreakerHeader,
                'manufacturer_id' => $manufacturerDeWalt,
                'model' => 'D25980',
                'model_type' => '4',
                'serial_number' => '027422',
            ]
        ]);
    }
}
