
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="grid grid-flow-col auto-cols-auto place-content-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Support
                </h2>

            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <Alert
                        :message="data.alert_text"
                        :is_successful="data.success_alert"
                        :is_error="data.error_alert"
                    />
                    <section>
                        <div class="p-10 bg-base-200">
                            <div><h1 class="text-center text-xl">Send a Message to Support</h1></div>
                            <div class="form-control w-3/4 max-w-xs">
                                <label class="label">
                                    <span class="label-text">Subject</span>
                                </label>
                                <input v-model="subject" type="text" placeholder="Subject" class="input input-bordered w-full max-w-xs" />
                            </div>
                            <div class="grid grid-flow-row grid-row-auto">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Message</span>
                                    </label>
                                    <textarea v-model="message" class="textarea textarea-bordered h-24" placeholder="Bio"></textarea>
                                </div>
                                <div>
                                    <button @click="save_message" class="mt-5 btn btn-primary">Send</button>
                                </div>
                            </div>

                        </div>


                    </section>


                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script setup>
import axios from "axios";
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { ref, onMounted, reactive } from 'vue';
import Alert from "@/Components/Alert.vue";

const file = ref();
const form = ref();

const data = reactive({
    success_alert: ref(''),
    error_alert: ref(''),
    alert_text: ref(''),
});
const message = ref('');
const subject = ref('');


const save_message = function() {
    data.alert_text= '';
    data.success_alert=false;
    data.error_alert=false;
    const formData = new FormData();

    formData.append('message', message.value);
    formData.append('subject', subject.value);

    const headers = { 'Content-Type': 'multipart/form-data'};
    axios.post('/data/support_message', formData, {headers}).then((res) =>
    {
        message.value='';
        subject.value='';
            data.alert_text="Message has been sent";
            window.scrollTo(0, 'top');
            data.success_alert=true;
            data.preview=false;
        data.success_alert=true;
        setTimeout(() => {
            data.success_alert=null;
            data.alert_text='';
        }, 5000)

    }).catch(function (error) {

        data.alert_text="There was an error with your message.  Please try again or email support@iceburgcrm.com";
        data.error_alert=true;
        setTimeout(() => {
            data.error_alert=null;
            data.alert_text='';
        }, 5000)});
}


</script>

