<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";
import { onMounted, ref } from "vue";
import BeatmapContainer from "../Components/BeatmapContainer.vue";
import LoadButton from "@/Components/LoadButton.vue";

const props = defineProps({
    beatmapCategory: Object,
});

const user = useForm({
    user_id: "",
});

const userData = ref({});
const beatmaps = ref({});
const scores = ref({});
const mode = ref("");
const include_fails = ref(0);
const legacy_only = ref(0);

let db;
onMounted(() => {
    const databaseOpenReuqest = window.indexedDB.open("osu", 5);
    databaseOpenReuqest.onsuccess = () => {
        db = databaseOpenReuqest.result;
        console.log("Database initialized successfully");
        if (db.objectStoreNames.contains("users")) {
            console.log("users store exists");
            const transaction = db.transaction("users", "readonly");
            transaction.onerror = (e) => {
                console.log("Transaction failed");
            };
            transaction.onsuccess = (e) => {
                console.log("Transaction successful");
            };
            transaction.objectStore("users").getAll().onsuccess = (e) => {
                userData.value = e.target.result[0];
                user.user_id = userData.value.id;
                mode.value = userData.value.playmode;
                console.log(e.target.result);
                console.log("users store fetched successfully");
            };
        }
    };
    databaseOpenReuqest.onerror = (e) => {
        console.log(e);
    };
    databaseOpenReuqest.onupgradeneeded = (e) => {
        db = e.target.result;
        if (!db.objectStoreNames.contains("users")) {
            db.createObjectStore("users", { keyPath: "id" });
        }
        if (!db.objectStoreNames.contains("beatmaps")) {
            db.createObjectStore("beatmaps", { keyPath: "id" });
        }
        if (!db.objectStoreNames.contains("scores", { keyPath: "id" })) {
            db.createObjectStore("scores");
        }
    };
});

const submitUser = () => {
    axios
        .post(route("get-user"), user.data())
        .then((response) => {
            userData.value = response.data;
            mode.value = response.data.playmode;
            // const transaction = db.transaction("users", "readwrite");
            // const request = transaction.objectStore("users").add(response.data);
            // request.onsuccess(() => {
            //     console.log("success");
            // });
        })
        .catch((error) => {
            console.log(error.message);
        });
};

const resetUser = () => {
    const users_transaction = db.transaction('users','readwrite');
    users_transaction.objectStore('users').clear();
    const beatmaps_transaction = db.transaction('beatmaps','readwrite');
    beatmaps_transaction.objectStore('beatmaps').clear();
    const scores_transaction = db.transaction('scores','readwrite');
    scores_transaction.objectStore('scores').clear();

    userData.value = {};
    beatmaps.value = {};
    scores.value = {};
};

const extractCommonFields = (beatmap) => ({
    id: beatmap.id,
    artist: beatmap.artist,
    author: beatmap.author,
    cover: beatmap.covers.list,
    creator: beatmap.creator,
    status: beatmap.status,
    title: beatmap.title,
    user_id: beatmap.user_id,
});

const pushDataToCategory = (category, data, valueContainer) => {
    valueContainer[category] = valueContainer[category] || [];
    valueContainer[category].push(data);
};

const extractBeatmapData = (response, type) => {
    response.data.forEach((beatmap) => {
        let modes = new Set(beatmap.beatmaps.map((item) => item.mode));
        const beatmapData = {
            ...extractCommonFields(beatmap),
            mode: [...modes],
        };
        pushDataToCategory(type, beatmapData, beatmaps.value);
    });
};

const extractScoreData = (response, category) => {
    response.data.forEach((score) => {
        let beatmap = score.beatmapset;
        const scoreData = {
            ...extractCommonFields(beatmap),
            mode: score.beatmap.mode,
        };
        pushDataToCategory(category, scoreData, scores.value);
    });
};

