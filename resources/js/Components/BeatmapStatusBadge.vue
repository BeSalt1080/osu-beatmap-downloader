<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: String,
});

const statusStyle = computed(() => {
    const statusMap = {
        "ranked": "bg-osu-lime",
        "approved": "bg-osu-lime",
        "qualified": "bg-osu-blue",
        "graveyard": "bg-black text-blue-400",
        "pending": "bg-osu-orange",
        "loved": "bg-osu-pink",
    };

    return statusMap[props.status] || "";
});
</script>

<template>
    <div :class="statusStyle" class="border rounded-xl px-2 uppercase text-osu-gray font-bold text-xs flex items-center w-fit">
        {{ status }}
    </div>
</template>
