<?php

namespace App\Http\Controllers;

use App\Models\Database;
use App\Models\Iceburg\Datalet;
use App\Models\Iceburg\DataletType;
use App\Models\Iceburg\Field;
use App\Models\Iceburg\Module;
use App\Models\Iceburg\ModuleGroup;
use App\Models\Iceburg\ModuleSubpanel;
use App\Models\Iceburg\Permission;
use App\Models\Iceburg\User;
use App\Models\Iceburg\Relationship;
use App\Models\Iceburg\SubpanelField;
use Illuminate\Http\Request;
use App\Models\Crm;

use Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use iamcal\SQLParser;



class CreateController extends Controller
{
    public $excludeFields = [
            'id', 'slug', 'soft_delete', 'created_at', 'updated_at'
        ];
    public $prefix ='ice_';
    public $icons = [
      'BuildingOffice2Icon',
        'BuildingOffice2Icon',
        'BuildingOfficeIcon',
        'BuildingLibraryIcon',
        'BuildingStorefrontIcon',
        'BriefcaseIcon',
        'HomeIcon',
        'HomeModernIcon',
        'UserPlusIcon',
        'UserMinusIcon',
        'UserCircleIcon',
        'UserIcon',
        'ChatBubbleLeftIcon',
        'CalculatorIcon',
        'CircleStackIcon',
        'BookOpenIcon',
        'Bars4Icon',
        'UsersIcon',
        'LightBulbIcon',
        'MegaphoneIcon',
        'InboxStackIcon',
        'CurrencyDollarIcon',
        'ArrowRightOnRectangleIcon',
        'QueueListIcon',
        'PencilSquareIcon',
        'DocumentIcon',
        'PencilIcon',
        'UserGroupIcon',
        'GlobeAmericasIcon',
        'RectangleGroupIcon',
        'GlobeAltIcon',
        'CurrencyPoundIcon',
        'SparklesIcon',
        'PhoneIcon',
        'Cog6ToothIcon',
    ];


    public function getIcon($id){
        $icon='BuildingOffice2Icon';

        if(isset($this->icons[$id])){
            $icon=$this->icons[$id];
        }
        return $icon;
    }

    public function getInputType($field) {
        $inputType='text';
        $dataType='string';

        if(str_contains($field['name'], 'video')){
            $inputType='video';
        }
        elseif(str_contains($field['name'], 'image'))
        {
            $inputType='image';
        }
        elseif(str_contains($field['name'], 'address'))
        {
            $inputType='address';
        }
        elseif(str_contains($field['name'], 'zip'))
        {
            $inputType='zip';
        }
        elseif(str_contains($field['name'], 'email'))
        {
            $inputType='email';
        }
        elseif(str_contains($field['name'], 'phone'))
        {
            $inputType='tel';
        }
        elseif(str_contains($field['name'], '%website%') || str_contains($field['name'], '%url%'))
        {
            $inputType='url';
        }
        elseif(str_contains($field['name'], 'number'))
        {
            $inputType='number';
        }
        elseif(str_contains($field['name'], 'password'))
        {
            $inputType='password';
        }

        $field['type']=strtoupper($field['type']);

        switch($field['type']){
            case 'LONGTEXT':
            case 'MEDIUMTEXT':
            case 'TEXT':
                $dataType=$field['type'];
                break;
            case 'TINYINT':
                $inputType='checkbox';
                $dataType='integer';
                break;
            case 'SMALLINT':
            case 'BIGINT':
            case 'INT':
                $inputType='integer';
                $dataType='integer';
                break;
            case 'DATETIME':
                $inputType='datetime';
                $dataType='datetime';
                break;
            case 'TIMESTAMP':
                $inputType='timestamp';
                $dataType='timestamp';
                break;
            case 'DATE':
                $inputType='date';
                $dataType='integer';
                break;
            case 'REAL':
            case 'DOUBLE':
            case 'DOUBLE PRECISION':
            case 'FLOAT':
            case 'DECIMAL':
                $inputType='currency';
                $dataType='float';
                break;
            default:
                break;
        }
        return [$inputType, $dataType];
    }

    public function createFields($moduleId, $fields){
        $cnt=0;
        foreach($fields as $field)
        {
            $data=[
                'name'          => $field['name'],
                'label'         => ucwords(preg_replace('/([^A-Z])([A-Z])/', "$1 $2", $field['name'])),
                'module_id'     => $moduleId,
            ];

            [$data['input_type'], $data['data_type']]=$this->getInputType($field);


            if($data['input_type']=='decimal'){
                $data['decimal_places']=$field['decimals'];
            }

            if(isset($field['length'])){
                $data['field_length']=$field['length'];
            }

            if($field['type']=='CHAR'){
                $data['field_length']=1;
            }


            if($field['type']=='VARCHAR'){
                $data['validation']='max:'.$field['length'];
            }
            if(isset($data['required'])){

                if(strlen($data['validation']) > 0){
                    $data['validation'].='|';
                }
                $data['validation'].='require';
            }

            if(!in_array(strtolower($field['name']), $this->excludeFields)) {
                Field::insert(Field::getField($data, $cnt++));
            }
        }

    }

    public function createRelationship($module_id, $name, $data){

        foreach($data as $item){
            $item['ref_table']=$this->prefix.$item['ref_table'];
            if($item['type'] == 'FOREIGN'){
                if($item['ref_table'] != $name) {

                    $relationship_id=Relationship::insertGetId([
                        'name' => strtolower($name) . '_' . strtolower($item['ref_table']),
                        'modules' => implode(",", [
                            $module_id,
                            Module::where('name', strtolower($item['ref_table']))->first()->id,
                        ]),
                        'related_field_types' => 'integer,integer',
                    ]);
                    return [$relationship_id, $name, $item['ref_table']];
                 }
            }
        }
    }

