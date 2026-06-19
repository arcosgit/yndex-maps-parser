<script setup>
import { Head, router, useHttp } from '@inertiajs/vue3';
import { reactive, useTemplateRef } from 'vue';
import { route } from 'ziggy-js';
import OrgInfo from '../../Components/Organization/OrgInfo.vue';
import OrgReview from '../../Components/Organization/OrgReview.vue';

const form = useHttp({url: ''});
const parseData = reactive({orgInfo: null, orgReviews: null});
const loadingStates = reactive({orgInfo: false, orgReviews: false});

const reviewsBlock = useTemplateRef('reviewsBlock');

const parse = () => {
    form.clearErrors();
    if(loadingStates.orgInfo) return;
    if(!form.url.startsWith('https://yandex.com/maps/') && !form.url.startsWith('https://yandex.ru/maps/')){
        form.setError('url', 'Некорректный адрес , пример: https://yandex.com/maps/');
        return;
    }
    form.post(route('parse'), {
        onBefore: () => {
            parseData.orgInfo = null;
            parseData.orgReviews = null;
            loadingStates.orgInfo = true;
        },

        onSuccess: (res) => {
            parseData.orgInfo = res;
            if(res.total_reviews && res.total_reviews > 0 && res.id){
                getReviews();
            }
        },

        onFinish: () => loadingStates.orgInfo = false,
    });

}

const getReviews = async (page = 1, scrollTop = false) => {
    if(parseData.orgReviews && parseData.orgReviews.meta.current_page === page || loadingStates.orgReviews) return;
    await useHttp().post(route('org.reviews', parseData.orgInfo.id) + `?page=${page}`,{
        onBefore: () => loadingStates.orgReviews = true,

        onSuccess: (res) => parseData.orgReviews = res,

        onFinish: () => loadingStates.orgReviews = false,
    });
    if(reviewsBlock.value && scrollTop){
        reviewsBlock.value.scrollIntoView({
            'behavior': 'smooth',
            'block': 'start',
        })
    }
}

const logout = () => {
    useHttp().post(route('logout'),{
        onSuccess: () => router.visit(route('index'))
    })
}
</script>
<template>
    <Head title="Главная"></Head>
    <div class="flex flex-col mt-10 gap-y-4 justify-center items-center bg-slate-950 text-white">
        <div class="flex flex-col gap-y-4 w-150">
            <div class="flex items-center justify-between">
                <h1 class="text-center text-xl grow">Парсер организаций с Яндекс Карт</h1>
                <button @click.prevent="logout" class="w-8 h-8 flex justify-center items-center rounded-full border border-red-500 hover:bg-red-500/20 cursor-pointer transition duration-300" title="Выйти из аккаунта">
                    <svg class="w-4 h-4 block rotate-180" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M14 20H6C4.89543 20 4 19.1046 4 18L4 6C4 4.89543 4.89543 4 6 4H14M10 12H21M21 12L18 15M21 12L18 9" stroke="#ff0000" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                </button>
            </div>
            <form @submit.prevent="parse" class="flex flex-col gap-y-2 rounded-xl shadow-[0_0_10px_rgba(255,255,255,0.2)] p-2 bg-slate-900/30">
                <input v-model="form.url" type="text" id="link" name="link" placeholder="Вставьте ссылку на организацию с Яндекс карт" class="border border-blue-950 w-full rounded-lg p-1.5 focus:outline-none focus:border-blue-700" autocomplete="none" required>
                <span v-if="form.errors.url" class="text-red-500 text-sm">{{ form.errors.url }}</span>
                <div class="flex justify-end">
                    <button class="w-40 flex justify-center items-center p-1.5 cursor-pointer bg-blue-950 rounded-lg hover:bg-blue-700 transition duration-300" type="submit">
                        <span v-if="!loadingStates.orgInfo">Получить данные</span>
                        <div v-else class="w-6 h-6 border-2 border-t-blue-500 border-white rounded-full animate-spin"></div>
                    </button>
                </div>
            </form>
        </div>
        <div v-if="parseData.orgInfo" class="w-150 rounded-xl shadow-[0_0_10px_rgba(255,255,255,0.2)] p-2 bg-slate-900/30">
            <OrgInfo :data="parseData.orgInfo"></OrgInfo>
        </div>
        <div ref="reviewsBlock" v-if="parseData.orgInfo" class="w-150 rounded-xl shadow-[0_0_10px_rgba(255,255,255,0.2)] p-2 bg-slate-900/30">
            <div v-if="parseData.orgReviews && parseData.orgReviews.data.length > 0" class="flex flex-col gap-y-4">
                <h2 class="text-center text-xl">Отзывы</h2>
                <div v-for="(review, index) in parseData.orgReviews.data" :key="index">
                    <OrgReview :review="review"></OrgReview>
                </div>
                <div v-if="parseData.orgReviews.meta.last_page >= 2" class="flex items-center justify-center gap-x-2 mb-4">
                    <div v-for="(_, index) in parseData.orgReviews.meta.last_page">
                        <button @click.prevent="getReviews(index + 1, true)"
                        :class="{'border-emerald-300/80 bg-emerald-300/50': index + 1 === parseData.orgReviews.meta.current_page}"
                        class="w-10 h-10 rounded-full border hover:border-emerald-300/50 hover:bg-emerald-300/50 cursor-pointer transition duration-300">
                            {{ index + 1 }}
                        </button>
                    </div>
                </div>
            </div>
            <div v-else-if="loadingStates.orgReviews" class="flex flex-col justify-center items-center gap-y-2">
                <h2 class="text-lg text-emerald-500">Загружаем отзывы...</h2>
                <div class="w-10 h-10 border-2 border-t-blue-500 border-white rounded-full animate-spin"></div>
            </div>
            <div v-else class="text-lg text-amber-300 text-center">Нет отзывов</div>
        </div>
    </div>
</template>
