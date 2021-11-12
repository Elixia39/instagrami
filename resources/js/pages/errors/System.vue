<template>
    <div class="message">
        <p>なんかサーバーのエラーだよ</p>
        <p>しばらくしたら治ると思うよ</p>
        <div class="photo-detail">
            <button class="button" @click="$router.push('/')">
                トップ画面に戻る
            </button>
        </div>
    </div>
</template>
