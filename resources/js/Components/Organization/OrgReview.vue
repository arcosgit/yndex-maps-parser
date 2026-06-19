<script setup>
import { computed } from 'vue';
import BaseRating from '../UI/BaseRating.vue';

const props = defineProps({review: Object});

const reviewStatusClasses = computed(() => {
    const rating = props.review?.rating || 0;
    if (rating >= 4) {
        return 'border-l-emerald-500 from-emerald-500/10';
    }
    if (rating === 3) {
        return 'border-l-amber-500 from-amber-500/10';
    }
    return 'border-l-red-500 from-red-500/10';
})
</script>

<template>
    <div class="flex flex-col gap-y-1 border-l-2 p-2 bg-linear-to-r to-transparent"
        :class="reviewStatusClasses">
        <div class="flex items-center gap-x-1 truncate">
            <h2 class="text-lg font-bold">Автор:</h2>
            <h3 class="text-lg text-violet-500 truncate">
                {{ props.review?.name || 'Аноним' }}
            </h3>
        </div>
        <BaseRating :count="props.review.rating"></BaseRating>
        <div class="italic text-sm text-sky-500">
            {{ props.review?.date }}
        </div>
        <div class="wrap-break-word mt-1">{{ props.review?.review }}</div>
    </div>
</template>

