<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\CrmPlan;
use App\Models\CrmTheme;
use App\Models\CrmType;
use App\Models\CrmStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        CrmPlan::insert([
            [
                'name' => 'free',
                'description' => '',
                'price' => 0,
                'status' => 1,
                'stripe_id' => '',
            ],
            [
                'name' => 'standard',
                'description' => '',
                'price' => 1499,
                'status' => 1,
                'stripe_id' => '',
            ],
            [
                'name' => 'professional',
                'description' => '',
                'price' => 9999,
                'status' => 1,
                'stripe_id' => '',
            ],
        ]);

        CrmType::insert([
            [
                'name' => 'iceburg',
                'description' => 'Create your own IceburgCRM using the standard template.  You can see a demo <a href="demo.iceburg.ca" target="_blank">here</a>',
            ],
            [
                'name' => 'connection',
                'description' => 'Connect your MySQL database and we\'ll import your table and fields and create a CRM from it.  Read permissions need to be enabled for the user connecting.',

            ],
            [
                'name' => 'uploadschema',
                'description' => 'Upload a MySQL dump file and we\'ll create a CRM from the tables and Fields',

            ],
            [
                'name' => 'ontop',
                'description' => 'Connect your MySQL database and we\'ll install IceburgCRM in your database schema which will allow you to use the CRM to manage data.  Write permissions need to be enabled for the user connecting.'
            ]
        ]);

        CrmStatus::insert([
            [
                'name' => 'Creating',
            ],
            [
                'name' => 'Active',
            ],
            [
                'name' => 'Disabled',
            ],
            [
                'name' => 'Deleted',
            ]
        ]);

        CrmTheme::insert([
                ['name' => "light"],
                ['name' => "dark"],
                ['name' => "cupcake"],
                ['name' => "bumblebee"],
                ['name' => "emerald"],
                ['name' => "corporate"],
                ['name' => "synthwave"],
                ['name' => "retro"],
                ['name' => "cyberpunk"],
                ['name' => "valentine"],
                ['name' => "halloween"],
                ['name' => "garden"],
                ['name' => "forest"],
                ['name' => "aqua"],
                ['name' => "lofi"],
                ['name' => "pastel"],
                ['name' => "fantasy"],
                ['name' => "wireframe"],
                ['name' => "black"],
                ['name' => "luxury"],
                ['name' => "dracula"],
                ['name' => "cmyk"],
                ['name' => "autumn"],
                ['name' => "business"],
                ['name' => "acid"],
                ['name' => "lemonade"],
                ['name' => "night"],
                ['name' => "coffee"],
                ['name' => "winter"],

        ]);

    }
}
