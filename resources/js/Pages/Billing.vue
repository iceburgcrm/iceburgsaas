<template>
    <page :title="title">
        <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold">
                    Hold up! You need an active subscription first.
                </h2>
                <jet-button class="mt-4" @click.native="checkout">
                    Head to the checkout page
                </jet-button>
            </div>
        </div>
    </page>
</template>

<script>

</script>

