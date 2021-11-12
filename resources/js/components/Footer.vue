<template>
    <footer class="footer">
        <button v-if="isLogin" class="button button--link" @click="logout">
            Logout
        </button>

        <RouterLink v-else class="button button--link" to="/login">
            <!-- ここは後で変えるやつ -->
            <button class="button neon__gaming">
                Login / Register
                <span></span><span></span><span></span><span></span>
            </button>
        </RouterLink>
    </footer>
</template>

<script>
import { mapGetters } from "vuex";
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState({
            apiStatus: state => state.auth.apiStatus
        }),
        ...mapGetters({
            isLogin: "auth/check"
        })
    },
    methods: {
        async logout() {
            await this.$store.dispatch("auth/logout");
            if (this.apiStatus) {
                this.$router.push("/login");
                this.$router.go({ path: "/login", force: true });
            }
        }
    }
};
</script>