    public function createModuleSubpanels($moduleId, $relationshipId, $primary_name, $secondary_name, $fields){

        $id=ModuleSubpanel::insertGetId([
            'name' => $primary_name . '_' . $secondary_name,
            'label' => 'Contacts',
            'relationship_id' => $relationshipId,
            'module_id' => $moduleId
        ]);
        foreach($fields as $field){
            SubpanelField::insert([
                'subpanel_id' => $id,
                'field_id' => $field->id
            ]);
        }
    }

    public function createModuleGroup($name, $label)  {
        return ModuleGroup::insertGetId([
            'name' => strtolower($name),
            'label' => ucwords(preg_replace('/([^A-Z])([A-Z])/', "$1 $2",
                str_replace("_", " ", $label)
            )),
            'view_order' => 0,
        ]);
    }
    public function createModule($name, $label, $order, $group_id) {
        return Module::insertGetId([
            'name' => strtolower($name),
            'label' => ucwords(preg_replace('/([^A-Z])([A-Z])/', "$1 $2", $label)),
            'description' => ucwords(preg_replace('/([^A-Z])([A-Z])/', "$1 $2",
                    str_replace("_", " ", $label)
                )) .
                ' module',
            'view_order' => $order,
            'module_group_id' => $group_id,
            'icon' => $this->getIcon($order),
            'status' => 1,
        ]);

    }

    public function getRemoteDatabase2($data=null){
        $name='iceburg';
        Config::set('database.connections.mysql.database', $name);
        DB::reconnect();
        $returnData=[];
        $data=DB::select("select * from information_schema.columns where table_schema='iceburg' order by table_name, ordinal_position");

        $database=[];
        foreach($data as $row){

            $database[$row->TABLE_NAME]['name']=$row->TABLE_NAME;

            $item=[
                'name' => $row->COLUMN_NAME,
                'type' => $row->DATA_TYPE,
            ];
            if(!strpos($row->DATA_TYPE, 'text')){
                $item['length']=$row->NUMERIC_PRECISION > 0 ? $row->NUMERIC_PRECISION : $row->CHARACTER_MAXIMUM_LENGTH;
            }
            if($row->DATA_TYPE == 'decimal')
            {
                $values=explode(",", str_replace(['decimal('], ')', strtolower($row->COLUMN_TYPE)));
                if(isset($values[1])){
                    $item['decimals']=$values[1];
                }
            }
            $database[$row->TABLE_NAME]['fields'][]=$item;

        }

          //  return $database;
         Config::set('database.connections.mysql.database', env('DB_DATABASE'));
         DB::reconnect('mysql');

        return $database;

    }

    public function getRemoteDatabase($data=null){
        $name='iceburg';
        Config::set('database.connections.mysql.database', $name);
        DB::reconnect();
        $returnData=[];
        $data=DB::select("select * from information_schema.columns where table_schema='iceburg' order by table_name, ordinal_position");

        $database=[];
        foreach($data as $row){

            $database[$row->TABLE_NAME]['name']=$row->TABLE_NAME;

            $item=[
                'name' => $row->COLUMN_NAME,
                'type' => $row->DATA_TYPE,
                ];
            if(!strpos($row->DATA_TYPE, 'text')){
                $item['length']=$row->NUMERIC_PRECISION > 0 ? $row->NUMERIC_PRECISION : $row->CHARACTER_MAXIMUM_LENGTH;
            }
            if($row->DATA_TYPE == 'decimal')
            {
                $values=explode(",", str_replace(['decimal('], ')', strtolower($row->COLUMN_TYPE)));
                if(isset($values[1])){
                    $item['decimals']=$values[1];
                }
            }
            $database[$row->TABLE_NAME]['fields'][]=$item;

        }

       // Config::set('database.connections.mysql.database', env('DB_DATABASE'));
       // DB::reconnect('mysql');
        return response()->json([
           $database
        ]);
       // return $database;

    }

    public function copyBaseDatabases($schemaName, $type='base', $connection=[]){


        $tables=$this->getTableNames('defaultcrm_' . $type);
        if(count($connection) > 0){

        }
        foreach($tables as $table)
        {
            DB::statement("CREATE TABLE $schemaName." . $table . " LIKE defaultcrm_" . $type . "." . $table);
            if($table == 'roles')
                DB::statement("INSERT INTO $schemaName." . $table . " SELECT * FROM defaultcrm_" . $type . "." . $table);
        }

    }
    public function processSQL($parserTables, $schemaName, $seedAmount=5, $generateTable=1, $connection=[])  {

       // dd($parserTables);

        $this->copyBaseDatabases($schemaName, 'base', $connection);
        Config::set('database.connections.mysql.database', $schemaName);
        DB::reconnect();
       // dd(Config::get('database'));

      //  $this->createBaseTables();
        $cnt=0;
        $module_group_id=0;
        foreach($parserTables as $key => $table)
        {
            $table['label']=$table['name'];
            $table['name']=$this->prefix.$table['name'];
            if($cnt==0 || ($cnt <= 12 && $cnt % 3 == 1) )
            {
                $module_group_id=$this->createModuleGroup($table['name'], $table['label']);
            }
            elseif($cnt > 12){
                $module_group_id=6;
            }


            $module_id=$this->createModule($table['name'], $table['label'], $cnt, $module_group_id);
            $this->createFields($module_id, $table['fields']);
            $cnt++;
        }

        foreach($parserTables as $key => $table)
        {
            $table['name']=$this->prefix.$table['name'];
            $module=Module::where('name', $table['name'])->first();
            if(isset($table['indexes'])){
                [$relationship_id, $primary_name, $secondary_name]=$this->createRelationship($module->id, $module->name, $table['indexes']);
                $fields=Field::where('module_id', Module::where('name', $secondary_name)->value('id'))->take(3)->get();
                if(intval($relationship_id) > 0){
                    $this->createModuleSubpanels($module->id, $relationship_id, $primary_name, $secondary_name, $fields);
                }

            }
        }


        $relationship = new Relationship();
        $relationship->generate($seedAmount);





        $this->AddUsers();
        $this->AddSettings();

        $this->addDataletTypes();
        $this->addDatalets();


        if($generateTable==1){
            $module=new Module();
            $module->generate($seedAmount);
        }

/*
       // dd(Module::all()->toArray());
        $table=$this->prefix.'roles';
        $data=$this->roles();
        foreach($data as $row) {
            $row['slug']=bin2hex(random_bytes(16));
            DB::table($table)->insert($row);
        }
*/

        $this->createBaseTables();
        $this->addModulesAndRoles();
       // $this->sampleMedia();



        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect();
        return true;
    }

