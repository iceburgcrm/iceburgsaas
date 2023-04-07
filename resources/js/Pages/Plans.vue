
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="grid grid-flow-col auto-cols-auto place-content-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Plans
                </h2>
                <a href="/create" class="btn btn-primary">Create</a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <Alert :message="alert.alert_text" :is_successful="alert.success_alert" :is_error="alert.error_alert" />

                    <section class="bg-gray-100 py-8">
                        <div class="container mx-auto px-2 pt-4 pb-12 text-gray-800">
                            <h2 class="w-full my-2 text-5xl font-bold leading-tight text-center text-gray-800">
                                Pricing
                            </h2>
                            <div class="w-full mb-4">
                                <div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
                            </div>
                            <div class="flex flex-col sm:flex-row justify-center pt-12 my-12 sm:my-4">
                                <div class="flex flex-col w-5/6 lg:w-1/4 mx-auto lg:mx-0 rounded-none lg:rounded-l-lg bg-white mt-4">
                                    <div class="flex-1 bg-white text-gray-600 rounded-t rounded-b-none overflow-hidden shadow">
                                        <div class="p-8 text-3xl font-bold text-center border-b-4">
                                            Free
                                        </div>
                                        <ul class="w-full text-center text-sm">
                                            <li class="border-b py-4">Create Two upto 2 CRMs</li>
                                            <li class="border-b py-4">CRMs expire after 7 days</li>
                                            <li class="border-b py-4">Limited Support</li>
                                        </ul>
                                    </div>
                                    <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden shadow p-6">
                                        <div class="w-full pt-6 text-3xl text-gray-600 font-bold text-center">
                                            Free
                                        </div>
                                        <div class="flex items-center justify-center">
                                            <a v-if="current_plan !== 1" :href="`/billing/1`" class="mx-auto lg:mx-0 hover:underline gradient text-black font-bold rounded-full my-6 py-4 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out"> Select Plan </a>
                                            <span class="mx-auto lg:mx-0 gradient text-black font-bold rounded-full my-6 py-4 px-8" v-else>Current Plan</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col w-5/6 lg:w-1/3 mx-auto lg:mx-0 rounded-lg bg-white mt-4 sm:-mt-6 shadow-lg z-10">
                                    <div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden shadow">
                                        <div class="w-full p-8 text-3xl font-bold text-center">Basic</div>
                                        <div class="h-1 w-full gradient my-0 py-0 rounded-t"></div>
                                        <ul class="w-full text-center text-base font-bold">
                                            <li class="border-b py-4">Create Two upto 5 CRMs</li>
                                            <li class="border-b py-4">30 Days Free</li>
                                            <li class="border-b py-4">Support</li>
                                        </ul>
                                    </div>
                                    <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden shadow p-6">
                                        <div class="w-full pt-6 text-4xl font-bold text-center">
                                            $14.99
                                        </div>
                                        <div class="flex items-center justify-center">
                                            <a v-if="current_plan !== 2" :href="`/billing/2`" class="mx-auto lg:mx-0 hover:underline gradient text-black font-bold rounded-full my-6 py-4 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out"> Select Plan </a>
                                            <span class="mx-auto lg:mx-0 gradient text-black font-bold rounded-full my-6 py-4 px-8" v-else>Current Plan</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col w-5/6 lg:w-1/4 mx-auto lg:mx-0 rounded-none lg:rounded-l-lg bg-white mt-4">
                                    <div class="flex-1 bg-white text-gray-600 rounded-t rounded-b-none overflow-hidden shadow">
                                        <div  class="p-8 text-3xl font-bold text-center border-b-4">
                                            Pro
                                        </div>
                                        <ul class="w-full text-center text-sm">
                                            <li class="border-b py-4">Create upto 10 CRMs</li>
                                            <li class="border-b py-4">30 Days Free</li>
                                            <li class="border-b py-4">Priority Support</li>
                                        </ul>
                                    </div>
                                    <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden shadow p-6">
                                        <div class="w-full pt-6 text-3xl text-gray-600 font-bold text-center">
                                            $99
                                        </div>
                                        <div class="flex items-center justify-center">
                                            <a v-if="current_plan !== 3" :href="`/billing/3`" class="mx-auto lg:mx-0 hover:underline gradient text-black font-bold rounded-full my-6 py-4 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out"> Select Plan </a>
                                            <span class="mx-auto lg:mx-0 gradient text-black font-bold rounded-full my-6 py-4 px-8" v-else>Current Plan</span>
                                        </div>
                                    </div>
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
import Alert from "@/Components/Alert.vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/inertia-vue3';
import { ref, onMounted, reactive } from 'vue';

const plans = ref(usePage().props.value.plans);
const current_plan = ref(usePage().props.value.plan_id)

const alert = reactive({
    success_alert : ref(0),
    error_alert : ref(0),
    alert_text : ref('')
});

const change_package = function() {

}


</script>

