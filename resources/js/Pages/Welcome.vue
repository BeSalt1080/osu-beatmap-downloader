<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { ref } from "vue";
import Beatmaps from "../Components/Beatmaps.vue";

const props = defineProps({
    beatmapType: Object
});

console.log(props.beatmapType);

const user = useForm({
    user_id: "",
});

const userData = ref({});

const beatmaps = ref({});

const submitUser = () => {
    axios
        .post(route("get-user"), user.data())
        .then((response) => {
            userData.value = response.data;
            props.beatmapType.forEach(type => {
                if (userData.value[type+"_beatmapset_count"] > 0) {
                    axios.post(route("get-user-beatmaps"), {user_id: userData.value.id, type: type, limit: userData.value[type+"_beatmapset_count"]}).then((response) => {
                        beatmaps.value[type] = response.data;
                    });
                }
            });
        })
        .catch((error) => {
            console.log(error.message);
        });
};
</script>

<template>
    <Head title="Welcome" />
    <div class="p-8 flex justify-center">
        <div class="w-1/2 text-center">
            <h1 class="text-3xl">OsuMusicDownloader</h1>
            <div class="flex">
                <div :class="{ 'w-1/2': !userData.id, 'w-full': userData.id }">
                    <div>Users</div>
                    <div>
                        <div class="flex">
                            <img
                                class="w-24 h-24 rounded-md mr-2"
                                :src="userData.avatar_url"
                                :alt="userData.avatar_url"
                                v-if="userData.id"
                            />
                            <div class="text-left">
                                <span
                                    class="text-2xl font-bold"
                                    v-if="userData.id"
                                >
                                    {{ userData.username }}
                                </span>
                                <div v-if="userData.id">
                                    {{ userData.country.name }}
                                </div>
                                <div>
                                    <form @submit.prevent="submitUser">
                                        <input
                                            class="px-4 border outline-none rounded-md"
                                            :class="{
                                                'bg-slate-300': userData.id,
                                            }"
                                            type="text"
                                            v-model="user.user_id"
                                            :disabled="userData.id"
                                        />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="userData.id">
                        <div>
                            <h3>Ranks</h3>
                        </div>
                        <div>
                            <h3>Recent Activity</h3>
                        </div>
                        <div>
                            <h3>Historical</h3>
                        </div>
                        <div>
                            <h3>Beatmaps</h3>
                            <Beatmaps
                                v-for="type in props.beatmapType" :key="type"
                                :title="type+' Beatmaps'"
                                :count="userData[type+'_beatmapset_count']"
                                :beatmaps="beatmaps[type]"
                            />
                        </div>
                    </div>
                </div>
                <div class="w-1/2 bg-red-500" v-show="!userData.id">
                    Beatmaps
                </div>
            </div>
        </div>
    </div>
</template>
