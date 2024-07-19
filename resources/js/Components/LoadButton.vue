<script setup>
defineProps({
    onClick: Function,
    type: String,
});
</script>
<template>
    <button @click="() => onClick(type)">Load</button>
</template>
