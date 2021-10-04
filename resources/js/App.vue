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

import { INTERNAL_SERVER_ERROR,NOT_FOUND } from "./util";

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
            handler(val) {
                if (val === INTERNAL_SERVER_ERROR) {
                    this.$router.push("/500");
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
