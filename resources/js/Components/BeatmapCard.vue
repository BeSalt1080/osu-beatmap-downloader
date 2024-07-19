<script setup>
import BeatmapStatusBadge from "./BeatmapStatusBadge.vue";
defineProps({ beatmap: Object });
</script>

<template>
    <div class="border flex">
        <img
            class="w-32 h-32"
            :src="beatmap.cover"
            :alt="beatmap.title"
        />
        <div class="p-4 w-full leading-tight">
            <div class="leading-tight">
                <div class="flex justify-between">
                    <span class="text-2xl font-bold">{{ beatmap.title }}</span>
                </div>
                <div>by {{ beatmap.artist }}</div>
            </div>
                <span class="text-sm leading-relaxed">
                    Mapped by
                <a
                    class="text-pink-400 hover:text-pink-600 underline"
                    :href="'https://osu.ppy.sh/users/' + beatmap.user_id"
                    rel="noopener noreferrer"
                    target="_blank"
                >
                    {{ beatmap.creator }}
                </a>
            </span>
            <BeatmapStatusBadge class="mt-1":status="beatmap.status" />
        </div>
        <div></div>
    </div>
</template>
