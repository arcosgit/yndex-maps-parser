<script setup>
import setColorRating from '../../helpers/organization/setColorRating';

const props = defineProps({data: Object});
</script>
<template>
    <div class="flex flex-col gap-y-2">
        <h2 class="text-center text-xl">Основная информация</h2>
        <h3 class="text-lg">Наименование: <span class="text-violet-500 wrap-break-word">{{ props.data?.title }}</span></h3>
        <h3 class="text-lg">
            Рейтинг:
            <span
                :class="setColorRating(props.data?.rating && (props.data?.total_ratings || props.data?.total_reviews) ? props.data?.rating : null).value">
                {{ props.data?.rating && (props.data?.total_ratings || props.data?.total_reviews) ? props.data?.rating : 'недостаточно данных' }}
            </span>
        </h3>
        <h3 class="text-lg">
            Количество оценок:
            <span
                :class="props.data?.total_ratings || props.data?.total_reviews ? 'text-sky-500' : 'text-gray-400'">
                {{ props.data?.total_ratings || props.data?.total_reviews || 'нет оценок' }}
            </span>
        </h3>
        <h3 class="text-lg">
            Количество отзывов:
            <span
                :class="props.data?.total_reviews ? 'text-sky-500' : 'text-gray-400'">
                {{ props.data?.total_reviews || 'нет отзывов' }}
            </span>
        </h3>
    </div>
</template>
