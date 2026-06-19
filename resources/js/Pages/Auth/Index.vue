<script setup>
import { Form, Head } from '@inertiajs/vue3';
</script>
<template>
    <Head title="Авторизация"></Head>
    <div class="flex w-screen h-screen justify-center items-center bg-slate-950 text-white">
        <Form :action="route('auth.login')" #default="{errors}" method="post" class="w-100 flex flex-col gap-y-2 rounded-xl shadow-[0_0_20px_rgba(255,255,255,0.2)] shadow-w p-2 bg-slate-900/30">
            <h1 class="text-center text-lg">Авторизация</h1>
            <input type="text" id="login" name="login" placeholder="Логин" class="border border-blue-950 w-full rounded-lg p-1.5 focus:outline-none focus:border-blue-700" autocomplete="none">
            <span v-if="errors.login" class="text-red-500 text-sm">{{ errors.login }}</span>
            <input type="password" id="password" name="password" placeholder="Пароль" class="border border-blue-950 w-full rounded-lg p-1.5 focus:outline-none focus:border-blue-700" autocomplete="none">
            <span v-if="errors.password" class="text-red-500 text-sm">{{ errors.password }}</span>
            <input value="Войти" class="w-full p-1.5 cursor-pointer bg-blue-950 rounded-lg hover:bg-blue-700 transition duration-300" type="submit" name="submit" id="submit">
        </Form>
    </div>
</template>
