<template>
    <div class="message">
        <p>なんかサーバーのエラーだよ</p>
        <p>しばらくしたら治ると思うよ</p>
        <div class="photo-detail">
            <button class="button" @click="topMenu">
                トップ画面に戻る
            </button>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        topMenu() {
            this.$router.push("/", () => {});
        }
    }
};
</script>
