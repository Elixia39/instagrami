<template>
    <div class="photo-list">
        <profile v-model="showForm" />
        <div class="photo-detail2 photo-detail__B-board">
            <div class="photo-detail2__panel">
                <img :src="data.url" alt="" />
            </div>
            <div class="photo-detail2__panel">
                <button class="button" @click="showForm = !showForm">
                    <i class="icon ion-md-add"></i>
                    プロフィール画像の変更
                </button>
            </div>
        </div>

        <!-- <div class="message">
            ここにハンバーガーメニューかタブ機能で
            「自分がいいねしたやつ」「自分が投降したやつ」「ユーザー情報変更」
            とかを作りたい クリックしたら別タブ開いてもいいかも
        </div>
        <div class="message">
            右上のユーザーページに飛ぶところはハンバーガーメニューにして
            「ユーザーページへ」「ログアウト」「写真投稿」を作る
            フッターのログアウトボタンは削除してもいいかも
            {{ data }}
            <img :src="data.url" width="150" height="150" alt="" />
        </div> -->

        <ul class="tab">
            <li
                class="tab__item"
                :class="{ 'tab__item--active': tab === 1 }"
                @click="tab = 1"
            >
                <div class="message">いいねしたやつ</div>
            </li>
            <li
                class="tab__item"
                :class="{ 'tab__item--active': tab === 2 }"
                @click="tab = 2"
            >
                <div class="message">投稿したやつ</div>
            </li>
        </ul>

        <div class="grid" v-show="tab === 1">
            <Photo
                class="grid__item"
                v-for="photo in likeFilter"
                :key="`first-${photo.id}`"
                :item="photo"
            />
        </div>

        <div class="grid" v-show="tab === 2">
            <Photo
                class="grid__item"
                v-for="photo in userFilter"
                :key="`second-${photo.id}`"
                :item="photo"
            />
        </div>

        <infinite-loading @infinite="infiniteHandler2" spinner="bubbles">
            <div slot="no-results">記事がありませんでした。</div>
            <div slot="no-more" class="message">
                全てのコンテンツが表示されました。
            </div>
            <div slot="error">記事の取得中にエラーが発生しました。</div>
        </infinite-loading>
    </div>
</template>

<script>
import { OK } from "../util";
import Photo from "../components/Photo.vue";
import Pagination from "../components/Pagination.vue";
import Profile from "../components/Profile.vue";
import InfiniteLoading from "vue-infinite-loading";

export default {
    props: {
        id: { type: String },
        userid: { type: String }
    },
    components: {
        Photo,
        Pagination,
        InfiniteLoading,
        Profile
    },
    data() {
        return {
            tab: 1,
            photos: [],
            currentPage: 0,
            lastPage: 0,
            list: [],
            page: 1,
            data: [],
            showForm: false
        };
    },
    computed: {
        likeFilter: function() {
            return this.list.filter(function(value) {
                return value.liked_by_user === true;
            });
        },
        userFilter: function() {
            const userid = this.userid;
            return this.list.filter(function(value) {
                return value.owner.name === userid;
            });
        }
    },

    methods: {
        async profileImage() {
            const res = await axios.get("/api/user");
            this.data = res.data;
        },

        async fetchPhotos() {
            const response = await axios.get(
                `/api/userpage/?page=${this.$route.query.page}`
            );

            if (response.status !== OK) {
                this.$store.commit("error/setCode", response.status);
                return false;
            }

            this.photos = response.data.data;
            this.currentPage = response.data.current_page;
            this.lastPage = response.data.last_page;
        },

        async infiniteHandler2($state) {
            const res = await axios.get(
                `/api/userpage/?page=${this.$route.query.page}`,
                {
                    params: {
                        page: this.page,
                        per_page: 1
                    }
                }
            );

            if (res.status !== OK) {
                this.$store.commit("error/setCode", res.status);
                return false;
            }

            setTimeout(() => {
                if (!res.data.data.length) {
                    $state.complete();
                } else if (this.page < res.data.total) {
                    this.page += 1;
                    this.list.push(...res.data.data);
                    $state.loaded();
                } else {
                    $state.complete();
                }
            }, 1500);
        },

        infiniteHandler($state) {
            //web.phpで設定したルーティング
            axios
                .get(`/api/userpage/?page=${this.$route.query.page}`, {
                    params: {
                        page: this.page,
                        per_page: 1
                    }
                })
                .then(({ data }) => {
                    //そのままだと読み込み時にカクつくので1500毎に読み込む
                    setTimeout(() => {
                        if (data.data.length === 0) {
                            $state.complete();
                        } else if (this.page < data.total) {
                            this.page += 1;
                            this.list.push(...data.data);
                            console.log(data);
                            // console.log(this.page);
                            // console.log(this.list);
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                    }, 1500);
                })
                .catch(err => {
                    $state.error();
                });
        }
    },
    watch: {
        $route: {
            async handler() {
                await this.fetchPhotos();
                await this.profileImage();
            },
            immediate: true
        }
    }
};
</script>