const fetchBeatmapData = (type, { limit = 0, offset = 0 } = {}) => {
    props.beatmapCategory.forEach((category) => {
        if (userData.value[`${category}_beatmapset_count`] > 0) {
            const data = {
                user_id: userData.value.id,
                type,
                limit: limit || userData.value[`${category}_beatmapset_count`],
                offset,
            };
            axios
                .post(route("get-user-beatmaps"), data)
                .then((response) => extractBeatmapData(response, type));
        } else {
            console.log(category + " is empty");
        }
    });
};

const fetchScoreData = (category, { limit = 0, offset = 0 } = {}) => {
    const data = {
        user_id: userData.value.id,
        type: category,
        mode: mode.value,
        limit,
        offset,
        include_fails: include_fails.value,
        legacy_only: legacy_only.value,
    };
    axios
        .post(route("get-user-scores", data))
        .then((response) => extractScoreData(response, category));
};
</script>

<template>
    <Head title="Home" />
    <div class="p-8 flex justify-center">
        <div class="w-1/2">
            <h1 class="text-3xl text-center">OsuMusicDownloader</h1>
            <div class="flex">
                <div :class="{ 'w-1/2': !userData.id, 'w-full': userData.id }">
                    <div>Users</div>
                    <div class="flex justify-between">
                        <div class="flex">
                            <img
                                class="w-24 h-24 rounded-md mr-2"
                                :src="userData.avatar_url"
                                :alt="userData.avatar_url"
                                v-if="userData.id"
                            />
                            <div class="text-left">
                                <div
                                    class="text-2xl font-bold flex justify-between"
                                    v-if="userData.id"
                                >
                                    <span>{{ userData.username }}</span>
                                    <button @click="resetUser">x</button>
                                </div>
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
                        <div>
                            <div class="flex items-center">
                                <label
                                    ><input
                                        type="radio"
                                        value="osu"
                                        v-model="mode"
                                    />
                                    Osu</label
                                >
                                <label
                                    ><input
                                        type="radio"
                                        value="taiko"
                                        v-model="mode"
                                    />
                                    Taiko</label
                                >
                                <label
                                    ><input
                                        type="radio"
                                        value="fruits"
                                        v-model="mode"
                                    />
                                    Fruits</label
                                >
                                <label
                                    ><input
                                        type="radio"
                                        value="mania"
                                        v-model="mode"
                                    />
                                    Mania</label
                                >
                            </div>
                            <label
                                ><input
                                    type="checkbox"
                                    v-model="include_fails"
                                />
                                Include Fails</label
                            >
                            <label
                                ><input type="checkbox" v-model="legacy_only" />
                                Legacy Only</label
                            >
                        </div>
                    </div>
                    <div v-if="userData.id">
                        <div>
                            <BeatmapContainer
                                title="Best Performances"
                                :beatmaps="scores['best']"
                            />
                            <LoadButton
                                :onClick="() => fetchScoreData('best')"
                            />
                        </div>
                        <div>
                            <BeatmapContainer
                                title="First Place Ranks"
                                :beatmaps="scores['firsts']"
                            />
                            <LoadButton
                                :onClick="() => fetchScoreData('firsts')"
                            />
                        </div>
                        <div>
                            <BeatmapContainer
                                title="Recent Plays (24h)"
                                :beatmaps="scores['recent']"
                            />
                            <LoadButton
                                :onClick="() => fetchScoreData('recent')"
                            />
                        </div>
                        <div>
                            <h3>Beatmaps</h3>
                            <div
                                v-for="type in props.beatmapCategory"
                                :key="type"
                            >
                                <BeatmapContainer
                                    :title="type + ' Beatmaps'"
                                    :count="
                                        userData[type + '_beatmapset_count']
                                    "
                                    :beatmaps="beatmaps[type]"
                                />
                                <LoadButton
                                    :onClick="fetchBeatmapData"
                                    :type="type"
                                />
                            </div>
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