    public function createPredefinedDatabase($schemaName, $type='iceburg') : bool
    {
        $tables=$this->getTableNames('defaultcrm_'.$type);
        foreach($tables as $table)
        {
            DB::statement("CREATE TABLE $schemaName." . $table . " LIKE defaultcrm_" . $type . "." . $table);
            DB::statement("INSERT INTO $schemaName." . $table . " SELECT * FROM defaultcrm_" . $type . "." . $table);
        }
        return true;
    }

    public function createDatabase($name='') : string
    {
        $schemaName="user_" . strtolower(Str::random(8));
        if(strlen($name) > 0)
        {
            $schemaName=$name;
        }
        DB::statement('CREATE DATABASE ' . $schemaName);
        if(strlen($name) < 1){
            Database::insert([
                'name' => $schemaName,
                'user_id' => auth()->user()->id ?? 0
            ]);
        }

        return $schemaName;
    }

    public function index(){
        $status=false;
      // $this->deleteDatabases();
        $parserData=[];
      //  $parserData2=[];

        $schemaNameExternal=[];
      //  $parserData2=$this->getRemoteDatabase2();
       //$parserData=$this->getRemoteDatabase2();


      //  $sql=file_get_contents('http://demo.iceburg.ca/seed/mysqlsampledatabase.sql');
        //$parserData=json_decode(file_get_contents('http://localhost:8001/remote'));
       // $data=json_decode($this->data_file(), true);
        //$parserData=$data[0];
       // $parserData=$data[0];
       // dd($parserData);

        //  $this->processSQL($sql, 'test');
       // $this->deleteDatabases();



        if(isset($schemaNameExternal['name']) > 0){
            $schemaName=$schemaNameExternal['name'];
        }
        else {
            $schemaName=$this->createDatabase('defaultcrm_base');
        }


       //$status=$this->createPredefinedDatabase($schemaName);

        print $schemaName;
        //exit;
        if(count($parserData) > 0 && count($schemaNameExternal) > 0){
                //  print_r($parserData);
                //exit;
            $status=$this->processSQL($parserData2, $schemaName, 0, 0, $schemaNameExternal);
        }
        elseif(count($parserData) > 0){
          //  print_r($parserData);
            //exit;
            $status=$this->processSQL($parserData, $schemaName);
        }
        elseif(strlen($sql) > 0){
            $parser = new SQLParser();
            $parser->parse($sql);
            //print_r($parser->tables);

            $status=$this->processSQL($parser->tables, $schemaName);
        }

        else {
            $status=$this->createPredefinedDatabase($schemaName);
        }

        dd([
            'name' => $schemaName,
            'status' => $status
        ]);

        return response()->json([
            'name' => $schemaName,
            'status' => $status
        ]);
    }

    private function deleteDatabases(){
        Database::all()->each(function ($db){
            DB::statement("DROP DATABASE " . $db->name);
        });
        DB::statement('truncate `databases`');
        //DB::statement("DROP DATABASE " . $name);
    }

    private function getTableNames($name='iceburg')
    {
        $tableNames=[];
        Config::set('database.connections.mysql.database', $name);
        DB::reconnect();
        $tables = DB::select('SHOW Tables');
        Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        DB::reconnect();
        foreach($tables as $table)
        {
            $tableNames[]=$table->{'Tables_in_'.$name};
        }

        return $tableNames;
    }

