<?php

namespace Database\Seeders;

use App\Enums\Product\InspectionQuestionTypeEnum;
use App\Models\Product\InspectionProcedure;
use App\Models\Product\InspectionSchedule;
use App\Models\Product\Manufacturer;
use App\Models\Product\Product;
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

        $manufacturerHaulotte = Manufacturer::create([ 
            'name' => 'Haulotte',
        ]);

        $manufacturerDeWalt = Manufacturer::create([
            'name' => 'DeWalt',
        ]);

        $product4527Header = Product::create([
            'reference' => '3813',
            'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
            'manufacturer_id' => $manufacturerHaulotte->id,
            'model' => '4527A',
            'is_header' => TRUE,
        ]);

        $product4527Header->children()->createMany([
            [
                'reference' => '3813-03',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'manufacturer_id' => $manufacturerHaulotte->id,
                'model' => '4527A',
                'serial_number' => '45A16-00277',
                'model_year' => 2016,
            ],[
                'reference' => '3813-04',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'manufacturer_id' => $manufacturerHaulotte->id,
                'model' => '4527A',
                'serial_number' => '2083888-00180',
                'model_year' => NULL,
            ],[
                'reference' => '3813-05',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'manufacturer_id' => $manufacturerHaulotte->id,
                'model' => '4527A',
                'serial_number' => '2084742-00200',
                'model_year' => NULL,
            ],[
                'reference' => '3813-06',
                'name' => 'ARTIC. LIFT, TOWABLE 45\' 4527A',
                'manufacturer_id' => $manufacturerHaulotte->id,
                'model' => '4527A',
                'serial_number' => '2122514-00621',
                'model_year' => 2022,
            ]
        ]);

        $product65lbBreakerHeader = Product::create([
            'reference' => '1505',
            'name' => 'BREAKER ELECTRIC 65 LB',
            'manufacturer_id' => $manufacturerDeWalt->id,
            'model' => 'D25980',
            'is_header' => TRUE,            
        ]);

        $product65lbBreakerHeader->children()->createMany([
            [
                'reference' => '1505-10',
                'name' => 'BREAKER ELECTRIC 65 LB',
                'manufacturer_id' => $manufacturerDeWalt->id,
                'model' => 'D25980',
                'model_type' => '3',
                'serial_number' => '007269',
            ],[
                'reference' => '1505-11',
                'name' => 'BREAKER ELECTRIC 65 LB',
                'manufacturer_id' => $manufacturerDeWalt->id,
                'model' => 'D25980',
                'model_type' => '4',
                'serial_number' => '014932',
            ],[
                'reference' => '1505-12',
                'name' => 'BREAKER ELECTRIC 65 LB',
                'manufacturer_id' => $manufacturerDeWalt->id,
                'model' => 'D25980',
                'model_type' => NULL,
                'serial_number' => '027420',
            ],[
                'reference' => '1505-13',
                'name' => 'BREAKER ELECTRIC 65 LB',
                'manufacturer_id' => $manufacturerDeWalt->id,
                'model' => 'D25980',
                'model_type' => '4',
                'serial_number' => '027422',
            ]
        ]);

        $annualInspection = InspectionProcedure::firstOrCreate([
            'name' => 'Annual Inspection',
        ]);

        $rentReadyInspection = InspectionProcedure::firstOrCreate([
            'name' => 'Rent Ready Inspection',
        ]);

        $generalConditionsInspection = InspectionProcedure::firstOrCreate([
            'name' => 'Inspect General Condition'
        ]);
        
        $generalConditionsTemplate = InspectionSchedule::create([
            'procedure_id' => $generalConditionsInspection->id,
        ]);

        $electricalInspection = InspectionProcedure::firstOrCreate([
            'name' => 'Inspect Electrical Equipment'
        ]);

        $electricalInspectionTemplate = InspectionSchedule::create([
            'procedure_id' => $electricalInspection->id,
        ]);

        $annualInspection4527 = InspectionSchedule::create([
            'product_id' => $product4527Header->id,
            'procedure_id' => $annualInspection->id,
        ]);

        $rentReadyInspection4527 = InspectionSchedule::create([
            'product_id' => $product4527Header->id,
            'procedure_id' => $rentReadyInspection->id,
        ]);

        $annualInspection65lbBreaker = $product65lbBreakerHeader->inspectionSchedules()->create([
            'procedure_id' => $annualInspection->id,
        ]);
        $rentReadyInspection65lbBreaker = $product65lbBreakerHeader->inspectionSchedules()->create([
            'procedure_id' => $rentReadyInspection->id,
        ]);    

        $generalConditionsTemplate->questions()->createMany([
            [
                'question' => 'Has the product been cleaned?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
            [
                'question' => 'Are all parts secure?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
            [
                'question' => 'Do all functions operate correctly?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
            [
                'question' => 'Are all safety guards intact and in good working order?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
            [
                'question' => 'Are all the safety labels and decals in good overall condition?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
            [
                'question' => 'Is the paint in good overall condition?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
        ]);

        $electricalInspectionTemplate->questions()->createMany([
            [
                'question' => 'Does the equipment pass the electrical safety check?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
            [
                'question' => 'Is the GFCI and Ground Prong intact and in good working order?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
            [
                'question' => 'Are the cord and plug in good working order?',
                'type' => InspectionQuestionTypeEnum::Toggle,
                'options' => [
                    "toggleState" => true,
                ],
            ],
            [
                'question' => 'What is the amp draw?',
                'type' => InspectionQuestionTypeEnum::Text,
            ],
        ]);

        $rentReadyInspection4527->questions()->create([
            'type' => InspectionQuestionTypeEnum::FromTemplate,
            'options' => [
                "templateProduct" => NULL,
                "templateSchedule" => $generalConditionsTemplate->id,
            ],
        ]);

        $rentReadyInspection65lbBreaker->questions()->createMany([
            [
                'type' => InspectionQuestionTypeEnum::FromTemplate,
                'options' => [
                    "templateProduct" => NULL,
                    "templateSchedule" => $generalConditionsTemplate->id,
                ],
            ],
            [
                'type' => InspectionQuestionTypeEnum::FromTemplate,
                'options' => [
                    "templateProduct" => NULL,
                    "templateSchedule" => $electricalInspectionTemplate->id,
                ],
            ],
        ]);
    }
}
