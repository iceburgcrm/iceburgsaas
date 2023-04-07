

<template>
    <AppLayout title="Create CRM">
        <template #header>
            <div class="grid grid-flow-col auto-cols-auto place-content-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Create a CRM
                </h2>
                <button class="btn btn-primary">Create</button>
            </div>
        </template>

        <div class="py-12 grid justify-items-center">

            <div class="p-10 w-full md:w-3/4  bg-base-200">
           <div class="card w-full bg-base-100 shadow-xl gap-4 text-center">
               <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />

               <div class="card-body w-full items-center text-center">
                        <h2 class="card-title">What kind of CRM?</h2>
                        <p><select v-model="new_crm_data.type" class="input input-bordered select-xl select w-full">
                            <option value="" disabled selected>Select type</option>
                            <option value="default">IceburgCRM</option>
                            <option value="uploadschema">Schema Upload</option>
                            <option value="connection">Connect MySQL Database</option>
                        </select></p>
                        <hr />
                        <div v-if="new_crm_data.type === 'default'">
                            <div class="alert shadow-lg mt-10 mb-10">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Create a CRM based on the IceburgCRM framework.
                                        </span>
                                </div>
                                <div class="flex-none">
                                    <a href="https://www.iceburg.ca" target="_blank" class="btn btn-sm btn-accent text-accent-content">Learn More</a>
                                </div>
                            </div>
                       </div>
                        <div v-if="new_crm_data.type === 'uploadschema'">
                            <div class="alert shadow-lg mt-10">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Upload your own MySQL schema.  Take any MySQL dump and upload it and we will create a crm from it.
                                        </span>
                                </div>
                                <div class="flex-none">
                                    <a href="/mysqlsampledatabase.sql" target="_blank" class="btn btn-sm btn-accent text-accent-content">Sample Schema File</a>
                                    <a href="/MySQL-Sample-Database-Diagram.pdf" target="_blank" class="btn btn-sm btn-grey text-accent-content">Schema Diagram</a>
                                </div>
                            </div>
                            <div class="bg-base-500 mt-10 mb-10 border-primary">
                                </div>
                            <input  type="file" id="file-input" @change="onFileChanged($event)" name="file" class="file-input file-input-primary mb-10 mt-5 w-full max-w-xs" />
                        </div>
                       <div v-if="new_crm_data.type === 'connection'" class="alert shadow-lg mt-10 mb-10">
                           <div>
                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                               <span>Create a CRM from your existing MySQL database. Enter in your connection settings and we'll create a CRM using your databases as modules and database fields as CRM module fields.  <br /><br />The connection information you enter will not be saved
                                            </span>
                           </div>
                       </div>
                        <div v-if="new_crm_data.type === 'connection'" class="bg-base-300 mb-10 p-10 rounded-2xl"><h4 class="text-sm font-semibold mt-5">Connection</h4>



                            <div class="grid grid-flow-col colspan-1 md:colspan-2 gap-4">

                                <div class="ml-5">
                                    <div class="form-control max-w-xs">
                                        <label class="label">
                                            <span class="label-text">Database Name</span>
                                        </label>
                                        <input type="text" v-model="connection.db_name" placeholder="Host ip or dns address" class="input input-bordered w-full max-w-xs" />
                                    </div>
                                    <div class="form-control max-w-xs">
                                        <label class="label">
                                            <span class="label-text">Host</span>
                                        </label>
                                        <input type="text" v-model="connection.host" placeholder="Host ip or dns address" class="input input-bordered w-full max-w-xs" />
                                    </div>
                                    <div class="form-control max-w-xs">
                                        <label class="label">
                                            <span class="label-text">Username</span>
                                        </label>
                                        <input type="text" v-model="connection.username" placeholder="MySQL username" class="input input-bordered w-full max-w-xs" />
                                    </div>
                                    <div class="form-control max-w-xs">
                                        <label class="label">
                                            <span class="label-text">Password</span>
                                        </label>
                                        <input type="password" v-model="connection.password" class="input input-bordered w-full max-w-xs" />
                                    </div>
                                </div>
                                <div>
                                    <div class="form-control max-w-xs">
                                        <label class="label">
                                            <span class="label-text">Port</span>
                                        </label>
                                        <input type="text" v-model="connection.port" placeholder="Port Number" class="input input-bordered w-full max-w-xs" />
                                    </div>
                                    <div class="form-control max-w-xs">
                                        <label class="label">
                                            <span class="label-text">Charset</span>
                                        </label>
                                        <input type="text" v-model="connection.character_set" placeholder="Character Set" class="input input-bordered w-full max-w-xs" />
                                    </div>
                                    <div class="form-control max-w-xs">
                                        <label class="label">
                                            <span class="label-text">Collation</span>
                                        </label>
                                        <input type="text" v-model="connection.collation" placeholder="Select a name for your CRM" class="input input-bordered w-full max-w-xs" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="new_crm_data.type != ''" class="w-full md:w-1/2  p-10 rounded-2xl bg-gray-200"><h4 class="text-sm font-semibold">Options</h4>
                            <div class="form-control max-w-xs">
                                <label class="label">
                                    <span class="label-text">Name</span>
                                </label>
                                <input type="text" v-model="new_crm_data.name" placeholder="Select a name for your CRM" class="input input-bordered w-full max-w-xs" />
                            </div>
                            <div class="form-control max-w-xs">
                                <label class="label">
                                    <span class="label-text">Description</span>
                                </label>
                                <textarea v-model="new_crm_data.description" class="textarea textarea-bordered h-24 input  w-full max-w-xs" placeholder="Set the CRM description.  Will be shown on login page" />
                            </div>
                            <div class="form-control w-full max-w-xs">
                                <label class="label">
                                    <span class="label-text">Theme</span>
                                </label>
                                <select name="theme" v-model="new_crm_data.theme"  class="select select-bordered">
                                    <option v-for="item in $page.props.themes" :value="item.name">{{item.name}}</option>

                                </select>
                            </div>


                        </div>
                   <div v-if="new_crm_data.type != ''"  class="card card-compact w-full md:w-3/4 bg-base-100 mt-10 shadow-xl">
                       <div class="card-body">
                           <h2 class="card-title place-content-center text-center">Default usernames and passwords</h2>
                           <p>admin@iceburg.ca:admin<br />
                               user@iceburg.ca:user<br />
                               sales@iceburg.ca:sales<br />
                               accounting@iceburg.ca:accounting<br />
                               marketing@iceburg.ca:marketing<br /></p>

                       </div>
                   </div>

                        <div v-if="new_crm_data.type != ''" class="card-actions">
                            <button @click="create" class="mt-10 btn btn-primary">{{create_title}}</button>
                        </div>
                    </div>
                </div>
