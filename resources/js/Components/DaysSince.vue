<template>
    <div>
        <p v-if="daysSince == 0">Expiring tonight</p>
        <p v-else-if="daysSince <= 1">{{ daysSince }} day left</p>
        <p v-else>{{ daysSince }} days left</p>
    </div>
</template>

<script>
import { computed } from 'vue';

export default {
    props: {
        date: {
            type: Date,
            required: true,
        },
    },
    setup(props) {
        const today = new Date();
        const daysSince = computed(() =>
            7 - Math.floor((today - new Date(props.date)) / (1000 * 60 * 60 * 24))
        );

        return {
            daysSince,
        };
    },
};
</script>
