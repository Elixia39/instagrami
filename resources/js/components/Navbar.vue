<template>
    <nav class="navbar2">
        <!-- ヘッダー部分をおしゃれにしたい -->
        <RouterLink class="navbar2__brand" to="/">
            InstagramI
        </RouterLink>
        <div class="navbar2__menu">
            <div v-if="isLogin" class="navbar2__item">
                <button class="button" @click="showForm = !showForm">
                    <i class="icon ion-md-add"></i>
                    Submit a photo
                </button>
            </div>

            <span v-if="isLogin" class="navbar2__item">
                <RouterLink :to="`/userpage/${data.id}/${data.name}`">
                    <!-- <img
                        :src="data.url"
                        alt="プロフィール画像"
                        width="30px"
                        height="30px"
                    /> -->
                    <div v-if="data.public_id">
                        <img
                            :src="data.public_id"
                            alt="プロフィール画像"
                            width="30px"
                            height="30px"
                        />
                    </div>
                    <div v-else>
                        <img
                            src="https://res.cloudinary.com/milia3939/image/upload/v1652763666/profiles/default_fct1ak.png"
                            alt="プロフィール画像"
                            width="30px"
                            height="30px"
                        />
                    </div>
                </RouterLink>
            </span>

            <div v-else class="navbar2__item">
                <RouterLink class="button kiraButton button--link" to="/login">
                    Login / Register
                </RouterLink>
            </div>

            <div
                v-if="isLogin"
                v-on:click="active = !active"
                v-bind:class="{ active: active }"
                class="hamburger navbar2__item"
            >
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <photoForm v-model="showForm" />

        <nav class="globalMenuSp" v-bind:class="{ active }">
            <ul>
                <li>
                    <RouterLink :to="`/userpage/${data.id}/${data.name}`">
                        プロフィールページ
                    </RouterLink>
                </li>
                <li><a href="#">プロフィール画像の変更</a></li>
                <li><a href="#">フォロワー関係(未実装)</a></li>
                <li><a @click="logout">ログアウト</a></li>
                <li></li>
            </ul>
        </nav>
    </nav>
</template>

<script>
import PhotoForm from "./PhotoForm.vue";
import { OK, CREATED, UNPROCESSABLE_ENTITY } from "../util";

export default {
    components: {
        PhotoForm
    },
    data() {
        return {
            showForm: false,
            active: false,
            data: []
        };
    },
    computed: {
        isLogin() {
            return this.$store.getters["auth/check"];
        },
        apiStatus() {
            return this.$store.state.auth.apiStatus;
        }
    },
    methods: {
        async profileImage() {
            const res = await axios.get("/api/user");
            this.data = res.data;
        },
        async logout() {
            await this.$store.dispatch("auth/logout");
            if (this.apiStatus) {
                this.$router.push("/login");
                this.$router.go({ path: "/login", force: true });
            }
        }
    },
    watch: {
        $route: {
            async handler() {
                await this.profileImage();
            },
            immediate: true
        }
    }
};
</script>
