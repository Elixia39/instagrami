<template>
    <div class="momomo">
        <header>
            <Navbar />
        </header>

        <main>
            <div class="container">
                <Message />
                <RouterView />
            </div>
        </main>

        <footer>
            <Footer />
        </footer>
    </div>
</template>

<script>
import Message from "./components/Message.vue";
import Navbar from "./components/Navbar.vue";
import Footer from "./components/Footer.vue";

import { INTERNAL_SERVER_ERROR, NOT_FOUND, UNAUTHORIZED } from "./util";

export default {
    components: {
        Message,
        Navbar,
        Footer
    },
    computed: {
        errorCode() {
            return this.$store.state.error.code;
        }
    },
    watch: {
        errorCode: {
            async handler(val) {
                if (val === INTERNAL_SERVER_ERROR) {
                    this.$router.push("/500");
                } else if (val === UNAUTHORIZED) {
                    //refresh
                    await axios.get("/api/refresh-token");

                    this.$store.commit("auth/setUser", null);
                    this.$router.push("/login");
                } else if (val === NOT_FOUND) {
                    this.$router.push("/not-found");
                }
            },
            immediate: true
        },
        $route() {
            this.$store.commit("error/setCode", null);
        }
    }
};
</script>
