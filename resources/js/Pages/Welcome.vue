

<template>
    <title>IceburgCRM</title>
    <link rel="stylesheet" href="dist/styles.css" />
    <nav class="fixed flex justify-between py-6 w-full lg:px-48 md:px-12 px-4 content-center bg-secondary z-10">
        <div class="flex items-center">
            <img src='images/iceburg_resized.png' alt="Logo" class="h-50 mask mask-hexagon-2" /> <span class="ml-2 text-3xl font-bold">Iceburg CRM</span>
        </div>

        <ul class="font-montserrat items-center hidden md:flex">
            <li class="mx-3 ">
                <a class="growing-underline" href="/#howitworks">
                    How it works
                </a>
            </li>
            <li class="growing-underline mx-3">
                <a href="/#features">Features</a>
            </li>
            <li class="growing-underline mx-3">
                <a href="/#pricing">Pricing</a>
            </li>
            <li class="growing-underline mx-3">

            </li>
            <li>
                <a href="https://www.github.com/iceburgcrm/iceburgcrm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                </a>
            </li>
        </ul>
        <div class="font-montserrat hidden md:block">
            <a v-if="$page.props.user" :href="route('dashboard')" class="">
                Home
            </a>
            <span v-else>
                <a role="link" :href="route('login')" class="btn btn-ghost mr-6">Login</a>

                <a v-if="canRegister" :href="route('register')" class="btn">
                    Signup
                </a>
            </span>


        </div>
        <div id="showMenu" class="md:hidden">
            <img src='dist/assets/logos/Menu.svg' alt="Menu icon" />
        </div>
    </nav>

    <div class="relative flex items-top justify-center bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link v-if="$page.props.user" :href="route('dashboard')" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</Link>

            <template v-else>
                <Link :href="route('login')" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</Link>

                <Link v-if="canRegister" :href="route('register', {type : 'free'})" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</Link>
            </template>
        </div>



    </div>
    <div id='mobileNav' class="hidden px-4 py-6 fixed top-0 left-0 h-full w-full bg-secondary z-20 animate-fade-in-down">
        <div id="hideMenu" class="flex justify-end">
            <img src='dist/assets/logos/Cross.svg' alt="" class="h-16 w-16" />
        </div>
        <ul class="font-montserrat flex flex-col mx-8 my-24 items-center text-3xl">
            <li class="my-6" v-if="$page.props.user" :href="route('dashboard')">
                Home
            </li>
            <li class="my-6" v-else>
                <a role="link" :href="route('login')" class="btn btn-ghost mr-6">Login</a>

                <a v-if="canRegister" :href="route('register')" class="btn">
                    Signup
                </a>
            </li><li class="my-6">
                <a @click="e => e.target.classList.toggle('hideMenu')" href="/?1#howitworks">How it works</a>
            </li>
            <li class="my-6">
                <a href="/?1#features">Features</a>
            </li>
            <li class="my-6">
                <a href="/?1#pricing">Pricing</a>
            </li>

            <li class="my-6">

            </li>
            <li>
                <a class="my-6" href="https://www.github.com/iceburgcrm/iceburgcrm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                </a>
            </li>
        </ul>
    </div>

    <!-- Hero -->
    <section
        class="pt-24 md:mt-0 md:h-screen flex flex-col justify-center text-center md:text-left md:flex-row md:justify-between md:items-center lg:px-48 md:px-12 px-4 bg-secondary">
        <div class="md:flex-1 md:mr-10">
            <h1 class="font-pt-serif text-5xl font-bold mb-7">
                Create a CRM out of any

           MySQL database

            </h1>
            <p class="font-pt-serif font-normal mb-7">
            Create your own crm for free.
                Create your own custom CRM from any MySQL schema dump or connect your database
                and we'll import your schema and create a CRM from it.
            </p>
            <div class="font-montserrat">
                <a :href="route('register', {type : 'free'})" class="px-6 py-4 border-2 border-black border-solid rounded-lg">
                    Create My Own CRM
                </a>
            </div>
        </div>
        <div class="flex justify-around md:block mt-8 md:mt-0 md:flex-1">
            <div class="mockup-phone">
                <div class="camera"></div>
                <div class="display">
                    <div  class="artboard artboard-demo phone-1">
                        <img src='images/screenshot1.jpg' alt="iceburgcrm screenshot" />
                        <img src='images/screenshot3.jpg' alt="iceburgcrm screenshot" />
                        <img src='images/screenshot4.jpg' alt="iceburgcrm screenshot" />
                   </div>
                 </div>
            </div>

           </div>
    </section>

    <!-- How it works -->
    <section class="bg-black text-white sectionSize">
        <div>
            <a id="howitworks"></a>
            <h2 class="secondaryTitle bg-underline2 bg-100%">3 Ways to Create a CRM</h2>
        </div>
        <div class="flex flex-col md:flex-row">
            <div class="flex-1 mx-8 flex flex-col items-center my-4">
                <div class="border-2 rounded-full bg-secondary text-black h-12 w-12 flex justify-center items-center mb-3">
                    1
                </div>
                <h3 class="font-montserrat font-medium text-xl mb-2">IceburgCRM</h3>
                <p class="text-center font-montserrat">
                    Create a CRM using the default settings for the open source IceburgCRM framework.
                    <a class="underline" href="https://demo.iceburg.ca" target="_blank">view demo</a>
                </p>
            </div>
            <div class="flex-1 mx-8 flex flex-col items-center my-4">
                <div class="border-2 rounded-full bg-secondary text-black h-12 w-12 flex justify-center items-center mb-3">
                    2
                </div>
                <h3 class="font-montserrat font-medium text-xl mb-2">Upload a MySQL Schema</h3>
                <p class="text-center font-montserrat">
                    Upload your MySQL schema dump file and we will turn it into a CRM.
                </p>
            </div>
            <div class="flex-1 mx-8 flex flex-col items-center my-4">
                <div class="border-2 rounded-full bg-secondary text-black h-12 w-12 flex justify-center items-center mb-3">
                    3
                </div>
                <h3 class="font-montserrat font-medium text-xl mb-2">MySQL Connection</h3>
                <p class="text-center font-montserrat">
                    Connect your MySQL database through an existing connection
                    and we'll read your database structure and create a CRM from it.</p>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="sectionSize bg-secondary">
        <a id="features"></a>
        <div>
            <h2 class="secondaryTitle bg-underline3 bg-100%">Features</h2>
        </div>
        <div class="md:grid md:grid-cols-2 md:grid-rows-2">



            <div class="flex items-start font-montserrat my-6 mr-10">
                <img src='dist/assets/logos/Heart.svg' alt='' class="h-7 mr-4" />
                <div>
                    <h3 class="font-semibold text-2xl">Import / Export</h3>
                    <p>
                        Ability to Import/Export in 6 different formats (XLSX, CSV, TSV, ODS, XLS, HTML)
                    </p>
                </div>
            </div>

            <div class="flex items-start font-montserrat my-6 mr-10">
                <img src='dist/assets/logos/Heart.svg' alt='' class="h-7 mr-4" />
                <div>
                    <h3 class="font-semibold text-2xl">25+ Field Types</h3>
                    <p>
                        25 different input types, Laravel field validation, Maska field masking
                    </p>
                </div>
            </div>

            <div class="flex items-start font-montserrat my-6 mr-10">
                <img src='dist/assets/logos/Heart.svg' alt='' class="h-7 mr-4" />
                <div>
                    <h3 class="font-semibold text-2xl">26 different themes</h3>
                    <p>
                        26 themes with light and dark themes available
                    </p>
                </div>
            </div>

            <div class="flex items-start font-montserrat my-6 mr-10">
                <img src='dist/assets/logos/Heart.svg' alt='' class="h-7 mr-4" />
                <div>
                    <h3 class="font-semibold text-2xl">Logs, Charts, Workflow, etc</h3>
                    <p>
                        Audit logs, Vue3 Charts, Convertable modules, Workflow, Related Fields (related to another module)
                    </p>
                </div>
            </div>

            <div class="flex items-start font-montserrat my-6 mr-10">
                <img src='dist/assets/logos/Heart.svg' alt='' class="h-7 mr-4" />
                <div>
                    <h3 class="font-semibold text-2xl">Relate Unlimited Modules</h3>
                    <p>
                        Unlimited Relationships between any number modules without common fields.
                        Related fields are also available.
                    </p>
                </div>
            </div>

            <div class="flex items-start font-montserrat my-6 mr-10">
                <img src='dist/assets/logos/Heart.svg' alt='' class="h-7 mr-4" />
                <div>
                    <h3 class="font-semibold text-2xl">Metadata Driven</h3>
                    <p>
                        Metadata creations of modules, fields, relationships, subpanels, datalets
                    </p>
                </div>
            </div>



        </div>
    </section>

    <!-- Pricing -->
    <section class="sectionSize bg-secondary py-0">
        <a id="pricing"></a>
        <div>
            <h2 class="secondaryTitle bg-underline4 mb-0 bg-100%">Pricing</h2>
        </div>
        <div class="flex w-full flex-col md:flex-row">

            <div class='flex-1 flex flex-col border-2 mx-6 shadow-2xl relative bg-secondary rounded-2xl py-5 px-8 my-8 md:top-24'>
                <h3 class="font-pt-serif font-normal text-2xl mb-4">
                    Starter Plan
                </h3>
                <div class="font-montserrat font-bold text-2xl mb-4">
                    Free
                </div>

                <div class="flex">
                    <img src='dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Create up to 2 CRMs</p>
                </div>
                <div class="flex">
                    <img src='dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>CRMs expire after 7 days</p>
                </div>
                <div class="flex">
                    <img src='/dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Limited Support</p>
                </div>

                <a  :href="route('register', {type : 'free'})" class=" border-2 border-solid border-black rounded-xl text-lg py-3 mt-4 text-center">
                    Choose
                </a>
            </div>

            <div class='flex-1 flex flex-col border-2 mx-6 shadow-2xl relative bg-secondary rounded-2xl py-5 px-8 my-8 md:top-12'>
                <h3 class="font-pt-serif font-normal text-2xl mb-4">
                    Standard Plan
                </h3>
                <div class="font-montserrat font-bold text-2xl mb-4">
                    $14.99
                    <span class="font-normal text-base"> / month</span>
                </div>

                <div class="flex">
                    <img src='dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Create up to 10 CRMs</p>
                </div>
                <div class="flex">
                    <img src='dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Upto 0.5 gig in storage</p>
                </div>
                <div class="flex">
                    <img src='/dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Support</p>
                </div>

                <a  :href="route('register', {type : 'standard'})" class=" border-2 border-solid border-black rounded-xl text-lg py-3 mt-4 text-center">
                    Choose
                </a>
            </div>

            <div class='flex-1 flex flex-col border-2 mx-6 shadow-2xl relative bg-secondary rounded-2xl py-5 px-8 my-8 md:top-24'>
            <h3 class="font-pt-serif font-normal text-2xl mb-4">
                    Custom Plan
                </h3>
                <div class="font-montserrat font-bold text-2xl mb-4">
                    $xx.xx
                    <span class="font-normal text-base"> / month / year / once</span>
                </div>
                <div class="flex">
                    <img src='/dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Custom Development</p>
                </div>
                <div class="flex">
                    <img src='/dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Additional Storage</p>
                </div>
                <div class="flex">
                    <img src='/dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Own Domain name</p>
                </div>
                <div class="flex">
                    <img src='/dist/assets/logos/CheckedBox.svg' alt="" class="mr-1" />
                    <p>Priority Support</p>
                </div>

                <a href="mailto:sales@iceburgcrm.com" class=" border-2 border-solid border-black rounded-xl text-lg py-3 mt-4 text-center">
                    Contact
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ  -->
    <section class="sectionSize items-start pt-8 md:pt-36 bg-black text-white">
        <div>
            <h2 class="secondaryTitle bg-highlight3 p-10 mb-0 bg-center bg-100%">
                FAQ
            </h2>
        </div>

        <div class="w-full py-4">
            <div class="flex justify-between items-center">
                <div class="font-montserrat font-medium mr-auto">
                    Do you only support only MySQL databases?
                </div>
             </div>
            <div class="font-montserrat text-sm font-extralight pb-8">
                Currently we only support MySQL hosted databases but IceburgCRM can use any number of databases from MongoDB to Oracle when you self host.
            </div>
        </div>
        <hr class="w-full bg-white" />

        <div class="w-full py-4">
            <div class="flex justify-between items-center">
                <div class="font-montserrat font-medium mr-auto">
                    Will my CRM data be sold?
                </div>
             </div>
            <div class="font-montserrat text-sm font-extralight pb-8">
                No, data will be sold, shared or used without your express permission.  Check out our <a class="underline font-bold" href="/privacy">privacy policy</a> page for details.

            </div>
        </div>
        <hr class="w-full bg-white" />

        <div class="w-full py-4">
            <div class="flex justify-between items-center">
                <div class="font-montserrat font-medium mr-auto">
                    What are your Terms of Service?
                </div>
            </div>
            <div class="font-montserrat text-sm font-extralight pb-8">
                Check out our <a class="underline font-bold" href="/tos">terms of service</a> page for details.

            </div>
        </div>
        <hr class="w-full bg-white" />

        <div class="w-full py-4">
            <div class="flex justify-between items-center">
                <div class="font-montserrat font-medium mr-auto">
                    Can I self host IceburgCRM?
                </div>
            </div>
            <div class="font-montserrat text-sm font-extralight pb-8">
                You can self host IceburgCRM by visiting the <a class="underline font-bold" href="https://github.com/iceburgcrm/iceburgcrm">github</a> repo and following the instructions.  If you would like to use the CRM database you created, you can request a copy of your database from support.
            </div>
        </div>
        <hr class="w-full bg-white" />
        <div class="w-full py-4">
            <div class="flex justify-between items-center">
                <div class="font-montserrat font-medium mr-auto">
                    I have more question or a support issue.
                </div>
            </div>
            <div class="font-montserrat text-sm font-extralight pb-8">
                Please email us <a class="font-bold" href="mailto:support@iceburgcrm.com">support@iceburgcrm.com</a> us</div>
        </div>
        <hr class="w-full bg-white" />

    </section>

    <!-- Footer -->
    <section class="bg-black sectionSize">
        <div class="text-white font-montserrat text-sm">
            © 2023 IceburgCRM
        </div>
    </section>
</template>
<script>
window.addEventListener("load", function () {

    document
        .querySelector("#showMenu")
        .addEventListener("click", function (event) {
            document.querySelector("#mobileNav").classList.remove("hidden");
        });

    document
        .querySelector("#hideMenu")
        .addEventListener("click", function (event) {
            document.querySelector("#mobileNav").classList.add("hidden");
        });

    document.querySelectorAll("[toggleElement]").forEach((toggle) => {
        toggle.addEventListener("click", function (event) {
            console.log(toggle);
            const answerElement = toggle.querySelector("[answer]");
            const caretElement = toggle.querySelector("img");
            console.log(answerElement);
            if (answerElement.classList.contains("hidden")) {
                answerElement.classList.remove("hidden");
                caretElement.classList.add("rotate-90");
            } else {
                answerElement.classList.add("hidden");
                caretElement.classList.remove("rotate-90");
            }
        });
    });
});

</script>
<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import {ref} from "vue";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const hide_menu = function(element){

};


</script>
