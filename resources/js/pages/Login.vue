<template>
    <div class="container--small">
        <ul class="tab">
            <li
                class="tab__item"
                :class="{ 'tab__item--active': tab === 1 }"
                @click="tab = 1"
            >
                Login
            </li>
            <li
                class="tab__item"
                :class="{ 'tab__item--active': tab === 2 }"
                @click="tab = 2"
            >
                Register
            </li>
        </ul>

        <form class="loginForm" @submit.prevent="login" v-show="tab === 1">
            <div v-if="loginErrors" class="errors">
                <ul v-if="loginErrors.email">
                    <li v-for="msg in loginErrors.email" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="loginErrors.password">
                    <li v-for="msg in loginErrors.password" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
            </div>

            <label for="login-email">Email</label>
            <div class="input-group" style="display:block;">
                <div class="input-group-append" style="display:block;">
                    <div class="input-group-text far fa-envelope">
                        <input
                            type="text"
                            class="loginForm__input"
                            id="login-email"
                            v-model="loginForm.email"
                        />
                    </div>
                </div>
            </div>
            <br />
            <br />

            <label for="login-password">Password</label>
            <div class="input-group" style="display:block;">
                <div class="input-group-append" style="display:block;">
                    <div class="input-group-text">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            class="loginForm__input"
                            id="login-password"
                            v-model="loginForm.password"
                        />
                    </div>
                </div>
            </div>

            <button type="submit" class="button loginForm__button">
                Login
            </button>
        </form>

        <form class="loginForm" @submit.prevent="register" v-show="tab === 2">
            <div v-if="registerErrors" class="errors">
                <ul v-if="registerErrors.name">
                    <li v-for="msg in registerErrors.name" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="registerErrors.email">
                    <li v-for="msg in registerErrors.email" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
                <ul v-if="registerErrors.password">
                    <li v-for="msg in registerErrors.password" :key="msg">
                        {{ msg }}
                    </li>
                </ul>
            </div>

            <label for="username">Name</label>
            <div class="input-group" style="display:block;">
                <div class="input-group-append" style="display:block;">
                    <div class="input-group-text fas fa-user">
                        <input
                            type="text"
                            class="loginForm__input"
                            id="username"
                            v-model="registerForm.name"
                        />
                    </div>
                </div>
            </div>
            <br />

            <label for="email">Email</label>
            <div class="input-group" style="display:block;">
                <div class="input-group-append" style="display:block;">
                    <div class="input-group-text fas fa-envelope">
                        <input
                            type="text"
                            class="loginForm__input"
                            id="email"
                            v-model="registerForm.email"
                        />
                    </div>
                </div>
            </div>
            <br />

            <label for="password">Password</label>
            <div class="input-group" style="display:block;">
                <div class="input-group-append" style="display:block;">
                    <div class="input-group-text">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            class="loginForm__input"
                            id="password"
                            v-model="registerForm.password"
                            autocomplete="new-password"
                        />
                    </div>
                </div>
            </div>
            <br />

            <label for="password-confirmation">Password (confirm) </label>
            <div class="input-group" style="display:block;">
                <div class="input-group-append" style="display:block;">
                    <div class="input-group-text">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            class="loginForm__input"
                            id="password-confirmation"
                            v-model="registerForm.password_confirmation"
                        />
                    </div>
                </div>
            </div>
            <br />

            <button type="submit" class="button loginForm__button">
                register
            </button>
        </form>
    </div>
</template>

<script>
import { mapState } from "vuex";

export default {
    data() {
        return {
            tab: 1,
            loginForm: {
                email: "",
                password: ""
            },
            registerForm: {
                name: "",
                email: "",
                password: "",
                password_confirmation: ""
            }
        };
    },
    mounted() {
        $(function() {
            $("#password").password({
                message: "|дﾟ) ﾐﾀﾅ"
            });
            $("#password-confirmation").password({
                message: "パスワード表示"
            });
            $("#login-password").password({
                message: "パスワード表示"
            });
        });
    },
    computed: {
        ...mapState({
            apiStatus: state => state.auth.apiStatus,
            loginErrors: state => state.auth.loginErrorMessages,
            registerErrors: state => state.auth.registerErrorMessages
        })
    },
    methods: {
        async login() {
            // authストアのloginアクションを呼び出す
            await this.$store.dispatch("auth/login", this.loginForm);
            // トップページに移動する
            if (this.apiStatus) {
                this.$router.push("/");
            }
        },
        async register() {
            // authストアのresigterアクションを呼び出す
            await this.$store.dispatch("auth/register", this.registerForm);
            // トップページに移動する
            if (this.apiStatus) {
                this.$router.push("/");
            }
        },
        clearError() {
            this.$store.commit("auth/setLoginErrorMessages", null);
            this.$store.commit("auth/setRegisterErrorMessages", null);
        }
    },
    created() {
        this.clearError();
    }
};
</script>
