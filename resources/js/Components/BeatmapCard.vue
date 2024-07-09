<script setup>
defineProps({ beatmap: Object });
</script>

<template>
    <div class="border flex">
        <img
            class="w-32 h-32"
            :src="beatmap.covers.list"
            :alt="beatmap.title"
        />
        <div>
            <div class="flex justify-between">
                <span>{{ beatmap.title }}</span>
                <div class="">{{ beatmap.status }}</div>
            </div>
            <div>
                {{ beatmap.artist }}
            </div>
            <a
                :href="'https://osu.ppy.sh/users/' + beatmap.user_id"
                rel="noopener noreferrer"
                target="_blank"
            >
                {{ beatmap.creator }}
            </a>
        </div>
        <div></div>
    </div>
</template>