    private function onTheFlyConnection($name){
        config(
        [
            'database.connections.fly' =>
            [
                'driver'    => 'mysql',
                'host'      => 'localhost',
                'port'      => 3306,
                'database'  => $name,
                'username'  => env('DB_USERNAME'),
                'password'  => env('DB_PASSWORD'),
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
        ]);
        DB::reconnect("fly");
    }

    private function addModulesAndRoles()
    {
        $module = Module::where('name', 'roles')->first();
        $records=DB::table($module->name)->get();
        Permission::truncate();
        foreach($records as $record)
        {
            Module::all()->each(function ($module) use ($record) {
                Permission::insert([
                    'role_id' => $record->id,
                    'module_id' => $module->id
                ]);
            });
        }
    }

    private function addDataletTypes()
    {
        DataletType::truncate();
        DataletType::insert([
            ['id' => 1, 'name' => 'Doughnut Chart'],
            ['id' => 2, 'name' => 'Line Chart'],
            ['id' => 3, 'name' => 'Bar Graph'],
            ['id' => 4, 'name' => 'Pie Chart'],
            ['id' => 5, 'name' => 'Area Chart'],
            ['id' => 6, 'name' => 'Latest Campaign'],
            ['id' => 7, 'name' => 'Latest Meetings'],
        ]);
    }

    private function addDataLets()
    {
        Datalet::truncate();
        Datalet::insert([

            ['type'=>1,
                'module_id'=>0,
                'label' => 'Total Sales',
                'size' => 12,
                'display_order' => 1],
            ['type'=>2,
                'module_id'=>0,
                'label' => 'Number of new Leads / Contacts / Accounts over the last 7 Days',
                'size' => 12,
                'display_order' => 1],
            ['type'=>3,
                'module_id'=>0,
                'label' => 'Meetings',
                'size' => 12,
                'display_order' => 1],
            ['type'=>4,
                'module_id'=>0,
                'label' => 'Number of new Opportunities / Quotes / Contracts over the last 7 Days',
                'size' => 12,
                'display_order' => 1],
            ['type'=>1,
                'module_id'=>0,
                'label' => 'Orders This Month',
                'size' => 12,
                'display_order' => 1],
        ]);

    }

    private function addWorkflowActions()
    {
        WorkflowAction::truncate();
        WorkflowAction::insert(
            [
                ['name' => 'Insert new Module Record'],
                ['name' => 'Insert new Relationship Record'],
                ['name' => 'Update Module Record'],
                ['name' => 'Update Relationship Record'],
                ['name' => 'Delete Module Record'],
                ['name' => 'Delete Relationship Record'],
                ['name' => 'Field Change Status']
            ]
        );
    }

    private function AddUsers()
    {
        User::truncate();
        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('admin'),
            'role_id' => 1
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'User',
            'email' => 'user@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('user'),
            'role_id' => 2
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'Sales',
            'email' => 'sales@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('sales'),
            'role_id' => 3
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'Accounting',
            'email' => 'accounting@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('accounting'),
            'role_id' => 4
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'Marketing',
            'email' => 'marketing@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('marketing'),
            'role_id' => 5
        ]);
    }

    private function AddSettings()
    {

        DB::table('settings')->insert([
            'name' => 'theme',
            'value' => 'light'
        ]);

        DB::table('settings')->insert([
            'name' => 'search_per_page',
            'value' => '10'
        ]);

        DB::table('settings')->insert([
            'name' => 'submodule_search_per_page',
            'value' => '10'
        ]);

        DB::table('settings')->insert([
            'name' => 'title',
            'value' => 'Iceburg CRM'
        ]);

        DB::table('settings')->insert([
            'name' => 'description',
            'value' => 'Open Source, data driven, extendable, unlimited relationships, convertable modules, 29 default themes, light/dark themes'
        ]);

        DB::table('settings')->insert([
            'name' => 'max_export_records',
            'value' => 10000
        ]);



    }

    private function Roles()
    {
        return [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'User'],
            ['id' => 3, 'name' => 'Sales'],
            ['id' => 4, 'name' => 'Accounting'],
            ['id' => 5, 'name' => 'Support'],
            ['id' => 6, 'name' => 'Marketing'],
            ['id' => 7, 'name' => 'HR'],
        ];
    }

    private function createBaseTables(){
        //Schema::drop('roles');
     //  DB::statement('create table roles (name varchar(200))');
        $moduleId=Module::insertGetId([
            'name' => 'roles',
            'label' => 'Roles',
            'description' => 'Roles',
            'view_order' => 0,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1
        ]);

        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], 0));

    }

    private function data_file(){
        return '[{"account_status":{"name":"account_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"accounts":{"name":"accounts","fields":[{"name":"company_logo","type":"mediumtext"},{"name":"name","type":"varchar","length":255},{"name":"first_name","type":"varchar","length":255},{"name":"last_name","type":"varchar","length":255},{"name":"color","type":"varchar","length":255},{"name":"email","type":"varchar","length":255},{"name":"phone","type":"varchar","length":255},{"name":"fax","type":"varchar","length":255},{"name":"website","type":"varchar","length":255},{"name":"address","type":"varchar","length":255},{"name":"city","type":"varchar","length":255},{"name":"zip","type":"varchar","length":255},{"name":"state","type":"int","length":10},{"name":"country","type":"int","length":10},{"name":"description","type":"varchar","length":255},{"name":"status","type":"int","length":10},{"name":"assigned_to","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"accounts_cases":{"name":"accounts_cases","fields":[{"name":"id","type":"bigint","length":20},{"name":"accounts_id","type":"int","length":10},{"name":"cases_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"accounts_contacts":{"name":"accounts_contacts","fields":[{"name":"id","type":"bigint","length":20},{"name":"accounts_id","type":"int","length":10},{"name":"contacts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"accounts_contracts":{"name":"accounts_contracts","fields":[{"name":"id","type":"bigint","length":20},{"name":"accounts_id","type":"int","length":10},{"name":"contracts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"accounts_invoices":{"name":"accounts_invoices","fields":[{"name":"id","type":"bigint","length":20},{"name":"accounts_id","type":"int","length":10},{"name":"invoices_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"accounts_invoices_users":{"name":"accounts_invoices_users","fields":[{"name":"id","type":"bigint","length":20},{"name":"accounts_id","type":"int","length":10},{"name":"invoices_id","type":"int","length":10},{"name":"users_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"accounts_meetings":{"name":"accounts_meetings","fields":[{"name":"id","type":"bigint","length":20},{"name":"accounts_id","type":"int","length":10},{"name":"meetings_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"accounts_opportunities":{"name":"accounts_opportunities","fields":[{"name":"id","type":"bigint","length":20},{"name":"accounts_id","type":"int","length":10},{"name":"opportunities_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"campaign_status":{"name":"campaign_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"campaign_types":{"name":"campaign_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"campaigns":{"name":"campaigns","fields":[{"name":"name","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"budget","type":"double","length":22},{"name":"forecast","type":"double","length":22},{"name":"actual","type":"double","length":22},{"name":"impressions","type":"int","length":10},{"name":"currency","type":"int","length":10},{"name":"campaign_type","type":"int","length":10},{"name":"creative","type":"mediumtext"},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"campaigns_accounts":{"name":"campaigns_accounts","fields":[{"name":"id","type":"bigint","length":20},{"name":"campaigns_id","type":"int","length":10},{"name":"accounts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"campaigns_tasks":{"name":"campaigns_tasks","fields":[{"name":"id","type":"bigint","length":20},{"name":"campaigns_id","type":"int","length":10},{"name":"tasks_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"case_priorities":{"name":"case_priorities","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"case_status":{"name":"case_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"case_types":{"name":"case_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"cases":{"name":"cases","fields":[{"name":"subject","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"case_number","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"priority","type":"int","length":10},{"name":"type","type":"int","length":10},{"name":"resolution","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"contacts":{"name":"contacts","fields":[{"name":"profile_pic","type":"mediumtext"},{"name":"first_name","type":"varchar","length":255},{"name":"last_name","type":"varchar","length":255},{"name":"email","type":"varchar","length":255},{"name":"phone","type":"varchar","length":255},{"name":"fax","type":"varchar","length":255},{"name":"website","type":"varchar","length":255},{"name":"address","type":"varchar","length":255},{"name":"city","type":"varchar","length":255},{"name":"state","type":"int","length":10},{"name":"zip","type":"varchar","length":255},{"name":"country","type":"int","length":10},{"name":"description","type":"varchar","length":255},{"name":"status","type":"int","length":10},{"name":"email_receive","type":"tinyint","length":3},{"name":"assigned_to","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"contract_status":{"name":"contract_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"contract_types":{"name":"contract_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"contracts":{"name":"contracts","fields":[{"name":"name","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"discount","type":"double","length":22},{"name":"taxes","type":"double","length":22},{"name":"shipping","type":"double","length":22},{"name":"subtotal","type":"double","length":22},{"name":"total","type":"double","length":22},{"name":"currency","type":"int","length":10},{"name":"signed_by","type":"int","length":10},{"name":"assigned_to","type":"int","length":10},{"name":"contract_type","type":"int","length":10},{"name":"start_date","type":"int","length":10},{"name":"end_date","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"contracts_lineitems":{"name":"contracts_lineitems","fields":[{"name":"id","type":"bigint","length":20},{"name":"contracts_id","type":"int","length":10},{"name":"lineitems_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"countries":{"name":"countries","fields":[{"name":"code","type":"varchar","length":255},{"name":"name","type":"varchar","length":255},{"name":"flag","type":"mediumtext"},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"currency":{"name":"currency","fields":[{"name":"name","type":"varchar","length":255},{"name":"code","type":"varchar","length":255},{"name":"symbol","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"datalet_types":{"name":"datalet_types","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":255},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"datalets":{"name":"datalets","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":255},{"name":"label","type":"varchar","length":255},{"name":"type","type":"int","length":10},{"name":"role_id","type":"int","length":10},{"name":"field_id","type":"int","length":10},{"name":"module_id","type":"int","length":10},{"name":"relationship_id","type":"int","length":10},{"name":"size","type":"int","length":10},{"name":"display_order","type":"int","length":10},{"name":"active","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"discount_types":{"name":"discount_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"document_status":{"name":"document_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"document_types":{"name":"document_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"documents":{"name":"documents","fields":[{"name":"name","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"file_link","type":"varchar","length":255},{"name":"document_type","type":"int","length":10},{"name":"document_status","type":"int","length":10},{"name":"expire_date","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"documents_accounts":{"name":"documents_accounts","fields":[{"name":"id","type":"bigint","length":20},{"name":"documents_id","type":"int","length":10},{"name":"accounts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"documents_cases":{"name":"documents_cases","fields":[{"name":"id","type":"bigint","length":20},{"name":"documents_id","type":"int","length":10},{"name":"cases_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"documents_contracts":{"name":"documents_contracts","fields":[{"name":"id","type":"bigint","length":20},{"name":"documents_id","type":"int","length":10},{"name":"contracts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"documents_meetings":{"name":"documents_meetings","fields":[{"name":"id","type":"bigint","length":20},{"name":"documents_id","type":"int","length":10},{"name":"meetings_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"documents_opportunities":{"name":"documents_opportunities","fields":[{"name":"id","type":"bigint","length":20},{"name":"documents_id","type":"int","length":10},{"name":"opportunities_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"documents_tasks":{"name":"documents_tasks","fields":[{"name":"id","type":"bigint","length":20},{"name":"documents_id","type":"int","length":10},{"name":"tasks_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"documents_users":{"name":"documents_users","fields":[{"name":"id","type":"bigint","length":20},{"name":"documents_id","type":"int","length":10},{"name":"users_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"failed_jobs":{"name":"failed_jobs","fields":[{"name":"id","type":"bigint","length":20},{"name":"uuid","type":"varchar","length":255},{"name":"connection","type":"text","length":65535},{"name":"queue","type":"text","length":65535},{"name":"payload","type":"longtext"},{"name":"exception","type":"longtext"},{"name":"failed_at","type":"timestamp","length":null}]},"fields":{"name":"fields","fields":[{"name":"name","type":"varchar","length":245},{"name":"label","type":"varchar","length":245},{"name":"module_id","type":"int","length":10},{"name":"validation","type":"varchar","length":245},{"name":"input_type","type":"varchar","length":245},{"name":"data_type","type":"varchar","length":100},{"name":"field_length","type":"int","length":10},{"name":"required","type":"int","length":10},{"name":"is_nullable","type":"tinyint","length":3},{"name":"default_value","type":"varchar","length":245},{"name":"read_only","type":"tinyint","length":3},{"name":"related_module_id","type":"int","length":10},{"name":"related_field_id","type":"varchar","length":255},{"name":"related_value_id","type":"varchar","length":255},{"name":"decimal_places","type":"int","length":10},{"name":"status","type":"tinyint","length":3},{"name":"id","type":"bigint","length":20},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"group_types":{"name":"group_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"groups":{"name":"groups","fields":[{"name":"name","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"groups_accounts":{"name":"groups_accounts","fields":[{"name":"id","type":"bigint","length":20},{"name":"groups_id","type":"int","length":10},{"name":"accounts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"input_masks":{"name":"input_masks","fields":[{"name":"id","type":"bigint","length":20},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"input_types":{"name":"input_types","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":128},{"name":"mask","type":"varchar","length":128},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"invoice_status":{"name":"invoice_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"invoices":{"name":"invoices","fields":[{"name":"name","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"currency","type":"int","length":10},{"name":"amount","type":"double","length":22},{"name":"tax","type":"double","length":22},{"name":"total","type":"double","length":22},{"name":"subtotal","type":"double","length":22},{"name":"discount","type":"double","length":22},{"name":"billing_address","type":"varchar","length":255},{"name":"billing_city","type":"varchar","length":255},{"name":"billing_zip","type":"varchar","length":255},{"name":"billing_state","type":"int","length":10},{"name":"billing_country","type":"int","length":10},{"name":"shipping_address","type":"varchar","length":255},{"name":"shipping_city","type":"varchar","length":255},{"name":"shipping_zip","type":"varchar","length":255},{"name":"shipping_state","type":"int","length":10},{"name":"shipping_country","type":"int","length":10},{"name":"sign_date","type":"int","length":10},{"name":"expire_date","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"lead_sources":{"name":"lead_sources","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"lead_status":{"name":"lead_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"lead_types":{"name":"lead_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"leads":{"name":"leads","fields":[{"name":"first_name","type":"varchar","length":255},{"name":"last_name","type":"varchar","length":255},{"name":"email","type":"varchar","length":255},{"name":"phone","type":"varchar","length":255},{"name":"fax","type":"varchar","length":255},{"name":"website","type":"varchar","length":255},{"name":"address","type":"varchar","length":255},{"name":"city","type":"varchar","length":255},{"name":"state","type":"int","length":10},{"name":"zip","type":"varchar","length":255},{"name":"country","type":"int","length":10},{"name":"description","type":"varchar","length":255},{"name":"status","type":"int","length":10},{"name":"email_receive","type":"tinyint","length":3},{"name":"assigned_to","type":"int","length":10},{"name":"lead_type","type":"int","length":10},{"name":"lead_source","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"leads_accounts_opportunities":{"name":"leads_accounts_opportunities","fields":[{"name":"id","type":"bigint","length":20},{"name":"leads_id","type":"int","length":10},{"name":"accounts_id","type":"int","length":10},{"name":"opportunities_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"lineitems":{"name":"lineitems","fields":[{"name":"product_id","type":"int","length":10},{"name":"quantity","type":"int","length":10},{"name":"price","type":"double","length":22},{"name":"unit_price","type":"double","length":22},{"name":"cost","type":"double","length":22},{"name":"discount","type":"double","length":22},{"name":"discount_type","type":"int","length":10},{"name":"taxes","type":"double","length":22},{"name":"gross","type":"double","length":22},{"name":"net","type":"double","length":22},{"name":"description","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"logs":{"name":"logs","fields":[{"name":"id","type":"bigint","length":20},{"name":"module_id","type":"int","length":10},{"name":"type","type":"varchar","length":16},{"name":"message","type":"varchar","length":200},{"name":"user_id","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"meeting_status":{"name":"meeting_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"meeting_types":{"name":"meeting_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"meetings":{"name":"meetings","fields":[{"name":"name","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"start_date","type":"int","length":10},{"name":"end_date","type":"int","length":10},{"name":"start_time","type":"int","length":10},{"name":"end_time","type":"int","length":10},{"name":"reminder_time","type":"int","length":10},{"name":"location","type":"varchar","length":255},{"name":"phone","type":"varchar","length":255},{"name":"link","type":"varchar","length":255},{"name":"meeting_password","type":"varchar","length":255},{"name":"video_recording","type":"mediumtext"},{"name":"audio_recording","type":"mediumtext"},{"name":"contract","type":"int","length":10},{"name":"types","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"assigned_to","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"migrations":{"name":"migrations","fields":[{"name":"id","type":"int","length":10},{"name":"migration","type":"varchar","length":255},{"name":"batch","type":"int","length":10}]},"module_convertables":{"name":"module_convertables","fields":[{"name":"id","type":"bigint","length":20},{"name":"primary_module_id","type":"int","length":10},{"name":"module_id","type":"int","length":10},{"name":"level","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"module_groups":{"name":"module_groups","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":245},{"name":"label","type":"varchar","length":245},{"name":"view_order","type":"int","length":10}]},"module_subpanels":{"name":"module_subpanels","fields":[{"name":"id","type":"bigint","length":20},{"name":"subpanel_filter","type":"varchar","length":255},{"name":"name","type":"varchar","length":245},{"name":"label","type":"varchar","length":245},{"name":"module_id","type":"varchar","length":255},{"name":"list_size","type":"int","length":10},{"name":"list_order_column","type":"varchar","length":255},{"name":"list_order","type":"varchar","length":255},{"name":"relationship_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"saved_search_id","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"modules":{"name":"modules","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":100},{"name":"label","type":"varchar","length":255},{"name":"description","type":"varchar","length":245},{"name":"status","type":"int","length":10},{"name":"faker_seed","type":"int","length":10},{"name":"create_table","type":"int","length":10},{"name":"view_order","type":"int","length":10},{"name":"admin","type":"int","length":10},{"name":"parent_id","type":"int","length":10},{"name":"primary","type":"int","length":10},{"name":"icon","type":"varchar","length":128},{"name":"module_group_id","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"modules_datalets":{"name":"modules_datalets","fields":[{"name":"id","type":"bigint","length":20},{"name":"modules_id","type":"int","length":10},{"name":"datalets_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"modules_fields":{"name":"modules_fields","fields":[{"name":"id","type":"bigint","length":20},{"name":"modules_id","type":"int","length":10},{"name":"fields_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"modules_subpanels":{"name":"modules_subpanels","fields":[{"name":"id","type":"bigint","length":20},{"name":"modules_id","type":"int","length":10},{"name":"module_subpanels_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"notes":{"name":"notes","fields":[{"name":"subject","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"notes_accounts":{"name":"notes_accounts","fields":[{"name":"id","type":"bigint","length":20},{"name":"notes_id","type":"int","length":10},{"name":"accounts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"notes_cases":{"name":"notes_cases","fields":[{"name":"id","type":"bigint","length":20},{"name":"documents_id","type":"int","length":10},{"name":"cases_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"notes_contracts":{"name":"notes_contracts","fields":[{"name":"id","type":"bigint","length":20},{"name":"notes_id","type":"int","length":10},{"name":"contracts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"notes_meetings":{"name":"notes_meetings","fields":[{"name":"id","type":"bigint","length":20},{"name":"notes_id","type":"int","length":10},{"name":"meetings_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"notes_opportunities":{"name":"notes_opportunities","fields":[{"name":"id","type":"bigint","length":20},{"name":"notes_id","type":"int","length":10},{"name":"opportunities_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"notes_tasks":{"name":"notes_tasks","fields":[{"name":"id","type":"bigint","length":20},{"name":"notes_id","type":"int","length":10},{"name":"tasks_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"notes_users":{"name":"notes_users","fields":[{"name":"id","type":"bigint","length":20},{"name":"notes_id","type":"int","length":10},{"name":"users_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunities":{"name":"opportunities","fields":[{"name":"name","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"type","type":"int","length":10},{"name":"amount","type":"double","length":22},{"name":"probability","type":"int","length":10},{"name":"close_date","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunities_cases":{"name":"opportunities_cases","fields":[{"name":"id","type":"bigint","length":20},{"name":"opportunities_id","type":"int","length":10},{"name":"cases_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunities_contacts":{"name":"opportunities_contacts","fields":[{"name":"id","type":"bigint","length":20},{"name":"accounts_id","type":"int","length":10},{"name":"opportunities_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunities_contracts":{"name":"opportunities_contracts","fields":[{"name":"id","type":"bigint","length":20},{"name":"opportunities_id","type":"int","length":10},{"name":"contracts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunities_meetings":{"name":"opportunities_meetings","fields":[{"name":"id","type":"bigint","length":20},{"name":"opportunities_id","type":"int","length":10},{"name":"meetings_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunities_quotes":{"name":"opportunities_quotes","fields":[{"name":"id","type":"bigint","length":20},{"name":"opportunities_id","type":"int","length":10},{"name":"quotes_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunities_quotes_accounts":{"name":"opportunities_quotes_accounts","fields":[{"name":"id","type":"bigint","length":20},{"name":"opportunities_id","type":"int","length":10},{"name":"quotes_id","type":"int","length":10},{"name":"accounts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunity_status":{"name":"opportunity_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"opportunity_types":{"name":"opportunity_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"orders":{"name":"orders","fields":[{"name":"first_name","type":"varchar","length":255},{"name":"last_name","type":"varchar","length":255},{"name":"email","type":"varchar","length":255},{"name":"phone","type":"varchar","length":255},{"name":"status","type":"int","length":10},{"name":"currency","type":"int","length":10},{"name":"amount","type":"double","length":22},{"name":"tax","type":"double","length":22},{"name":"total","type":"double","length":22},{"name":"subtotal","type":"double","length":22},{"name":"discount","type":"double","length":22},{"name":"billing_address","type":"varchar","length":255},{"name":"billing_city","type":"varchar","length":255},{"name":"billing_zip","type":"varchar","length":255},{"name":"billing_state","type":"int","length":10},{"name":"billing_country","type":"int","length":10},{"name":"shipping_address","type":"varchar","length":255},{"name":"shipping_city","type":"varchar","length":255},{"name":"shipping_zip","type":"varchar","length":255},{"name":"shipping_state","type":"int","length":10},{"name":"shipping_country","type":"int","length":10},{"name":"product","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"password_resets":{"name":"password_resets","fields":[{"name":"email","type":"varchar","length":255},{"name":"token","type":"varchar","length":255},{"name":"created_at","type":"timestamp","length":null}]},"permissions":{"name":"permissions","fields":[{"name":"id","type":"bigint","length":20},{"name":"module_id","type":"int","length":10},{"name":"role_id","type":"int","length":10},{"name":"can_read","type":"int","length":10},{"name":"can_write","type":"int","length":10},{"name":"can_delete","type":"int","length":10},{"name":"can_export","type":"int","length":10},{"name":"can_import","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"personal_access_tokens":{"name":"personal_access_tokens","fields":[{"name":"id","type":"bigint","length":20},{"name":"tokenable_type","type":"varchar","length":255},{"name":"tokenable_id","type":"bigint","length":20},{"name":"name","type":"varchar","length":255},{"name":"token","type":"varchar","length":64},{"name":"abilities","type":"text","length":65535},{"name":"last_used_at","type":"timestamp","length":null},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"products":{"name":"products","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"project_priorities":{"name":"project_priorities","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"project_status":{"name":"project_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"project_types":{"name":"project_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"projects":{"name":"projects","fields":[{"name":"name","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"priority","type":"int","length":10},{"name":"type","type":"int","length":10},{"name":"start_date","type":"int","length":10},{"name":"end_date","type":"int","length":10},{"name":"due_date","type":"int","length":10},{"name":"completed_date","type":"int","length":10},{"name":"estimated_hours","type":"int","length":10},{"name":"actual_hours","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"projects_accounts":{"name":"projects_accounts","fields":[{"name":"id","type":"bigint","length":20},{"name":"projects_id","type":"int","length":10},{"name":"accounts_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"projects_tasks":{"name":"projects_tasks","fields":[{"name":"id","type":"bigint","length":20},{"name":"projects_id","type":"int","length":10},{"name":"tasks_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"quote_status":{"name":"quote_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"quotes":{"name":"quotes","fields":[{"name":"name","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"currency","type":"int","length":10},{"name":"amount","type":"double","length":22},{"name":"tax","type":"double","length":22},{"name":"total","type":"double","length":22},{"name":"subtotal","type":"double","length":22},{"name":"discount","type":"double","length":22},{"name":"billing_address","type":"varchar","length":255},{"name":"billing_city","type":"varchar","length":255},{"name":"billing_zip","type":"varchar","length":255},{"name":"billing_state","type":"int","length":10},{"name":"billing_country","type":"int","length":10},{"name":"shipping_address","type":"varchar","length":255},{"name":"shipping_city","type":"varchar","length":255},{"name":"shipping_zip","type":"varchar","length":255},{"name":"shipping_state","type":"int","length":10},{"name":"shipping_country","type":"int","length":10},{"name":"expire_date","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"relationship_modules":{"name":"relationship_modules","fields":[{"name":"id","type":"bigint","length":20},{"name":"module_id","type":"int","length":10},{"name":"relationship_id","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"relationships":{"name":"relationships","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":245},{"name":"modules","type":"varchar","length":255},{"name":"related_field_types","type":"varchar","length":255},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"roles":{"name":"roles","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"settings":{"name":"settings","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":255},{"name":"value","type":"varchar","length":255},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"states":{"name":"states","fields":[{"name":"code","type":"varchar","length":255},{"name":"abbreviation","type":"varchar","length":255},{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"subpanel_fields":{"name":"subpanel_fields","fields":[{"name":"id","type":"bigint","length":20},{"name":"field_id","type":"int","length":10},{"name":"subpanel_id","type":"int","length":10},{"name":"label","type":"varchar","length":255},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"task_priorities":{"name":"task_priorities","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"task_status":{"name":"task_status","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"task_types":{"name":"task_types","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"tasks":{"name":"tasks","fields":[{"name":"subject","type":"varchar","length":255},{"name":"description","type":"varchar","length":255},{"name":"assigned_to","type":"int","length":10},{"name":"task_types","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"task_priority","type":"int","length":10},{"name":"due_date","type":"int","length":10},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"themes":{"name":"themes","fields":[{"name":"name","type":"varchar","length":255},{"name":"id","type":"bigint","length":20},{"name":"slug","type":"varchar","length":64},{"name":"soft_delete","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"user_meetings":{"name":"user_meetings","fields":[{"name":"id","type":"bigint","length":20},{"name":"users_id","type":"int","length":10},{"name":"meetings_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"users":{"name":"users","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":255},{"name":"email","type":"varchar","length":255},{"name":"email_verified_at","type":"timestamp","length":null},{"name":"password","type":"varchar","length":255},{"name":"profile_pic","type":"mediumtext"},{"name":"role_id","type":"int","length":10},{"name":"slug","type":"varchar","length":255},{"name":"remember_token","type":"varchar","length":100},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"users_tasks":{"name":"users_tasks","fields":[{"name":"id","type":"bigint","length":20},{"name":"users_id","type":"int","length":10},{"name":"tasks_id","type":"int","length":10},{"name":"status","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"work_flow_data":{"name":"work_flow_data","fields":[{"name":"id","type":"bigint","length":20},{"name":"from_id","type":"int","length":10},{"name":"from_module_id","type":"int","length":10},{"name":"to_id","type":"int","length":10},{"name":"to_module_id","type":"int","length":10},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]},"workflow_actions":{"name":"workflow_actions","fields":[{"name":"id","type":"bigint","length":20},{"name":"name","type":"varchar","length":255},{"name":"created_at","type":"timestamp","length":null},{"name":"updated_at","type":"timestamp","length":null}]}}]';
    }
}