</div>
        </div>
    </AppLayout>
</template>
<script setup>
console.log('start');
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive } from 'vue';
import { Head, usePage } from '@inertiajs/inertia-vue3';
import Alert from "@/Components/Alert.vue";
import axios from "axios";
const type = ref('');
const file = ref();
const form = ref();
const create_title = ref('Create');

const themes = ref(usePage().props.value.themes);



const new_crm_data = reactive({
    type: ref(''),
    'name': ref(''),
    'theme': ref('light'),
    'description': ref(''),
    'login_email': ref(usePage().props.value.user.email),
    'login_password': ref(''),
    'login_name': ref(usePage().props.value.user.name)
});

const alert = reactive({
    success_alert : ref(0),
    error_alert : ref(0),
    alert_text : ref('')
});

const preview_data = reactive({
    fields: {},
    row: {},
    show: ref(false)
});

const connections = ref( usePage().props.value.connections)

const data = reactive({
    preview: ref(true)
});



const connection = reactive({
   character_set: ref('utf8mb4'),
   collation: ref('utf8mb4_unicode_ci'),
   username: ref(''),
   password: ref(''),
   host: ref(''),
    port: ref('3306'),
    db_name: ref(''),
    save_connection: ref(true),
    connection_id: ref('')

});


const onFileChanged = function($event) {
    preview_data.show=false;
    console.log('in prefi');
    const target = $event.target;
    if (target && target.files) {
        console.log('in prefi2');
        file.value = target.files[0];
    }
}

