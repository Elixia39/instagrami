<template>
    <div
        v-if="photo"
        class="photo-detail photo-detail__B-board"
        :class="{ 'photo-detail--column': fullWidth }"
    >
        <figure
            class="photo-detail__panel photo-detail__image"
            @click="fullWidth = !fullWidth"
        >
            <img :src="photo.public_id" alt="" />
            <!-- <img :src="photo.url" alt="" /> -->
            <figcaption>
                Posted by {{ photo.owner.name }}

                <!-- <img
                    :src="photo.owner.url"
                    :alt="photo.owner.name"
                    width="50"
                    height="50"
                /> -->
                <img
                    :src="photo.owner.public_id"
                    :alt="photo.owner.name"
                    width="50"
                    height="50"
                />
            </figcaption>
        </figure>
        <div class="photo-detail__panel">
            <button
                class="button button--like"
                :class="{ 'button--liked': photo.liked_by_user }"
                title="Like photo"
                @click="onLikeClick"
            >
                <i class="icon ion-md-heart"></i>{{ photo.likes_count }}
            </button>
            <a
                :href="`/photos/${photo.id}/download`"
                class="button button--inverse"
                title="Download photo"
            >
                <i class="icon ion-md-arrow-round-down"></i>Download
            </a>
            <h2 class="photo-detail__title">
                <i class="icon ion-md-chatboxes"></i>Comments
            </h2>
            <ul v-if="photo.comments.length > 0" class="photo-detail__comments">
                <li
                    v-for="comment in photo.comments"
                    :key="comment.content"
                    class="photo-detail__commentItem"
                >
                    <p class="photo-detail__commentBody">
                        {{ comment.content }}
                    </p>
                    <p class="photo-detail__commentInfo">
                        Commented by {{ comment.author.name }}
                    </p>
                </li>
            </ul>
            <p v-else>No comments yet.</p>
            <form v-if="isLogin" @submit.prevent="addComment" class="form">
                <div v-if="commentErrors" class="errors">
                    <ul v-if="commentErrors.content">
                        <li v-for="msg in commentErrors.content" :key="msg">
                            {{ msg }}
                        </li>
                    </ul>
                </div>
                <textarea
                    class="form__item"
                    v-model="commentContent"
                ></textarea>
                <div class="form__button">
                    <button type="submit" class="button button--inverse">
                        Submit comment
                    </button>
                </div>
            </form>
        </div>
        <div class="photo-detail">
            <button class="button" @click="$router.back()">
                写真一覧に戻る
            </button>
        </div>
    </div>
</template>

<script>
import { OK, CREATED, UNPROCESSABLE_ENTITY } from "../util";

export default {
    props: {
        id: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            photo: null,
            commentContent: "",
            commentErrors: null,
            fullWidth: false
        };
    },
    computed: {
        isLogin() {
            return this.$store.getters["auth/check"];
        }
    },
    methods: {
        async fetchPhoto() {
            const response = await axios.get(`/api/photos/${this.id}`);

            if (response.status !== OK) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.photo = response.data;
        },
        async addComment() {
            const response = await axios.post(
                `/api/photos/${this.id}/comments`,
                {
                    content: this.commentContent
                }
            );

            if (response.status === UNPROCESSABLE_ENTITY) {
                this.commentErrors = response.data.errors;
                return false;
            }

            this.commentContent = "";
            // エラーメッセージクリア
            this.commentErrors = null;

            if (response.status !== CREATED) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.photo.comments = [response.data, ...this.photo.comments];
        },
        onLikeClick() {
            if (!this.isLogin) {
                alert("ログインしないといいねできないyo");
                return false;
            }

            if (this.photo.liked_by_user) {
                this.unlike();
            } else {
                this.like();
            }
        },
        async like() {
            const response = await axios.put(`/api/photos/${this.id}/like`);

            if (response.status !== OK) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.photo.likes_count = this.photo.likes_count + 1;
            this.photo.liked_by_user = true;
        },
        async unlike() {
            const response = await axios.delete(`/api/photos/${this.id}/like`);

            if (response.status !== OK) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.photo.likes_count = this.photo.likes_count - 1;
            this.photo.liked_by_user = false;
        }
    },
    watch: {
        $route: {
            async handler() {
                await this.fetchPhoto();
            },
            immediate: true
        }
    }
};
</script>
