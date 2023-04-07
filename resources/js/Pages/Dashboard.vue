
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="grid grid-flow-col auto-cols-auto place-content-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>

                <a href="/create" class="btn btn-primary btn-xl">Create</a>


            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />

                    <div class="overflow-x-auto w-full">
                        <table class="table w-full">
                            <!-- head -->
                            <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Created (GMT)</th>
                                <th>Status</th>
                                <th>Link</th><th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- row 1 --><tr v-if="crms.length < 1 && plan == false">
                                <td colspan="7">

                                    <div class="alert  border-2 border-error bg-grey shadow-lg">
                                        <div class="">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span>Keep your CRMs alive forever - A monthly subscription is only $14.99 </span>
                                             </div>
                                        <div class="flex-none">

                                            <a  href="/subscribe" class="relative inline-flex items-center justify-start px-6 py-3 overflow-hidden font-medium transition-all bg-red-500 rounded-xl group">
<span class="absolute top-0 right-0 inline-block w-4 h-4 transition-all duration-500 ease-in-out bg-red-700 rounded group-hover:-mr-4 group-hover:-mt-4">
<span class="absolute top-0 right-0 w-5 h-5 rotate-45 translate-x-1/2 -translate-y-1/2 bg-white"></span>
</span>
                                                <span class="absolute bottom-0 left-0 w-full h-full transition-all duration-500 ease-in-out delay-200 -translate-x-full translate-y-full bg-red-600 rounded-2xl group-hover:mb-12 group-hover:translate-x-0"></span>
                                                <span class="relative w-full text-left text-white transition-colors duration-200 ease-in-out group-hover:text-white">Upgrade</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="crms.length < 1">
                                <td colspan="7">

                                    <div class="alert border-2 border-black shadow-lg mt-5 mb-5">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span><br>Let's get started and create your first CRM. You can select from a number of different ways to create a CRM:<br>
                                            <br><span class="grid grid-flow-col place-content-start mr-10"><svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                             IceburgCRM - Predefined standard crm modules<br>
                                            </span><span class="grid grid-flow-col place-content-start mr-10"><svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>Upload your own schema
                                                    </span>
                                                <span class="grid grid-flow-col place-content-start mr-10"><svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>

                                            Connect to an existing database</span><br>
                                                </span>
                                        </div>

                                        <div class="flex-none">

                                            <a class="btn btn-primary mr-5" href="/create">Create</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr v-for="crm in crms">
                                <td>
                                    &nbsp;
                                </td>
                                <td>
                                    <div class="flex items-center space-x-3">
                                        <div>
                                            <div class="font-bold">{{ crm.label}}</div>
                                            <!--<div v-if="crm.type" class="text-sm opacity-50">{{ crm.type.name }}</div>
                                        --></div>
                                    </div>
                                </td>
                                <td>
                                    {{crm.created_at}}
                                    <br/>
                                    <span v-if="plan == false" class="badge badge-ghost badge-sm"><DaysSince :date="crm.created_at" /></span>
                                    <!--<a v-if="plan == false" class="ml-5 btn btn-error text-white btn-xs" href="/plans/">Plans</a>
                                    --><a v-if="plan == false" class="ml-5 btn btn-error text-white btn-xs" href="/subscribe">Upgrade</a>

                                </td>

                                <td>
                                    {{crm.team_id}}
                                   {{crm.status.name}}
                                </td>
                                <td v-if="crm.status.id > 1">
                                    <a class="btn btn-ghost btn-sm font-bold underline" :href="`https://${crm.name.replace('user_','')}.iceburgcrm.com`" target="blank">Visit</a>
                                </td>
                                <td v-else>
                                    Processing
                                </td>

                                <td>
                                    <a @click="delete_crm(crm.id)" class="ml-30 text-error hover:text-black btn-xs underline"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    </a>
                                </td>

                            </tr>

                            </tbody>
                            <!-- foot -->
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Created (GMT)</th>
                                <th>Status</th>
                                <th>Link</th><th>Delete</th>
                            </tr>
                            </tfoot>

                        </table>
                    </div>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import axios from "axios";
import Alert from "@/Components/Alert.vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { ref, onMounted, reactive } from 'vue';
import DaysSince from '@/Components/DaysSince.vue';

const plan = ref(usePage().props.value.plan);
const crms = ref(usePage().props.value.crms);
const action = ref('');
const selected_crms = ref([]);

const alert = reactive({
    success_alert : ref(0),
    error_alert : ref(0),
    alert_text : ref('')
});

const getCrmData = function(){
    axios.get('/data/crms').then(response => {
        console.log('in here');
        crms.value=response.data;
    });
}

const export_crm = function(id){

}
const delete_crm = async function(id){
    let ok = await confirm('Are you sure you want to delete this CRM?');
    if(ok) {
        axios.post('/data/crm/delete/' + id, {id: id}).then(response => {
            let timer;

            if (response.data) {

                if (response.data.status == 1) {
                    window.scrollTo(0, top);
                    alert.alert_text = "The CRM selected has been queued for deletion";
                    alert.success_alert = true;

                    setTimeout(() => {
                        alert.error_alert = null;
                        alert.alert_text = '';
                        window.scrollTo(0, 'top');
                        alert.success_alert = null;
                        alert.preview = false;

                    }, 5000);
                } else {
                    alert.error_alert = 1;
                    alert.alert_text = response.data.message;
                    window.scrollTo(0, top);
                    setTimeout(() => {
                        alert.error_alert = null;
                        alert.alert_text = '';
                    }, 5000);
                }
            }

        })
            .catch(error => {
                let timer;
                alert.error_alert = 1;
                clearTimeout(timer)
                timer = setTimeout(() => {
                    alert.error_alert = 0;
                    alert.alert_text = "error";
                }, 5000);
            });
    }
}

onMounted(() => {

    setInterval(function(){
        getCrmData();
    }, 10000);


});

</script>