const process = function(){
    data.preview=false;
    upload();
}

let fieldValueData = {};

const create = async function ()
{
    create_title.value='Create';
    const formData = new FormData();
    if(new_crm_data.type == 'uploadschema'){
        formData.append('input_file', file.value);
        //create_title.value='Creating...  This will take 2 minutes.  Please stay on this page';
    }
    else if(new_crm_data.type == 'connection'){
        formData.append('character_set', connection.character_set);
        formData.append('collation', connection.collation);
        formData.append('username', connection.username);
        formData.append('db_name', connection.db_name);
        formData.append('password', connection.password);
        formData.append('host', connection.host);
        formData.append('port', connection.port);
        formData.append('save_connection', connection.save_connection);


    }
    formData.append('login_name', new_crm_data.login_name);
    formData.append('login_email', new_crm_data.login_email);
    formData.append('login_password', new_crm_data.login_password);

    formData.append('type', new_crm_data.type);
    formData.append('name', new_crm_data.name);
    formData.append('theme', new_crm_data.theme);
    formData.append('description', new_crm_data.description);

    console.log('in create');
    const headers = {'Content-Type': 'multipart/form-data'};
    //axios.post('/data/import', formData, {headers}).then((res) => {
    axios.post('/create', formData, {headers}).then(response => {
        if(response.data)
        {

            if(response.data.status == 1)
            {
                //window.scrollTo(0, 'top');
                alert.alert_text="Your CRM has been queued for creation.  You will be forwarded to the dashboard.";
                alert.success_alert=true;

                setTimeout(() => {
                    window.location = '/dashboard';
                }, 2000);
            }
            else {
                alert.error_alert=1;
                alert.alert_text=response.data.message;
                window.scrollTo(0, top);
                setTimeout(() => {
                    alert.error_alert=null;
                    alert.alert_text='';
                }, 5000);
            }


            //window.location = '/module/' + usePage().props.value.module.name + '/view/' + response.data;
        }
    })
        .catch(error => {
            alert.error_alert=1;
            alert.alert_text=error.response.data.errors;
            window.scrollTo(0, top);
            setTimeout(() => {
                alert.error_alert=null;
                alert.alert_text='';
            }, 5000);
        });

}

async function upload() {

    if (file.value) {
        data.alert_text = '';
        data.success_alert = false;
        data.error_alert = false;
        const formData = new FormData();

        formData.append('input_file', file.value);
        formData.append('module_id', data.module_id);
        formData.append('module_name', data.module_name);
        formData.append('preview', data.preview);
        formData.append('first_row_header', data.first_row_header);
        const headers = {'Content-Type': 'multipart/form-data'};
        axios.post('/data/import', formData, {headers}).then((res) => {

            if (data.preview === false) {
                data.alert_text = "Records have been imported";
                window.scrollTo(0, 'top');
                data.success_alert = true;
                data.preview = false;
                setTimeout(() => {
                    window.location = '/import?success=1&records=';
                }, 2000);

            } else {

                preview_data.fields = res.data.fields;
                preview_data.row = res.data.row;
                preview_data.show = true;
                data.preview = true;
            }

        }).catch(function (error) {

            alert.error_alert=1;
            alert.alert_text=error.response.data.errors;
            window.scrollTo(0, top);
            setTimeout(() => {
                alert.error_alert=null;
                alert.alert_text='';
            }, 5000);
        });

    }
}
</script>
