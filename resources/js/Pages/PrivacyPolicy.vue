<script setup>
import { Head } from '@inertiajs/inertia-vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';

defineProps({
    policy: String,
});
</script>

<template>
    <Head title="Privacy Policy" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="pt-4 bg-gray-100">
            <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <AuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose">
                    <h1 class="mt-5 text-3xl text-center">Privacy Policy</h1>

                    <p>Privacy Policy for IceburgCRM.com</p>

                    <p>At IceburgCRM.com, we respect the privacy of our users and are committed to protecting any personal information that we collect. This privacy policy outlines our practices for collecting, using, and disclosing information when you visit our website and use our services.</p>

                    <p>Collection and Use of Personal Information</p>

                    <p>We collect personal information such as your name, email address, and other contact information when you sign up for our services. We may also collect non-personal information such as your IP address, browser type, and operating system when you visit our website.</p>

                    <p>We use the personal information you provide to us to provide our services, to communicate with you, to improve our services, and to ensure that our website and services are secure. We do not sell or share your personal information with third parties.</p>

                    <p>Cookies and Similar Technologies</p>

                    <p>We may use cookies and similar technologies to collect information about your use of our website and services. Cookies are small files that are stored on your device when you visit our website. We use cookies to remember your preferences, to personalize your experience, and to analyze how our website and services are used.</p>

                    <p>Security of Personal Information</p>

                    <p>We take reasonable measures to protect the personal information we collect from unauthorized access, use, and disclosure. We use industry-standard security measures to protect your personal information, including secure servers, firewalls, and encryption.</p>

                    <p>Accessing and Updating Personal Information</p>

                    <p>You can access and update your personal information by logging into your account on our website. If you have any questions or concerns about the accuracy or completeness of your personal information, you may contact us at privacy@iceburgcrm.com.</p>

                    <p>Changes to Privacy Policy</p>

                    <p>We may update this privacy policy from time to time. We will post the updated policy on our website and notify you of any significant changes.</p>

                    <p>Contact Us</p>

                    <p>If you have any questions or concerns about our privacy policy, please contact us at support@iceburgcrm.com.</p>
                    <br>

                    <a href="/">Home</a><br>
                    <br>
                </div>
            </div>
        </div>
    </div>
</template>
