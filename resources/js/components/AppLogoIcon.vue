<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img
    src="../../assets/logo-icon.svg"
    :class="className"
    class="logo-svg"
    alt="Logo Musae" />
</template>
